<?php declare(strict_types=1);

namespace Profile\Sheets;

use Profile\Hand;
use Profile\Sheet;

use function Profile\e;
use function Profile\n;
use function Profile\textWidth;

/** Swimlanes: who works at which stage of a feature. */
final class CrewSheet extends Sheet
{
    private const float COL_X = 250;

    private const float COL_W = 118;

    private const float LANE_Y = 178;

    private const float LANE_H = 38;

    private const float PILL_H = 22;

    public function slug(): string
    {
        return 'crew';
    }

    protected function number(): int
    {
        return 2;
    }

    protected function width(): int
    {
        return 1000;
    }

    protected function height(): int
    {
        return 520;
    }

    protected function label(): string
    {
        $names = array_map(static fn (array $lane): string => $lane[0], $this->config['crew']['lanes']);

        return 'How a feature gets built: swimlanes for ' . implode(', ', $names)
            . ' across ' . implode(', ', array_column($this->config['crew']['stages'], 0)) . '.';
    }

    protected function body(): string
    {
        return '<text x="56" y="76" class="b" font-size="28">How a feature gets built</text>'
            . '<text x="57" y="102" class="m" font-size="16">Who does what, from the first brief to production.</text>'
            . $this->grid() . $this->edges() . $this->steps() . $this->footer();
    }

    private function grid(): string
    {
        $crew = $this->config['crew'];
        $right = self::COL_X + count($crew['stages']) * self::COL_W;
        $bottom = self::LANE_Y + count($crew['lanes']) * self::LANE_H;
        $out = '';
        $sep = '';
        foreach ($crew['stages'] as $i => [$title, $skills]) {
            $cx = n($this->colX($i));
            $out .= "<text x=\"$cx\" y=\"146\" text-anchor=\"middle\" class=\"b\" font-size=\"15\">" . e($title) . '</text>'
                . "<text x=\"$cx\" y=\"163\" text-anchor=\"middle\" class=\"m\" font-size=\"12\">" . e($skills) . '</text>';
            if ($i > 0) {
                $x = n(self::COL_X + $i * self::COL_W);
                $sep .= "M$x 130V" . n($bottom);
            }
        }
        $out .= "<path d=\"$sep\" class=\"fn\" stroke-dasharray=\"2 4\"/>"
            . '<path d="M56 ' . n(self::LANE_Y - 4) . 'H' . n($right) . '" class="ln" stroke-width="1"/>';

        $rows = '';
        foreach (array_keys($crew['lanes']) as $k => $tag) {
            [$name, $role, $human] = $crew['lanes'][$tag];
            $cy = $this->laneY($tag);
            if ($k > 0) {
                $rows .= 'M56 ' . n($cy - self::LANE_H / 2) . 'H' . n($right);
            }
            $out .= '<g' . $this->hook("data-agent=\"$tag\" data-lane=\"$tag\" tabindex=\"0\" role=\"button\" aria-label=\"" . e("$name: open the spec card") . '"') . '>';
            if ($this->site()) {
                $out .= '<rect x="56" y="' . n($cy - self::LANE_H / 2) . '" width="180" height="' . self::LANE_H . '" fill="transparent"/>';
            }
            $out .= $human
                ? '<path d="' . Hand::circle(72, $cy, 13, 5) . '" class="rd"/>'
                    . '<text x="72" y="' . n($cy + 3) . '" text-anchor="middle" class="b" font-size="9" style="fill:' . $this->p->red . '">' . $tag . '</text>'
                : $this->bubble(72, $cy, $tag, 13);
            $out .= '<text x="94" y="' . n($cy - 1) . '" class="b" font-size="14.5">' . e($name) . '</text>'
                . '<text x="94" y="' . n($cy + 13) . '" class="m" font-size="11.5">' . e($role) . '</text></g>';
        }

        return $out . "<path d=\"$rows\" class=\"fn\" opacity=\".6\"/>"
            . '<path d="M56 ' . n($bottom) . 'H' . n($right) . '" class="ln" stroke-width="1"/>';
    }

    private function edges(): string
    {
        $out = '';
        foreach ($this->config['crew']['flow'] as [$from, $to]) {
            [$x1, $y1, $hw1, $hh1] = $this->box($from);
            [$x2, $y2, $hw2, $hh2] = $this->box($to);
            $steps = $this->config['crew']['steps'];
            $lanes = $this->hook("data-lanes=\"{$steps[$from][0]} {$steps[$to][0]}\"");
            $gap = 3;
            if ($y1 === $y2) {
                $d = 'M' . n($x1 + $hw1) . ' ' . n($y1) . 'H' . n($x2 - $hw2 - $gap);
            } elseif ($x1 === $x2) {
                $dir = $y2 > $y1 ? 1 : -1;
                $d = 'M' . n($x1) . ' ' . n($y1 + $dir * $hh1) . 'V' . n($y2 - $dir * ($hh2 + $gap));
            } else {
                $mx = $x2 - self::COL_W / 2;
                $d = 'M' . n($x1 + $hw1) . ' ' . n($y1) . 'H' . n($mx) . 'V' . n($y2) . 'H' . n($x2 - $hw2 - $gap);
            }
            $out .= $this->isHuman($to)
                ? "<path d=\"$d\" class=\"rd\" stroke-width=\"1.6\" marker-end=\"{$this->url('arr')}\"$lanes/>"
                : "<path d=\"$d\" class=\"ln\" stroke-width=\"1.2\" marker-end=\"{$this->url('ar')}\"$lanes/>";
        }

        return $out;
    }

    private function steps(): string
    {
        $out = '';
        $seed = 20;
        foreach ($this->config['crew']['steps'] as $id => [$lane, , $label]) {
            [$x, $y, $hw, $hh] = $this->box($id);
            $out .= '<g' . $this->hook("data-lane=\"$lane\"") . '>';
            $out .= $this->isHuman($id)
                ? '<ellipse cx="' . n($x) . '" cy="' . n($y) . '" rx="' . n($hw) . '" ry="' . n($hh) . '" class="pp"/>'
                    . '<path d="' . Hand::ellipse($x, $y, $hw, $hh, $seed++) . '" class="rd" stroke-width="1.6"/>'
                    . '<text x="' . n($x) . '" y="' . n($y + 5) . '" text-anchor="middle" class="hd" font-size="18">' . e($label) . '</text>'
                : '<rect x="' . n($x - $hw) . '" y="' . n($y - $hh) . '" width="' . n(2 * $hw) . '" height="' . n(2 * $hh) . '" rx="' . n($hh) . '" class="ln pp" stroke-width="1.2"/>'
                    . '<text x="' . n($x) . '" y="' . n($y + 4.5) . '" text-anchor="middle" font-size="12.5">' . e($label) . '</text>';
            $out .= '</g>';
        }

        return $out;
    }

    private function footer(): string
    {
        $signature = '<text x="%s" y="%s" class="hd" font-size="22">S. Mutev</text>';

        return '<text x="56" y="481" font-size="15"><tspan class="b">Also in the toolbox:</tspan> '
            . e($this->config['crew']['toolbox']) . '</text>'
            . $this->titleBlock(676, 452, 44, [
                ['DRAWN BY', 'the crew', 100],
                ['CHECKED BY', $signature, 130, true],
                $this->sheetCell(),
            ]);
    }

    /** @return array{0: float, 1: float, 2: float, 3: float} centre x, centre y, half width, half height */
    private function box(string $step): array
    {
        [$lane, $col, $label] = $this->config['crew']['steps'][$step];
        $x = $this->colX($col);
        $y = $this->laneY($lane);

        return $this->isHuman($step)
            ? [$x, $y, textWidth($label, 18, 0.36) / 2 + 13, 14.0]
            : [$x, $y, textWidth($label, 12.5) / 2 + 10, self::PILL_H / 2];
    }

    private function isHuman(string $step): bool
    {
        $lane = $this->config['crew']['steps'][$step][0];

        return $this->config['crew']['lanes'][$lane][2];
    }

    private function colX(int $i): float
    {
        return self::COL_X + ($i + 0.5) * self::COL_W;
    }

    private function laneY(string $tag): float
    {
        $index = array_search($tag, array_keys($this->config['crew']['lanes']), true);

        return self::LANE_Y + ($index + 0.5) * self::LANE_H;
    }
}
