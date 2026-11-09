<?php declare(strict_types=1);

namespace Profile;

use DateTimeImmutable;
use Profile\Data\ContributionCalendar;
use Profile\Sheets\CrewSheet;
use Profile\Sheets\HeroSheet;
use Profile\Sheets\SkylineSheet;
use Profile\Sheets\StackSheet;

final class SheetSet
{
    /**
     * @param array<string, mixed> $config
     * @return list<Sheet>
     */
    public static function build(
        Palette $palette,
        Fonts $fonts,
        array $config,
        DateTimeImmutable $today,
        ?ContributionCalendar $calendar,
    ): array {
        $sheets = [
            new HeroSheet($palette, $fonts, $config, $today),
            new CrewSheet($palette, $fonts, $config),
            new StackSheet($palette, $fonts, $config),
        ];
        if ($calendar !== null) {
            $sheets[] = new SkylineSheet($palette, $fonts, $config, $calendar, $today);
        }

        return $sheets;
    }
}
