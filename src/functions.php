<?php declare(strict_types=1);

namespace Profile;

function n(float $v): string
{
    $s = rtrim(rtrim(number_format($v, 1, '.', ''), '0'), '.');

    return $s === '-0' ? '0' : $s;
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

/** Rough advance width for Barlow Semi Condensed; good enough to size pills and cells. */
function textWidth(string $s, float $size, float $em = 0.47): float
{
    return mb_strlen($s) * $size * $em;
}
