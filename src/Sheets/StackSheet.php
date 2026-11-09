<?php declare(strict_types=1);

namespace Profile\Sheets;

use Profile\Hand;
use Profile\Sheet;

use function Profile\e;
use function Profile\n;

/** The stack as a building section, with the agent crew as the tower crane. */
final class StackSheet extends Sheet
{
    private const float GROUND = 460;

    private const float FLOOR_H = 52;

    private const float LEFT = 340;

    private const float RIGHT = 600;

    private const float FOOTING = 48;

    public function slug(): string
    {
        return 'stack';
    }

    protected function number(): int
    {
        return 3;
    }

    protected function width(): int
    {
        return 1000;
    }

    protected function height(): int
    {
        return 580;
    }

    protected function label(): string
    {
        $layers = array_map(static fn (array $l): string => $l[0], [...$this->config['stack'], $this->config['foundation']]);

        return 'Stack as a building section, top to bottom: ' . implode('; ', $layers)
            . '. Load-bearing walls: ' . $this->config['walls'][0]
            . '. The crane building it: ' . implode(', ', $this->config['crane']) . '.';
    }

    protected function defs(): string
    {
        $ink = $this->p->ink;
        $line = static fn (string $id, int $size, int $angle, string $d, float $o = 0.55): string => "<pattern id=\"$id\" width=\"$size\" height=\"$size\" patternUnits=\"userSpaceOnUse\" patternTransform=\"rotate($angle)\"><path d=\"$d\" stroke=\"$ink\" stroke-width=\".9\" opacity=\"$o\" fill=\"none\"/></pattern>";

        return $line($this->id('h-steel'), 5, 45, 'M0 0V5')
            . $line($this->id('h-cross'), 8, 45, 'M0 0V8M0 0H8', 0.45)
            . $line($this->id('h-lines'), 6, 0, 'M0 3H6', 0.45)
            . $line($this->id('h-slant'), 10, -45, 'M0 0V10', 0.5)
            . "<pattern id=\"{$this->id('h-dots')}\" width=\"8\" height=\"8\" patternUnits=\"userSpaceOnUse\"><circle cx=\"2\" cy=\"2\" r=\".9\" fill=\"$ink\" opacity=\".6\"/><circle cx=\"6\" cy=\"6\" r=\".9\" fill=\"$ink\" opacity=\".6\"/></pattern>"
            . "<pattern id=\"{$this->id('h-concrete')}\" width=\"36\" height=\"30\" patternUnits=\"userSpaceOnUse\"><g fill=\"none\" stroke=\"$ink\" stroke-width=\".8\" opacity=\".6\">"
            . '<path d="M4 6l4 -3l1 5z"/><path d="M22 4l5 2l-4 3z"/><path d="M14 18l4 3l-5 1z"/><path d="M28 22l3 -4l2 5z"/><path d="M5 24l3 2l-4 1z"/></g>'
            . "<g fill=\"$ink\" opacity=\".55\"><circle cx=\"16\" cy=\"8\" r=\".8\"/><circle cx=\"31\" cy=\"12\" r=\".7\"/><circle cx=\"9\" cy=\"15\" r=\".7\"/><circle cx=\"23\" cy=\"27\" r=\".8\"/><circle cx=\"2\" cy=\"12\" r=\".6\"/></g></pattern>"
            . "<pattern id=\"{$this->id('h-earth')}\" width=\"22\" height=\"20\" patternUnits=\"userSpaceOnUse\"><path d=\"M2 9l6 -6M5 10l6 -6M8 11l6 -6M13 19l6 -6\" stroke=\"$ink\" stroke-width=\".8\" opacity=\".5\"/></pattern>";
    }

    protected function body(): string
    {
        return '<text x="56" y="76" class="b" font-size="28">What it&#8217;s built on</text>'
            . '<text x="57" y="102" class="m" font-size="16">A section through the stack. The crane is the crew.</text>'
            . $this->ground() . $this->building() . $this->dimension() . $this->crane() . $this->callouts() . $this->block();
    }

    private function top(): float
    {
        return self::GROUND - count($this->config['stack']) * self::FLOOR_H;
    }

    private function ground(): string
    {
        $g = self::GROUND;

        return '<rect x="40" y="' . $g . '" width="920" height="22" fill="' . $this->url('h-earth') . '"/>'
            . '<g' . $this->hook('data-layer="' . count($this->config['stack']) . '"') . '><rect x="' . (self::LEFT - 18) . '" y="' . $g . '" width="' . (self::RIGHT - self::LEFT + 36) . '" height="' . self::FOOTING . '" class="pp"/>'
            . '<rect x="' . (self::LEFT - 18) . '" y="' . $g . '" width="' . (self::RIGHT - self::LEFT + 36) . '" height="' . self::FOOTING . '" class="ln" stroke-width="1.4" style="fill:' . $this->url('h-concrete') . '"/></g>'
            . "<path d=\"M40 {$g}H960\" class=\"ln\" stroke-width=\"1.8\"/>";
    }

    private function building(): string
    {
        $l = self::LEFT;
        $r = self::RIGHT;
        $top = $this->top();
        $out = '';
        foreach ($this->config['stack'] as $i => [, , $hatch]) {
            $y = $top + $i * self::FLOOR_H;
            $out .= '<g' . $this->hook("data-layer=\"$i\"") . '>'
                . "<rect x=\"$l\" y=\"" . n($y) . '" width="' . ($r - $l) . '" height="' . self::FLOOR_H . "\" class=\"pp\"/>"
                . "<rect x=\"$l\" y=\"" . n($y) . '" width="' . ($r - $l) . '" height="' . self::FLOOR_H . "\" fill=\"{$this->url("h-$hatch")}\"/></g>";
        }
        $slabs = '';
        for ($i = 0; $i <= count($this->config['stack']); $i++) {
            $y = n($top + $i * self::FLOOR_H);
            $slabs .= 'M' . ($l - 6) . " {$y}H" . ($r + 6);
        }
        $ink = $this->p->ink;

        return $out
            . "<path d=\"$slabs\" stroke=\"$ink\" stroke-width=\"3.4\"/>"
            . '<g' . $this->hook('data-layer="walls"') . '><rect x="' . ($l - 6) . '" y="' . n($top - 8) . '" width="6" height="' . n(self::GROUND - $top + 8) . "\" fill=\"$ink\"/>"
            . "<rect x=\"$r\" y=\"" . n($top - 8) . '" width="6" height="' . n(self::GROUND - $top + 8) . "\" fill=\"$ink\"/></g>";
    }

    /** Height of the whole building, foundation included, measured in years. */
    private function dimension(): string
    {
        $x = 312;
        $y1 = $this->top() - 8;
        $y2 = self::GROUND + self::FOOTING;
        $years = $this->config['years'];
        $tick = static fn (float $y): string => 'M' . ($x - 5) . ' ' . n($y + 5) . 'L' . ($x + 5) . ' ' . n($y - 5);

        return '<path d="M' . ($x - 6) . ' ' . n($y1) . 'H' . (self::LEFT - 10) . 'M' . ($x - 6) . ' ' . n($y2) . 'H' . (self::LEFT - 20) . "M$x " . n($y1) . 'V' . n($y2) . $tick($y1) . $tick($y2) . '" class="ln" stroke-width="1"/>'
            . '<text transform="translate(' . ($x - 7) . ' ' . n(($y1 + $y2) / 2) . ') rotate(-90)" text-anchor="middle" font-size="13">' . $years . ' years, since ' . $this->config['since'] . '</text>';
    }

    private function crane(): string
    {
        $mast = '';
        for ($y = 160, $k = 0; $y < self::GROUND - 8; $y += 16, $k++) {
            $mast .= ($k === 0 ? 'M222 ' : 'L' . ($k % 2 ? 238 : 222) . ' ') . $y;
        }
        $jib = '';
        for ($x = 238, $k = 0; $x <= 580; $x += 14, $k++) {
            $jib .= ($k === 0 ? 'M' : 'L') . $x . ' ' . ($k % 2 ? 148 : 160);
        }
        $counter = '';
        for ($x = 168, $k = 0; $x <= 222; $x += 13.5, $k++) {
            $counter .= ($k === 0 ? 'M' : 'L') . n($x) . ' ' . ($k % 2 ? 148 : 160);
        }
        $red = $this->p->red;

        return '<g' . $this->hook('data-agent="crane" tabindex="0" role="button" aria-label="The crane: open the toolbox"') . '>'
            . '<g class="ln" stroke-width="1.2">'
            . '<path d="M222 160V452M238 160V452M238 148H580M238 160H580M168 148H222M168 160H222M222 148L230 116L238 148" stroke-width="1.6"/>'
            . "<path d=\"$mast\"/><path d=\"$jib\"/><path d=\"$counter\"/>"
            . '<path d="M230 116L580 148M230 116L168 148" stroke-width=".9"/>'
            . '<rect x="172" y="160" width="24" height="24" fill="' . $this->url('h-cross') . '"/>'
            . '<rect x="212" y="452" width="36" height="8" class="ln pp"/>'
            . '<rect x="472" y="160" width="16" height="6" class="ln pp"/>'
            . '<path d="M480 166V176M480 176q0 5 -4 4" stroke-width="1"/>'
            . '<rect x="456" y="180" width="48" height="14" class="ln pp"/><path d="M456 180L504 194M504 180L456 194" stroke-width=".8"/>'
            . '</g>'
            . "<rect x=\"200\" y=\"161\" width=\"21\" height=\"16\" fill=\"none\" stroke=\"$red\" stroke-width=\"1.8\"/>"
            . '<path d="' . Hand::arrow(150, 214, 203, 182, 0.22) . '" class="rd" stroke-width="1.6"/>'
            . '<text x="56" y="226" class="hd" font-size="19">me, at the controls</text>'
            . '<text x="56" y="300" class="b" font-size="15">The crane</text>'
            . $this->lines(56, 322, 13, 20, $this->config['crane'])
            . '<path d="M200 316H222" class="ln" stroke-width="1"/><circle cx="222" cy="316" r="2" fill="' . $this->p->ink . '"/></g>';
    }

    private function callouts(): string
    {
        $layers = [...$this->config['stack'], $this->config['foundation']];
        $out = '';
        foreach ($layers as $i => [$what, $why]) {
            $mid = $i < count($this->config['stack'])
                ? $this->top() + ($i + 0.5) * self::FLOOR_H
                : self::GROUND + self::FOOTING / 2;
            $from = $i < count($this->config['stack']) ? self::RIGHT - 22 : self::RIGHT + 6;
            $out .= '<g' . $this->hook("data-layer=\"$i\"") . '><circle cx="' . $from . '" cy="' . n($mid) . '" r="2.6" fill="' . $this->p->ink . '"/>'
                . '<path d="M' . $from . ' ' . n($mid) . 'H652" class="ln" stroke-width="1"/>'
                . '<text x="660" y="' . n($mid - 1) . '" class="b" font-size="15.5">' . e($what) . '</text>'
                . '<text x="660" y="' . n($mid + 15) . '" class="m" font-size="12.5">' . e($why) . '</text></g>';
        }

        [$what, $why] = $this->config['walls'];
        $wall = self::RIGHT + 3;
        $y = $this->top() + 6;

        return $out . '<g' . $this->hook('data-layer="walls"') . '><circle cx="' . $wall . '" cy="' . n($y) . '" r="2.6" fill="' . $this->p->paper . '" stroke="' . $this->p->ink . '" stroke-width="1.2"/>'
            . '<path d="M' . ($wall + 2) . ' ' . n($y - 2) . 'L638 ' . n($y - 40) . 'H652" class="ln" stroke-width="1"/>'
            . '<text x="660" y="' . n($y - 41) . '" class="b" font-size="15.5">' . e($what) . '</text>'
            . '<text x="660" y="' . n($y - 25) . '" class="m" font-size="12.5">' . e($why) . '</text></g>';
    }

    /** @param list<string> $lines */
    private function lines(float $x, float $y, float $size, float $leading, array $lines): string
    {
        $out = '';
        foreach ($lines as $i => $line) {
            $out .= '<text x="' . n($x) . '" y="' . n($y + $i * $leading) . '" font-size="' . n($size) . '">' . e($line) . '</text>';
        }

        return $out;
    }

    private function block(): string
    {
        return $this->titleBlock(676, 512, 44, [
            ['SECTION', 'A-A, the stack', 130],
            ['DRAWN BY', 'the crew', 100],
            $this->sheetCell(),
        ]);
    }
}
