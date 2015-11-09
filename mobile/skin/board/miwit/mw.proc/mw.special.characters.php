<?php
/**
 * Bechu-Basic Skin for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

include_once("_common.php");

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

function mw_spc_table($spc)
{
    global $g4;
    $cnt = 1;
    $html = "<table border=1><tr>\n";
    if (preg_match("/^utf/i", $g4['charset'])) $split = "/\s/u"; else $split = "/\s/";
    foreach (preg_split($split, $spc) as $char) {
        $char = trim($char);
        if (!$char) continue;
        $html .= "<td>$char</td>\n";
        if ($cnt++%20 == 0) $html .= "</tr><tr>\n";
    }
    $html .= "</tr></table>\n";

    return $html;
}

$spc="、 。 · ‥ … ¨ 〃 ― ∥ ＼ ∼ ‘ ’ “ ” 〔 〕 〈 〉 《 》 「 」 『 』 【 】 ± × ÷ ≠ ≤ ≥ ∞ ∴ ° ′ ″ ℃ Å ￠ ￡ ￥ ♂ ♀ ∠ ⊥ ⌒ ∂ ∇ ≡ ≒ § ※ ☆ ★ ○ ● ◎ ◇ ◆ □ ■ △ ▲ ▽ ▼ → ← ↑ ↓ ↔ 〓 ≪ ≫ √ ∽ ∝ ∵ ∫ ∬ ∈ ∋ ⊆ ⊇ ⊂ ⊃ ∩ ∧ ∨ ￢ ⇒ ⇔ ∀ ∃ ´ ～ ˇ ˘ ˝ ˚ ˙ ¸ ˛ ¡ ¿ ː ∮ ∑ ∏ ¤ ℉ ‰ ◁ ◀ ▷ ▶ ♤ ♠ ♡ ♥ ♧ ♣ ⊙ ◈ ▣ ◐ ◑ ▒ ▤ ▥ ▨ ▧ ▦ ▩ ♨ ☏ ☎ ☜ ☞ ¶ † ‡ ↕ ↗ ↙ ↖ ↘ ♭ ♩ ♪ ♬ ㉿ ㈜ № ㏇ ™ ㏂ ㏘ ℡";

echo mw_spc_table($spc);

