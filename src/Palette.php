<?php declare(strict_types=1);

namespace Profile;

/** White ink is the crew's work, red pencil is the human's. */
final readonly class Palette
{
    public function __construct(
        public string $paper,
        public string $gridMinor,
        public string $gridMajor,
        public string $ink,
        public string $muted,
        public string $red,
        public string $inkName,
    ) {}

    /** Solid blend from paper (0) to ink (1), so stacked shapes still hide what is behind them. */
    public function mix(float $t): string
    {
        $a = sscanf($this->paper, '#%2x%2x%2x');
        $b = sscanf($this->ink, '#%2x%2x%2x');

        return vsprintf('#%02X%02X%02X', array_map(
            static fn (int $from, int $to): int => (int) round($from + ($to - $from) * $t),
            $a,
            $b,
        ));
    }
}
