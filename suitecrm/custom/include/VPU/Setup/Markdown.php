<?php
/**
 * VPU-CRM — chuyển Markdown (tập con dùng trong tài liệu VPU) sang HTML an toàn, cho trang Quản trị > Kết nối kênh.
 * Hỗ trợ: tiêu đề (# … ######), đoạn văn, danh sách lồng nhau (-, *, 1.), khối mã ```…```, bảng | … |,
 * trích dẫn >, đường kẻ ---, inline `mã`, **đậm**, *nghiêng*, [liên kết](url).
 * Mọi nội dung được escape HTML trước; liên kết chỉ nhận http(s)://, # và đường dẫn tương đối.
 * Tiêu đề bắt đầu bằng số mục ("## 1. Facebook", "### 0.4 Nơi điền khoá") có id "muc-1", "muc-0-4".
 */
namespace VPU\Setup;

final class Markdown
{
    public static function toHtml(string $md): string
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $md));
        return self::blocks($lines);
    }

    /** @param string[] $lines */
    private static function blocks(array $lines): string
    {
        $out = '';
        $n = count($lines);
        for ($i = 0; $i < $n;) {
            $line = $lines[$i];
            if (trim($line) === '') {
                $i++;
                continue;
            }
            // khối mã
            if (preg_match('/^\s*```/', $line)) {
                $code = [];
                for ($i++; $i < $n && !preg_match('/^\s*```/', $lines[$i]); $i++) {
                    $code[] = $lines[$i];
                }
                $i++;
                $out .= '<pre><code>' . self::esc(implode("\n", self::dedent($code))) . "</code></pre>\n";
                continue;
            }
            // tiêu đề
            if (preg_match('/^(#{1,6})\s+(.+?)\s*#*\s*$/', $line, $m)) {
                $lvl = strlen($m[1]);
                $out .= "<h$lvl id=\"" . self::esc(self::slug($m[2])) . "\">" . self::inline($m[2]) . "</h$lvl>\n";
                $i++;
                continue;
            }
            // đường kẻ
            if (preg_match('/^\s*(-{3,}|\*{3,})\s*$/', $line)) {
                $out .= "<hr>\n";
                $i++;
                continue;
            }
            // bảng: dòng | … | + dòng phân cách |---|
            if (strpos(ltrim($line), '|') === 0 && isset($lines[$i + 1]) && preg_match('/^\s*\|?\s*:?-{2,}/', $lines[$i + 1])) {
                $head = self::cells($line);
                $i += 2;
                $out .= "<table>\n<thead><tr>";
                foreach ($head as $c) {
                    $out .= '<th>' . self::inline($c) . '</th>';
                }
                $out .= "</tr></thead>\n<tbody>\n";
                for (; $i < $n && strpos(ltrim($lines[$i]), '|') === 0; $i++) {
                    $out .= '<tr>';
                    foreach (self::cells($lines[$i]) as $c) {
                        $out .= '<td>' . self::inline($c) . '</td>';
                    }
                    $out .= "</tr>\n";
                }
                $out .= "</tbody>\n</table>\n";
                continue;
            }
            // trích dẫn
            if (preg_match('/^\s*>/', $line)) {
                $q = [];
                for (; $i < $n && preg_match('/^\s*>\s?(.*)$/', $lines[$i], $m); $i++) {
                    $q[] = $m[1];
                }
                $out .= '<blockquote>' . self::blocks($q) . "</blockquote>\n";
                continue;
            }
            // danh sách
            if (self::listMarker($line) !== null) {
                [$html, $i] = self::listBlock($lines, $i);
                $out .= $html;
                continue;
            }
            // đoạn văn
            $p = [];
            for (; $i < $n && trim($lines[$i]) !== '' && !self::startsBlock($lines, $i); $i++) {
                $p[] = trim($lines[$i]);
            }
            $out .= '<p>' . self::inline(implode(' ', $p)) . "</p>\n";
        }
        return $out;
    }

    /** @return array{0:int,1:bool,2:string,3:int}|null [thụt lề, có số thứ tự, nội dung, độ rộng dấu] */
    private static function listMarker(string $line): ?array
    {
        if (preg_match('/^(\s*)([-*]|\d+\.)\s+(.*)$/', $line, $m) && !preg_match('/^\s*(-{3,}|\*{3,})\s*$/', $line)) {
            return [strlen($m[1]), ctype_digit(rtrim($m[2], '.')), $m[3], strlen($m[1]) + strlen($m[2]) + 1];
        }
        return null;
    }

    /** @param string[] $lines @return array{0:string,1:int} */
    private static function listBlock(array $lines, int $i): array
    {
        $first = self::listMarker($lines[$i]);
        $indent = $first[0];
        $ordered = $first[1];
        $tag = $ordered ? 'ol' : 'ul';
        $html = "<$tag>\n";
        $n = count($lines);
        while ($i < $n) {
            $mk = self::listMarker($lines[$i]);
            if ($mk === null || $mk[0] !== $indent || $mk[1] !== $ordered) {
                break;
            }
            $item = [$mk[2]];
            $width = $mk[3];
            for ($i++; $i < $n; $i++) {
                $l = $lines[$i];
                if (trim($l) === '') {
                    // dòng trống: thuộc mục nếu dòng sau vẫn thụt vào
                    $next = $lines[$i + 1] ?? '';
                    if (trim($next) !== '' && strlen($next) - strlen(ltrim($next)) > $indent) {
                        $item[] = '';
                        continue;
                    }
                    break;
                }
                $lead = strlen($l) - strlen(ltrim($l));
                if ($lead <= $indent && self::listMarker($l) !== null) {
                    break;                                          // mục kế tiếp cùng cấp (hoặc cấp ngoài)
                }
                if ($lead <= $indent && self::startsBlock($lines, $i)) {
                    break;
                }
                $item[] = $lead >= $width ? substr($l, $width) : ltrim($l);
            }
            $body = self::blocks($item);
            // mục chỉ có một đoạn văn -> bỏ <p> cho gọn
            if (substr_count($body, '<p>') === 1 && strpos($body, '<p>') === 0) {
                $body = preg_replace('#^<p>(.*)</p>\n?#s', '$1', $body, 1);
            } else {
                $body = preg_replace('#^<p>(.*?)</p>\n#s', '$1', $body, 1);
            }
            $html .= '<li>' . rtrim($body) . "</li>\n";
        }
        return [$html . "</$tag>\n", $i];
    }

    /** @param string[] $lines */
    private static function startsBlock(array $lines, int $i): bool
    {
        $l = $lines[$i];
        return (bool)preg_match('/^\s*(```|#{1,6}\s|>)/', $l)
            || preg_match('/^\s*(-{3,}|\*{3,})\s*$/', $l)
            || self::listMarker($l) !== null
            || (strpos(ltrim($l), '|') === 0 && isset($lines[$i + 1]) && preg_match('/^\s*\|?\s*:?-{2,}/', $lines[$i + 1]));
    }

    /** @return string[] */
    private static function cells(string $row): array
    {
        $row = trim(trim($row), '|');
        $parts = preg_split('/(?<!\\\\)\|/', $row);
        return array_map(static fn($c) => str_replace('\\|', '|', trim($c)), $parts);
    }

    /** @param string[] $lines @return string[] */
    private static function dedent(array $lines): array
    {
        $min = null;
        foreach ($lines as $l) {
            if (trim($l) !== '') {
                $lead = strlen($l) - strlen(ltrim($l));
                $min = $min === null ? $lead : min($min, $lead);
            }
        }
        return $min ? array_map(static fn($l) => substr($l, min($min, strlen($l) - strlen(ltrim($l)))), $lines) : $lines;
    }

    public static function inline(string $text): string
    {
        $codes = [];
        $text = preg_replace_callback('/`([^`]+)`/', static function ($m) use (&$codes) {
            $codes[] = '<code>' . self::esc($m[1]) . '</code>';
            return "\x01" . (count($codes) - 1) . "\x01";
        }, $text);
        $links = [];
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/', static function ($m) use (&$links) {
            $url = $m[2];
            $safe = preg_match('#^(https?://|\#|[A-Za-z0-9_./-]+$)#', $url) && !preg_match('#^\s*javascript:#i', $url);
            $label = self::esc($m[1]);
            $links[] = $safe ? '<a href="' . self::esc($url) . '"' . (preg_match('#^https?://#', $url) ? ' target="_blank" rel="noopener"' : '') . '>' . $label . '</a>' : $label;
            return "\x02" . (count($links) - 1) . "\x02";
        }, $text);
        $text = self::esc($text);
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/s', '<em>$1</em>', $text);
        $text = preg_replace_callback("/\x02(\d+)\x02/", static fn($m) => $links[(int)$m[1]], $text);
        return preg_replace_callback("/\x01(\d+)\x01/", static fn($m) => $codes[(int)$m[1]], $text);
    }

    public static function slug(string $heading): string
    {
        if (preg_match('/^(\d+(?:\.\d+)*)\.?\s/', $heading, $m)) {
            return 'muc-' . str_replace('.', '-', $m[1]);
        }
        $s = strtolower((string)preg_replace('/[^A-Za-z0-9]+/', '-', self::ascii($heading)));
        return 'muc-' . (trim($s, '-') ?: 'x');
    }

    private static function ascii(string $s): string
    {
        $s = strtr($s, ['đ' => 'd', 'Đ' => 'D']);
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        return $t === false ? $s : $t;
    }

    private static function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}
