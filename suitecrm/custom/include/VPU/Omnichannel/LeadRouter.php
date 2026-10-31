<?php
/**
 * VPU-CRM — đưa tin nhắn chuẩn hoá vào phễu: tìm/tạo Lead, ghi hội thoại thành Note,
 * cập nhật quan tâm (ngành, học bổng…) và giai đoạn phễu.
 */
namespace VPU\Omnichannel;

use BeanFactory;
use DBManagerFactory;
use TimeDate;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

final class LeadRouter
{
    private const LABEL = [
        'facebook' => 'Messenger', 'instagram' => 'Instagram', 'zalo' => 'Zalo', 'telegram' => 'Telegram',
        'sms' => 'SMS', 'tiktok' => 'TikTok', 'whatsapp' => 'WhatsApp', 'website' => 'Web',
    ];

    /** @return array{lead_id:string,created:bool,duplicate:bool} */
    public function route(array $msg, string $assignedUserId): array
    {
        $cls = ChannelParser::classify($msg['text']);
        $phone = $msg['phone'] ?: $cls['phone'];
        $email = $msg['email'] ?: $cls['email'];

        $lead = $this->find($msg['channel'], $msg['user_id'], $phone, $email);
        $created = false;
        if (!$lead) {
            $lead = BeanFactory::newBean('Leads');
            $lead->last_name = $msg['name'] ?: sprintf('Khách %s %s', self::LABEL[$msg['channel']] ?? $msg['channel'], substr($msg['user_id'], -4));
            $lead->status = 'New';
            $lead->lead_source = 'vpu_' . $msg['channel'];
            $lead->vpu_funnel_stage = 'new';
            $lead->vpu_channel = $msg['channel'];
            $lead->vpu_channel_user_id = $msg['user_id'];
            $lead->assigned_user_id = $assignedUserId;
            $created = true;
        } elseif ($this->isDuplicateMessage($lead->id, $msg['message_id'])) {
            return ['lead_id' => $lead->id, 'created' => false, 'duplicate' => true];
        }

        // Chỉ bổ sung, không ghi đè dữ liệu tư vấn viên đã nhập.
        if ($phone && empty($lead->phone_mobile)) {
            $lead->phone_mobile = $phone;
        }
        if ($email && empty($lead->email1)) {
            $lead->email1 = $email;
        }
        if (empty($lead->vpu_channel_user_id) && $lead->vpu_channel === $msg['channel']) {
            $lead->vpu_channel_user_id = $msg['user_id'];
        }
        $lead->vpu_interest = $this->mergeMulti($lead->vpu_interest ?? '', $cls['interest']);
        $lead->vpu_major = $this->mergeMulti($lead->vpu_major ?? '', array_merge($cls['major'], (array)($msg['extra']['major'] ?? [])));
        if ($cls['scholarship'] && empty($lead->vpu_scholarship)) {
            $lead->vpu_scholarship = $cls['scholarship'];
        }
        $map = ['scholarship' => 'vpu_scholarship', 'intake_year' => 'vpu_intake_year', 'high_school' => 'vpu_high_school',
            'province' => 'vpu_province', 'guardian_name' => 'vpu_guardian_name', 'guardian_phone' => 'vpu_guardian_phone',
            'utm_source' => 'vpu_utm_source', 'utm_campaign' => 'vpu_utm_campaign'];
        foreach ($map as $k => $field) {
            if (isset($msg['extra'][$k]) && empty($lead->$field)) {
                $lead->$field = is_array($msg['extra'][$k]) ? reset($msg['extra'][$k]) : $msg['extra'][$k];
            }
        }
        if (!empty($msg['extra']['consent']) && empty($lead->vpu_consent_marketing)) {
            $lead->vpu_consent_marketing = 1;
            $lead->vpu_consent_at = $this->dbDate($msg['timestamp']);
        }
        $lead->vpu_last_message_at = $this->dbDate($msg['timestamp']);
        $lead->save();

        $note = BeanFactory::newBean('Notes');
        $note->name = mb_substr(sprintf('[%s] %s', self::LABEL[$msg['channel']] ?? $msg['channel'], $msg['text']), 0, 250);
        $note->description = $msg['text'] . ($msg['message_id'] ? "\n\n[mid:" . $msg['message_id'] . ']' : '');
        $note->parent_type = 'Leads';
        $note->parent_id = $lead->id;
        $note->assigned_user_id = $lead->assigned_user_id ?: $assignedUserId;
        $note->save();

        return ['lead_id' => $lead->id, 'created' => $created, 'duplicate' => false];
    }

    private function find(string $channel, string $uid, ?string $phone, ?string $email)
    {
        $db = DBManagerFactory::getInstance();
        $conds = ['(vpu_channel = ' . $db->quoted($channel) . ' AND vpu_channel_user_id = ' . $db->quoted($uid) . ')'];
        if ($phone) {
            $conds[] = 'phone_mobile = ' . $db->quoted($phone);
        }
        $id = $db->getOne('SELECT id FROM leads WHERE deleted = 0 AND (' . implode(' OR ', $conds) . ') ORDER BY date_entered ASC');
        if (!$id && $email) {
            $id = $db->getOne('SELECT r.bean_id FROM email_addr_bean_rel r JOIN email_addresses e ON e.id = r.email_address_id'
                . " WHERE r.deleted = 0 AND e.deleted = 0 AND r.bean_module = 'Leads' AND e.email_address_caps = "
                . $db->quoted(strtoupper($email)));
        }
        return $id ? BeanFactory::getBean('Leads', $id) : null;
    }

    private function isDuplicateMessage(string $leadId, ?string $mid): bool
    {
        if (!$mid) {
            return false;
        }
        $db = DBManagerFactory::getInstance();
        return (bool)$db->getOne("SELECT id FROM notes WHERE deleted = 0 AND parent_type = 'Leads' AND parent_id = "
            . $db->quoted($leadId) . ' AND description LIKE ' . $db->quoted('%[mid:' . addcslashes($mid, '%_') . ']%'));
    }

    private function mergeMulti(string $stored, array $add): string
    {
        $cur = $stored !== '' ? unencodeMultienum($stored) : [];
        $all = array_values(array_unique(array_filter(array_merge((array)$cur, $add))));
        return encodeMultienumValue($all);
    }

    private function dbDate(int $ts): string
    {
        return TimeDate::getInstance()->fromTimestamp($ts)->asDb();
    }
}
