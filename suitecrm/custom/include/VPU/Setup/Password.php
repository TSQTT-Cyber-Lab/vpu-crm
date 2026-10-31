<?php
/**
 * VPU-CRM — sinh mật khẩu AN TOÀN VỚI BỘ GÕ TIẾNG VIỆT (Unikey/EVKey: Telex, VNI, VIQR).
 *
 * Bộ gõ chỉ biến đổi khi gặp NGUYÊN ÂM (a e i o u y + phím dấu: Telex s f r x j, VNI 1–9, VIQR ' ` ? ~ .)
 * hoặc "dd" -> đ, "w" -> ư. Không dùng nguyên âm, d, w (và cả các phím dấu Telex cho chắc) thì gõ bật hay tắt
 * tiếng Việt đều ra đúng mật khẩu. Bỏ thêm ký tự dễ nhầm (l/1, O/0). Ví dụ: Kt7m-Pq3H@48
 */
namespace VPU\Setup;

if (!defined('sugarEntry') && PHP_SAPI !== 'cli') {
    die('Not A Valid Entry Point');
}

final class Password
{
    public const UPPER = 'BCGHKMNPQTVZ';
    public const LOWER = 'bcghkmnpqtvz';
    public const DIGIT = '23456789';

    public static function generate(): string
    {
        $pick = static function (string $set): string {
            return $set[random_int(0, strlen($set) - 1)];
        };
        $all = self::UPPER . self::LOWER . self::DIGIT;
        $group = static function () use ($pick, $all): string {
            // mỗi nhóm 4 ký tự có đủ hoa + thường + số, vị trí ngẫu nhiên
            $c = [$pick(self::UPPER), $pick(self::LOWER), $pick(self::DIGIT), $pick($all)];
            for ($i = count($c) - 1; $i > 0; $i--) {
                $j = random_int(0, $i);
                [$c[$i], $c[$j]] = [$c[$j], $c[$i]];
            }
            return implode('', $c);
        };
        return $group() . '-' . $group() . '@' . $pick(self::DIGIT) . $pick(self::DIGIT);
    }

    /** true nếu gõ chuỗi này với Telex/VNI/VIQR không bị biến đổi (không nguyên âm, không d/w). */
    public static function isImeSafe(string $p): bool
    {
        return !preg_match('/[aeiouyAEIOUYdDwW]/', $p);
    }
}
