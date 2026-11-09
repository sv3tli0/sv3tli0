<?php declare(strict_types=1);

namespace Profile;

/** Red-pencil strokes: deterministic wobble so every build draws the same marks. */
final class Hand
{
    public static function noise(int $seed, int $i): float
    {
        $x = sin($seed * 12.9898 + $i * 78.233) * 43758.5453;

        return $x - floor($x);
    }

    /** @param list<array{0: float, 1: float}> $pts */
    public static function smooth(array $pts): string
    {
        $d = 'M' . n($pts[0][0]) . ' ' . n($pts[0][1]);
        $c = count($pts);
        for ($i = 0; $i < $c - 1; $i++) {
            $p0 = $pts[max($i - 1, 0)];
            $p1 = $pts[$i];
            $p2 = $pts[$i + 1];
            $p3 = $pts[min($i + 2, $c - 1)];
            $d .= 'C' . n($p1[0] + ($p2[0] - $p0[0]) / 6) . ' ' . n($p1[1] + ($p2[1] - $p0[1]) / 6)
                . ' ' . n($p2[0] - ($p3[0] - $p1[0]) / 6) . ' ' . n($p2[1] - ($p3[1] - $p1[1]) / 6)
                . ' ' . n($p2[0]) . ' ' . n($p2[1]);
        }

        return $d;
    }

    /** A quick loop that overshoots its start, the way a pencil circle does. */
    public static function circle(float $cx, float $cy, float $r, int $seed = 1): string
    {
        return self::ellipse($cx, $cy, $r * 1.05, $r * 0.95, $seed);
    }

    public static function ellipse(float $cx, float $cy, float $rx, float $ry, int $seed = 1): string
    {
        $pts = [];
        $n = 16;
        $start = self::noise($seed, 0) * M_PI * 2;
        for ($i = 0; $i <= $n; $i++) {
            $a = $start + (M_PI * 2 + 0.55) * $i / $n;
            $k = 1 + (self::noise($seed, $i + 1) - 0.5) * 0.1 + 0.07 * $i / $n;
            $pts[] = [$cx + cos($a) * $rx * $k, $cy + sin($a) * $ry * $k];
        }

        return self::smooth($pts);
    }

    /** Tick whose lowest point sits at ($x, $y). */
    public static function check(float $x, float $y, float $s): string
    {
        return self::smooth([
            [$x - 0.5 * $s, $y - 0.42 * $s],
            [$x - 0.28 * $s, $y - 0.2 * $s],
            [$x, $y],
            [$x + 0.32 * $s, $y - 0.5 * $s],
            [$x + 0.78 * $s, $y - 1.02 * $s],
        ]);
    }

    public static function line(float $x1, float $y1, float $x2, float $y2, int $seed = 1, float $bow = 0.04): string
    {
        $len = hypot($x2 - $x1, $y2 - $y1);
        $off = $len * $bow * (self::noise($seed, 3) - 0.3);
        $mx = ($x1 + $x2) / 2 - ($y2 - $y1) / $len * $off;
        $my = ($y1 + $y2) / 2 + ($x2 - $x1) / $len * $off;

        return 'M' . n($x1) . ' ' . n($y1) . 'Q' . n($mx) . ' ' . n($my) . ' ' . n($x2) . ' ' . n($y2);
    }

    /** Curved arrow; $bend is the sideways pull as a share of its length. */
    public static function arrow(float $x1, float $y1, float $x2, float $y2, float $bend = 0.25): string
    {
        $len = hypot($x2 - $x1, $y2 - $y1);
        $cx = ($x1 + $x2) / 2 - ($y2 - $y1) * $bend;
        $cy = ($y1 + $y2) / 2 + ($x2 - $x1) * $bend;
        $a = atan2($y2 - $cy, $x2 - $cx);
        $h = min(11.0, $len * 0.3);
        $d = 'M' . n($x1) . ' ' . n($y1) . 'Q' . n($cx) . ' ' . n($cy) . ' ' . n($x2) . ' ' . n($y2);
        foreach ([2.65, -2.55] as $turn) {
            $d .= 'M' . n($x2) . ' ' . n($y2) . 'L' . n($x2 + cos($a + $turn) * $h) . ' ' . n($y2 + sin($a + $turn) * $h);
        }

        return $d;
    }

    /** Bracket over a span, opening downward, with a small peak in the middle. */
    public static function bracket(float $x1, float $x2, float $y, float $h = 8): string
    {
        $m = ($x1 + $x2) / 2;

        return self::smooth([[$x1, $y + $h], [$x1 + 3, $y + 1], [$m - 4, $y], [$m, $y - $h * 0.7]])
            . self::smooth([[$m, $y - $h * 0.7], [$m + 4, $y + 0.5], [$x2 - 3, $y + 1.5], [$x2, $y + $h]]);
    }
}
