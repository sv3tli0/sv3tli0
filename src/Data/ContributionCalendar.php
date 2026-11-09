<?php declare(strict_types=1);

namespace Profile\Data;

use DateTimeImmutable;
use RuntimeException;

/** Last 12 months of the public contribution calendar; private work shows up as counts only. */
final readonly class ContributionCalendar
{
    private const string QUERY = 'query($login:String!){user(login:$login){contributionsCollection{restrictedContributionsCount contributionCalendar{totalContributions weeks{contributionDays{date contributionCount weekday}}}}}}';

    /**
     * @param list<array{date: string, count: int, week: int, weekday: int}> $days
     */
    public function __construct(
        public array $days,
        public int $total,
        public int $restricted,
    ) {}

    public static function fetch(string $login, string $token): self
    {
        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Authorization: bearer $token\r\nContent-Type: application/json\r\nUser-Agent: profile-generator\r\n",
            'content' => json_encode(['query' => self::QUERY, 'variables' => ['login' => $login]], JSON_THROW_ON_ERROR),
            'timeout' => 20,
            'ignore_errors' => true,
        ]]);
        $raw = file_get_contents('https://api.github.com/graphql', false, $context);
        if ($raw === false) {
            throw new RuntimeException('GitHub GraphQL request failed');
        }
        $payload = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        if (! isset($payload['data']['user']['contributionsCollection'])) {
            throw new RuntimeException('Unexpected GraphQL response: ' . substr($raw, 0, 300));
        }

        return self::fromCollection($payload['data']['user']['contributionsCollection']);
    }

    /** @param array<string, mixed> $collection */
    public static function fromCollection(array $collection): self
    {
        $days = [];
        foreach ($collection['contributionCalendar']['weeks'] as $w => $week) {
            foreach ($week['contributionDays'] as $day) {
                $days[] = [
                    'date' => $day['date'],
                    'count' => (int) $day['contributionCount'],
                    'week' => $w,
                    'weekday' => (int) $day['weekday'],
                ];
            }
        }

        return new self(
            $days,
            (int) $collection['contributionCalendar']['totalContributions'],
            (int) $collection['restrictedContributionsCount'],
        );
    }

    public function weeks(): int
    {
        return $this->days === [] ? 0 : $this->days[array_key_last($this->days)]['week'] + 1;
    }

    public function max(): int
    {
        return max(array_column($this->days, 'count') ?: [0]);
    }

    public function privateShare(): float
    {
        return $this->total > 0 ? $this->restricted / $this->total : 0.0;
    }

    public function activeDays(): int
    {
        return count(array_filter($this->days, static fn (array $d): bool => $d['count'] > 0));
    }

    public function longestStreak(): int
    {
        $best = 0;
        $run = 0;
        foreach ($this->days as $d) {
            $run = $d['count'] > 0 ? $run + 1 : 0;
            $best = max($best, $run);
        }

        return $best;
    }

    /** @return array{date: string, count: int, week: int, weekday: int} */
    public function busiest(): array
    {
        $best = $this->days[0];
        foreach ($this->days as $d) {
            if ($d['count'] > $best['count']) {
                $best = $d;
            }
        }

        return $best;
    }

    /**
     * Busiest run of consecutive days.
     *
     * @return array{from: int, to: int, sum: int} indexes into $days
     */
    public function peak(int $length = 14): array
    {
        $counts = array_column($this->days, 'count');
        $sum = array_sum(array_slice($counts, 0, $length));
        $best = ['from' => 0, 'to' => $length - 1, 'sum' => $sum];
        for ($i = $length, $c = count($counts); $i < $c; $i++) {
            $sum += $counts[$i] - $counts[$i - $length];
            if ($sum > $best['sum']) {
                $best = ['from' => $i - $length + 1, 'to' => $i, 'sum' => $sum];
            }
        }

        return $best;
    }

    public function date(int $index): DateTimeImmutable
    {
        return new DateTimeImmutable($this->days[$index]['date']);
    }
}
