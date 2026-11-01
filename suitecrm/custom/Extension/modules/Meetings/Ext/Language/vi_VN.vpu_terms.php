<?php
// VPU-CRM: "Hội họp" -> "Lịch hẹn" (Meetings là lịch hẹn tư vấn với thí sinh/phụ huynh). Xem TermRelabel.php.
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once 'custom/include/VPU/Language/TermRelabel.php';
$mod_strings = \VPU\Language\TermRelabel::replaceText($mod_strings, ['Hội họp' => 'Lịch hẹn', 'hội họp' => 'lịch hẹn']);
