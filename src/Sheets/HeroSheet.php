<?php declare(strict_types=1);

namespace Profile\Sheets;

use DateTimeImmutable;
use Profile\Fonts;
use Profile\Hand;
use Profile\Palette;
use Profile\Sheet;

use function Profile\e;

final class HeroSheet extends Sheet
{
    private const float LOOP = 6.0;

    private const float LOOP_START = 2.2;

    /** @param array<string, mixed> $config */
    public function __construct(Palette $p, Fonts $fonts, array $config, private readonly DateTimeImmutable $today)
    {
        parent::__construct($p, $fonts, $config);
    }

    public function slug(): string
    {
        return 'hero';
    }

    protected function number(): int
    {
        return 1;
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
        return "{$this->config['name']}, {$this->config['role']}. I brief, Explore and Plan prepare the work, "
            . 'lara, fronty and designer build in parallel, code-reviewer checks it, I give the final review, then it ships.';
    }

    protected function css(): string
    {
        return '.w{stroke-dasharray:1;animation:draw 1s cubic-bezier(.6,0,.3,1) backwards}'
            . '.nd{animation:fade .6s ease-out backwards}'
            . ".pk{fill:{$this->p->ink}}";
    }

    protected function body(): string
    {
        return $this->intro() . $this->legend() . $this->schematic() . $this->ruleOfTheDay() . $this->block();
    }

    private function intro(): string
    {
        $c = $this->config;
        $out = '<text x="56" y="116" class="b" font-size="54">' . e($c['name']) . '</text>'
            . '<text x="58" y="150" class="m" font-size="21">' . e($c['role']) . '</text>';
        foreach ($c['lead'] as $i => $line) {
            $out .= '<text x="58" y="' . (200 + $i * 27) . '" font-size="18">' . e($line) . '</text>';
        }

        return $out;
    }

    private function legend(): string
    {
        return '<rect x="56" y="292" width="350" height="100" class="ln pp" stroke-width="1"/>'
            . '<text x="68" y="309" class="lb">LEGEND</text>'
            . $this->bubble(80, 337, 'LA', 12)
            . '<text x="102" y="342" font-size="15">' . e($this->p->inkName) . ': built by the crew</text>'
            . '<path d="' . Hand::check(80, 380, 15) . '" class="rd"/>'
            . '<text x="102" y="376" font-size="15">Red pencil: my calls and the final review</text>';
    }

    private function schematic(): string
    {
        $wires = [
            'wa' => ['M547 120H591', 0.1],
            'wb' => ['M637 120H681', 0.35],
            'wc1' => ['M727 120H750V178H766', 0.6],
            'wc2' => ['M727 120H750V250H766', 0.6],
            'wc3' => ['M727 120H750V322H766', 0.6],
            'wd1' => ['M812 178H880V250H901', 1.0],
            'wd2' => ['M812 250H901', 1.0],
            'wd3' => ['M812 322H880V250H901', 1.0],
            'we' => ['M925 272V326', 1.4],
            'wf' => ['M925 363V386', 1.65],
        ];
        $out = '';
        foreach ($wires as $id => [$d, $delay]) {
            $out .= "<path id=\"{$this->id($id)}\" d=\"$d\" pathLength=\"1\" class=\"ln w\" marker-end=\"{$this->url('ar')}\" style=\"animation-delay:{$delay}s\"/>";
        }

        $out .= $this->node(0.0, '<path d="' . Hand::circle(525, 120, 22, 3) . '" class="rd" pathLength="1"/>'
            . '<text x="525" y="126" text-anchor="middle" class="b" font-size="15" style="fill:' . $this->p->red . '">SM</text>'
            . '<text x="525" y="166" text-anchor="middle" class="hd" font-size="22">me</text>', 'SM');
        $out .= $this->node(0.3, $this->bubble(615, 120, 'EX') . '<text x="615" y="160" text-anchor="middle" font-size="13.5">Explore</text>', 'EX');
        $out .= $this->node(0.55, $this->bubble(705, 120, 'PL') . '<text x="705" y="160" text-anchor="middle" font-size="13.5">Plan</text>', 'PL');
        foreach ([['LA', 178, 'lara'], ['FR', 250, 'fronty'], ['DS', 322, 'designer']] as $k => [$tag, $y, $name]) {
            $out .= $this->node(0.95 + $k * 0.08, $this->bubble(790, $y, $tag)
                . '<text x="814" y="' . ($y - 24) . '" font-size="13.5">' . $name . '</text>', $tag);
        }
        $out .= $this->node(1.35, $this->bubble(925, 250, 'CR') . '<text x="968" y="219" text-anchor="end" font-size="13.5">code-reviewer</text>', 'CR');
        $out .= '<g' . $this->hook('data-node="ship"') . '><rect x="880" y="388" width="90" height="25" rx="12.5" class="ln pp nd" style="animation-delay:1.85s"/>'
            . '<rect id="' . $this->id('shipflash') . '" x="880" y="388" width="90" height="25" rx="12.5" fill="' . $this->p->ink . '" opacity="0"/>'
            . '<text x="925" y="405.5" text-anchor="middle" font-size="14" class="nd" style="animation-delay:1.85s">production</text></g>';

        $out .= $this->redMarks();

        return $out . $this->packets();
    }

    private function redMarks(): string
    {
        $mark = static fn (string $d, float $delay, string $extra = ''): string => "<path d=\"$d\" class=\"rd w\" pathLength=\"1\" style=\"animation-delay:{$delay}s\"$extra/>";

        return $mark(Hand::check(728, 97, 15), 0.9)
            . '<text x="742" y="92" class="hd nd" font-size="19" style="animation-delay:1.1s">ok</text>'
            . '<circle cx="925" cy="345" r="17" class="pp"/>'
            . $mark(Hand::circle(925, 345, 17, 11), 1.5)
            . $mark(Hand::check(922, 353, 16), 1.75, ' id="' . $this->id('gate') . '"' . $this->hook('data-node="gate"'))
            . '<path d="' . Hand::arrow(906, 356, 757, 328, -0.32) . '" class="rd" stroke-dasharray="4 5" style="animation:fade .6s 2s backwards"/>'
            . '<text x="872" y="401" text-anchor="end" class="hd nd" font-size="19" style="animation-delay:2.1s">my review: not yet? back to the crew</text>';
    }

    /** Work items travelling the wires on a loop; parallel tracks move together. */
    private function packets(): string
    {
        $runs = [
            ['wa', 0.01, 0.07], ['wb', 0.08, 0.15],
            ['wc1', 0.17, 0.3], ['wc2', 0.17, 0.3], ['wc3', 0.17, 0.3],
            ['wd1', 0.34, 0.46], ['wd2', 0.34, 0.46], ['wd3', 0.34, 0.46],
            ['we', 0.49, 0.55], ['wf', 0.64, 0.7],
        ];
        $timing = 'dur="' . self::LOOP . 's" begin="' . self::LOOP_START . 's" repeatCount="indefinite"';
        $out = '';
        foreach ($runs as [$wire, $from, $to]) {
            $out .= '<circle r="3.8" class="pk" opacity="0">'
                . "<animateMotion $timing calcMode=\"linear\" keyPoints=\"0;0;1;1\" keyTimes=\"0;$from;$to;1\"><mpath xlink:href=\"#{$this->id($wire)}\"/></animateMotion>"
                . "<animate attributeName=\"opacity\" $timing calcMode=\"discrete\" values=\"0;1;0\" keyTimes=\"0;$from;$to\"/>"
                . '</circle>';
        }

        return $out
            . "<animate xlink:href=\"#{$this->id('gate')}\" attributeName=\"stroke-width\" $timing values=\"2;2;4;2;2\" keyTimes=\"0;.55;.58;.62;1\"/>"
            . "<animate xlink:href=\"#{$this->id('shipflash')}\" attributeName=\"opacity\" $timing values=\"0;0;.35;0;0\" keyTimes=\"0;.7;.73;.82;1\"/>";
    }

    private function ruleOfTheDay(): string
    {
        $rules = $this->config['rules'];
        $rule = $rules[(int) $this->today->format('z') % count($rules)];

        return '<text x="962" y="58" text-anchor="end" class="hd" font-size="20"' . $this->hook('data-rule tabindex="0" role="button" aria-label="Show the next rule"') . '>rule of the day: ' . e($rule) . '</text>';
    }

    private function block(): string
    {
        $c = $this->config;
        $years = $c['years'];
        $crew = count(array_filter($c['crew']['lanes'], static fn (array $lane): bool => ! $lane[2]));
        $signature = '<text x="%s" y="%s" class="hd" font-size="24">S. Mutev</text>';

        return $this->titleBlock(24, 430, 66, [
            ['CLASS', $c['class'], 150],
            ['LEVEL', "$years, since {$c['since']}", 120],
            ['BASE', $c['base'], 160],
            ['ALIGNMENT', $c['alignment'], 120],
            ['DRAWN BY', 'the crew', 110],
            ['CHECKED BY', $signature, 140, true],
            ['SCALE', "1 : $crew", 82],
            $this->sheetCell(),
        ]);
    }

    private function node(float $delay, string $inner, string $tag): string
    {
        $name = $this->config['crew']['lanes'][$tag][0];
        $hook = $this->hook("data-node=\"$tag\" data-agent=\"$tag\" tabindex=\"0\" role=\"button\" aria-label=\"" . e("$name: open the spec card") . '"');

        return "<g class=\"nd\" style=\"animation-delay:{$delay}s\"$hook>$inner</g>";
    }
}
