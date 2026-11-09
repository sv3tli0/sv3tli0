<?php declare(strict_types=1);

namespace Profile;

/** One drawing in the set: paper, grid, zoned border and a title block. */
abstract class Sheet
{
    public const int TOTAL = 4;

    protected const float INNER = 24;

    /** Non-empty when the sheet is inlined into the site: scopes ids and styles, adds interaction hooks. */
    protected string $prefix = '';

    /** @param array<string, mixed> $config */
    public function __construct(
        protected readonly Palette $p,
        protected readonly Fonts $fonts,
        protected readonly array $config,
    ) {}

    public function inline(string $prefix): static
    {
        $copy = clone $this;
        $copy->prefix = $prefix;

        return $copy;
    }

    abstract public function slug(): string;

    abstract protected function number(): int;

    abstract protected function width(): int;

    abstract protected function height(): int;

    abstract protected function label(): string;

    abstract protected function body(): string;

    protected function css(): string
    {
        return '';
    }

    protected function keyframes(): string
    {
        return '';
    }

    protected function defs(): string
    {
        return '';
    }

    protected function site(): bool
    {
        return $this->prefix !== '';
    }

    protected function id(string $name): string
    {
        return $this->prefix . $name;
    }

    protected function url(string $name): string
    {
        return 'url(#' . $this->prefix . $name . ')';
    }

    /** Extra attributes that only the interactive site needs. */
    protected function hook(string $attributes): string
    {
        return $this->site() ? ' ' . $attributes : '';
    }

    public function render(): string
    {
        $w = $this->width();
        $h = $this->height();
        $p = $this->p;
        $i = self::INNER;
        $iw = $w - 2 * $i;
        $ih = $h - 2 * $i;
        $keyframes = '@keyframes draw{from{stroke-dashoffset:1}}@keyframes fade{from{opacity:0}}' . $this->keyframes();
        $rules = $this->baseCss() . $this->css();
        // Inlined sheets share one document, so their rules are nested under the sheet's own id.
        $style = $this->site()
            ? $keyframes . '#' . $this->id('svg') . '{' . $rules . '}'
            : $this->fonts->css . $keyframes . $rules;
        $root = $this->site()
            ? 'id="' . $this->id('svg') . '" class="sheet" data-sheet="' . $this->slug() . '" role="group"'
            : 'width="' . $w . '" height="' . $h . '" role="img"';

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 $w $h" $root aria-label="{$this->esc($this->label())}">
            <title>{$this->esc($this->label())}</title>
            <style>$style</style>
            <defs>
            <pattern id="{$this->id('gm')}" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M10 0H0V10" fill="none" stroke="{$p->gridMinor}" stroke-width=".6"/></pattern>
            <pattern id="{$this->id('gM')}" width="50" height="50" patternUnits="userSpaceOnUse" x="$i" y="$i"><rect width="50" height="50" fill="{$this->url('gm')}"/><path d="M50 0H0V50" fill="none" stroke="{$p->gridMajor}" stroke-width=".8"/></pattern>
            <marker id="{$this->id('ar')}" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 1L10 5L0 9z" fill="{$p->ink}"/></marker>
            <marker id="{$this->id('arr')}" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 1L10 5L0 9z" fill="{$p->red}"/></marker>
            {$this->defs()}
            </defs>
            <rect width="$w" height="$h" rx="6" fill="{$p->paper}"/>
            <rect x="$i" y="$i" width="$iw" height="$ih" fill="{$this->url('gM')}"/>
            {$this->frame($w, $h)}
            {$this->body()}
            </svg>
            SVG;
    }

    protected function esc(string $s): string
    {
        return e($s);
    }

    private function baseCss(): string
    {
        $p = $this->p;

        return "text{font-family:Barlow,'Barlow Semi Condensed','Arial Narrow',sans-serif;fill:{$p->ink}}"
            . ".m{fill:{$p->muted}}.b{font-weight:600}"
            . ".lb{font-size:9.5px;font-weight:600;letter-spacing:.07em;fill:{$p->muted}}"
            . ".hd{font-family:Caveat,'Segoe Print','Bradley Hand',cursive;font-weight:500;fill:{$p->red}}"
            . ".ln{fill:none;stroke:{$p->ink};stroke-width:1.4;stroke-linecap:round;stroke-linejoin:round}"
            . ".fn{fill:none;stroke:{$p->muted};stroke-width:.8}"
            . ".rd{fill:none;stroke:{$p->red};stroke-width:2;stroke-linecap:round;stroke-linejoin:round}"
            . ".pp{fill:{$p->paper}}"
            . '@media (prefers-reduced-motion:reduce){*{animation:none!important}.pk{display:none}}';
    }

    /** Double border with zone references, as on a real drawing sheet. */
    private function frame(int $w, int $h): string
    {
        $o = 8;
        $i = self::INNER;
        $cols = 8;
        $rows = max(2, (int) round(($h - 2 * $i) / 120));
        $cw = ($w - 2 * $i) / $cols;
        $rh = ($h - 2 * $i) / $rows;
        $out = '<rect x="' . $o . '" y="' . $o . '" width="' . ($w - 2 * $o) . '" height="' . ($h - 2 * $o) . '" class="fn"/>'
            . '<rect x="' . $i . '" y="' . $i . '" width="' . ($w - 2 * $i) . '" height="' . ($h - 2 * $i) . '" class="ln" stroke-width="1.6"/>';
        $ticks = '';
        $labels = '';
        for ($c = 0; $c < $cols; $c++) {
            $x = $i + $c * $cw;
            if ($c > 0) {
                $ticks .= 'M' . n($x) . " $o" . "V$i" . 'M' . n($x) . ' ' . ($h - $i) . 'V' . ($h - $o);
            }
            $cx = n($x + $cw / 2);
            $labels .= "<text x=\"$cx\" y=\"19\" class=\"lb\" text-anchor=\"middle\">" . ($c + 1) . '</text>'
                . "<text x=\"$cx\" y=\"" . ($h - 12) . '" class="lb" text-anchor="middle">' . ($c + 1) . '</text>';
        }
        for ($r = 0; $r < $rows; $r++) {
            $y = $i + $r * $rh;
            if ($r > 0) {
                $ticks .= "M$o " . n($y) . "H$i" . 'M' . ($w - $i) . ' ' . n($y) . 'H' . ($w - $o);
            }
            $cy = n($y + $rh / 2 + 3);
            $letter = chr(65 + $r);
            $labels .= "<text x=\"16\" y=\"$cy\" class=\"lb\" text-anchor=\"middle\">$letter</text>"
                . '<text x="' . ($w - 16) . "\" y=\"$cy\" class=\"lb\" text-anchor=\"middle\">$letter</text>";
        }

        return $out . "<path d=\"$ticks\" class=\"fn\"/>" . $labels;
    }

    /**
     * @param list<array{0: string, 1: string, 2: float, 3?: bool}> $cells label, value, width, value is raw markup
     */
    protected function titleBlock(float $x, float $y, float $h, array $cells): string
    {
        $total = array_sum(array_column($cells, 2));
        $out = '<rect x="' . n($x) . '" y="' . n($y) . '" width="' . n($total) . '" height="' . n($h) . '" class="ln pp" stroke-width="1.6"/>';
        $cx = $x;
        foreach ($cells as $k => $cell) {
            [$label, $value, $cw] = $cell;
            if ($k > 0) {
                $out .= '<path d="M' . n($cx) . ' ' . n($y) . 'v' . n($h) . '" class="ln" stroke-width="1"/>';
            }
            $out .= '<text x="' . n($cx + 9) . '" y="' . n($y + 15) . '" class="lb">' . e($label) . '</text>';
            $out .= ($cell[3] ?? false)
                ? sprintf($value, n($cx + 9), n($y + $h - 11))
                : '<text x="' . n($cx + 9) . '" y="' . n($y + $h - 11) . '" font-size="15">' . e($value) . '</text>';
            $cx += $cw;
        }

        return $out;
    }

    protected function sheetCell(): array
    {
        return ['SHEET', $this->number() . ' of ' . self::TOTAL, 70];
    }

    /** P&ID-style instrument bubble with a two-letter tag. */
    protected function bubble(float $x, float $y, string $tag, float $r = 22, string $extra = ''): string
    {
        return '<g' . $extra . '><circle cx="' . n($x) . '" cy="' . n($y) . '" r="' . n($r) . '" class="ln pp"/>'
            . '<path d="M' . n($x - $r) . ' ' . n($y) . 'h' . n(2 * $r) . '" class="fn" opacity=".55"/>'
            . '<text x="' . n($x) . '" y="' . n($y - 3) . '" text-anchor="middle" class="b" font-size="' . n($r * 0.62) . '">' . e($tag) . '</text></g>';
    }
}
