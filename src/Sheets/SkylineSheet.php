<?php declare(strict_types=1);

namespace Profile\Sheets;

use DateTimeImmutable;
use Profile\Data\ContributionCalendar;
use Profile\Fonts;
use Profile\Hand;
use Profile\Palette;
use Profile\Sheet;

use function Profile\e;
use function Profile\n;
use function Profile\textWidth;

/** Site plan of the year: one building per day, height by contributions. */
final class SkylineSheet extends Sheet
{
    private const float BASE = 420;

    private const float WEEK = 16.4;

    private const float ROW_X = 5.4;

    private const float ROW_Y = -3.9;

    private const float PLOT = 0.78;

    private const float MAX_H = 190;

    /** @param array<string, mixed> $config */
    public function __construct(
        Palette $p,
        Fonts $fonts,
        array $config,
        private readonly ContributionCalendar $calendar,
        private readonly DateTimeImmutable $today,
    ) {
        parent::__construct($p, $fonts, $config);
    }

    public function slug(): string
    {
        return 'skyline';
    }

    protected function number(): int
    {
        return 4;
    }

    protected function width(): int
    {
        return 1000;
    }

    protected function height(): int
    {
        return 550;
    }

    protected function label(): string
    {
        $c = $this->calendar;
        $peak = $c->peak();

        return sprintf(
            'Contribution skyline for the last 12 months: %s contributions, %s%% in private repositories, busiest day %s with %d, busiest two weeks %s to %s with %s.',
            number_format($c->total),
            n($c->privateShare() * 100),
            $c->date(array_search($c->busiest(), $c->days, true))->format('M j'),
            $c->busiest()['count'],
            $c->date($peak['from'])->format('M j'),
            $c->date($peak['to'])->format('M j'),
            number_format($peak['sum']),
        );
    }

    protected function css(): string
    {
        $p = $this->p;

        return ".bd{transform-box:fill-box;transform-origin:50% 100%;animation:rise .7s cubic-bezier(.2,.7,.3,1) backwards}"
            . ".fr{fill:{$p->mix(0.14)}}.sd{fill:{$p->mix(0.32)}}.tp{fill:{$p->mix(0.55)}}"
            . ".city{stroke:{$p->ink};stroke-width:.6;stroke-linejoin:round}"
            . ".lot{fill:none;stroke:{$p->muted};stroke-width:.5;opacity:.55}"
            . '.nt{animation:fade .6s 2.4s backwards}';
    }

    protected function keyframes(): string
    {
        return '@keyframes rise{from{transform:scaleY(0)}}';
    }

    protected function body(): string
    {
        return '<text x="56" y="76" class="b" font-size="28">What got built this year</text>'
            . '<text x="57" y="102" class="m" font-size="16">One building per day, its height set by that day&#8217;s contributions.</text>'
            . $this->north() . $this->city() . $this->months() . $this->datum() . $this->sprint() . $this->block();
    }

    /** @return array{0: float, 1: float, 2: float} front-left x, base y, height */
    private function place(array $day): array
    {
        $depth = 6 - $day['weekday'];
        $x = $this->originX() + $day['week'] * self::WEEK + $depth * self::ROW_X;
        $y = self::BASE + $depth * self::ROW_Y;
        $max = max(1, $this->calendar->max());
        $h = $day['count'] > 0 ? max(3.0, self::MAX_H * sqrt($day['count'] / $max)) : 0.0;

        return [$x, $y, $h];
    }

    private function originX(): float
    {
        $span = $this->calendar->weeks() * self::WEEK + 7 * self::ROW_X;

        return ($this->width() - $span) / 2;
    }

    private function city(): string
    {
        $days = $this->calendar->days;
        // back row first, so nearer buildings cover the ones behind
        usort($days, static fn (array $a, array $b): int => [$a['weekday'], $a['week']] <=> [$b['weekday'], $b['week']]);
        $w = self::WEEK * self::PLOT;
        $vx = self::ROW_X * self::PLOT;
        $vy = self::ROW_Y * self::PLOT;
        $lots = '';
        $out = '';
        foreach ($days as $day) {
            [$x, $y, $h] = $this->place($day);
            $top = $y - $h;
            $roof = 'M' . n($x) . ' ' . n($top) . 'h' . n($w) . 'l' . n($vx) . ' ' . n($vy) . 'h' . n(-$w) . 'z';
            if ($h === 0.0) {
                $lots .= $roof;
                continue;
            }
            $delay = n($day['week'] * 0.035 + (6 - $day['weekday']) * 0.03);
            $out .= "<g class=\"bd\" style=\"animation-delay:{$delay}s\"" . $this->hook("data-d=\"{$day['date']}\" data-c=\"{$day['count']}\"") . '>'
                . '<rect class="fr" x="' . n($x) . '" y="' . n($top) . '" width="' . n($w) . '" height="' . n($h) . '"/>'
                . '<path class="sd" d="M' . n($x + $w) . ' ' . n($y) . 'l' . n($vx) . ' ' . n($vy) . 'v' . n(-$h) . 'l' . n(-$vx) . ' ' . n(-$vy) . 'z"/>'
                . '<path class="tp" d="' . $roof . '"/></g>';
        }

        return "<path class=\"lot\" d=\"$lots\"/><g class=\"city\">$out</g>";
    }

    private function months(): string
    {
        $out = '';
        $seen = null;
        foreach ($this->calendar->days as $i => $day) {
            $month = substr($day['date'], 0, 7);
            if ($month === $seen || $day['weekday'] !== 0) {
                continue;
            }
            $seen = $month;
            $date = $this->calendar->date($i);
            $text = $date->format('n') === '1' ? $date->format('M Y') : $date->format('M');
            // each week runs diagonally into the page; tick its middle row
            $x = $this->originX() + $day['week'] * self::WEEK + 3 * self::ROW_X;
            $out .= '<path d="M' . n($x) . ' ' . n(self::BASE + 4) . 'v6" class="fn"/>'
                . '<text x="' . n($x) . '" y="' . n(self::BASE + 20) . '" class="m" font-size="11.5">' . $text . '</text>';
        }

        return $out;
    }

    /** Datum line from the tallest roof, the way a section marks a level. */
    private function datum(): string
    {
        $busiest = $this->calendar->busiest();
        [$x, $y, $h] = $this->place($busiest);
        $top = $y - $h;
        $date = (new DateTimeImmutable($busiest['date']))->format('M j');

        return '<g class="nt">'
            . '<path d="M' . n($x - 3) . ' ' . n($top) . 'H64" class="fn" stroke-dasharray="6 4"/>'
            . '<path d="M64 ' . n($top) . 'l-6 -9h12z" class="ln pp" stroke-width="1"/>'
            . '<text x="78" y="' . n($top - 7) . '" font-size="13"><tspan class="b">' . $busiest['count'] . '</tspan> on ' . $date . ', the busiest day</text>'
            . '</g>';
    }

    private function sprint(): string
    {
        $c = $this->calendar;
        $peak = $c->peak();
        $span = array_slice($c->days, $peak['from'], $peak['to'] - $peak['from'] + 1);
        $xs = [];
        $tops = [];
        foreach ($span as $day) {
            [$x, $y, $h] = $this->place($day);
            $xs[] = $x;
            $xs[] = $x + self::WEEK * self::PLOT + self::ROW_X * self::PLOT;
            $tops[] = $y - $h;
        }
        $x1 = min($xs);
        $x2 = max($xs);
        $y = min($tops) - 16;
        $note = sprintf(
            '%s to %s: %s in two weeks',
            $c->date($peak['from'])->format('M j'),
            $c->date($peak['to'])->format('M j'),
            number_format($peak['sum']),
        );
        $half = textWidth($note, 20, 0.37) / 2;
        $cx = min(max(($x1 + $x2) / 2, 40 + $half), $this->width() - 40 - $half);

        return '<g class="nt">'
            . '<path d="' . Hand::bracket($x1, $x2, $y) . '" class="rd" stroke-width="1.8"/>'
            . '<text x="' . n($cx) . '" y="' . n($y - 14) . '" text-anchor="middle" class="hd" font-size="20">' . e($note) . '</text>'
            . '</g>';
    }

    private function north(): string
    {
        return '<g transform="translate(936 72)"><circle r="15" class="ln" stroke-width="1"/>'
            . '<path d="M0 -12L5 6L0 2L-5 6z" fill="' . $this->p->ink . '"/>'
            . '<text y="-20" text-anchor="middle" class="lb">N</text></g>';
    }

    private function block(): string
    {
        $c = $this->calendar;
        $first = $c->date(0);
        $last = $c->date(count($c->days) - 1);

        return $this->titleBlock(24, 474, 52, [
            ['CONTRIBUTIONS', number_format($c->total), 130],
            ['IN PRIVATE REPOS', n($c->privateShare() * 100) . '%', 130],
            ['ACTIVE DAYS', $c->activeDays() . ' of ' . count($c->days), 130],
            ['LONGEST STREAK', $c->longestStreak() . ' days', 130],
            ['PERIOD', $first->format('M Y') . ' to ' . $last->format('M Y'), 202],
            ['UPDATED', $this->today->format('M j, Y'), 160],
            $this->sheetCell(),
        ]);
    }
}
