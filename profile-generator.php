<?php declare(strict_types=1);

/**
 * GitHub profile generator: draws the blueprint sheets into dist/.
 *
 * Usage: GITHUB_TOKEN=... php profile-generator.php [--output]
 */

readonly class BackendArchitect
{
    public function __construct(
        private(set) array $frameworks = ['Laravel 13', 'Symfony 8', 'Filament 5', 'Node.js'],
        private(set) array $databases = ['MySQL', 'PostgreSQL', 'Redis'],
    ) {}

    public function chooseArchitecture(string $need): string
    {
        return match($need) {
            'scalability' => 'Microservices with Docker & K8s',
            'performance' => 'Octane + FrankenPHP with caching layers',
            'organization' => 'Modular monolith with extractable modules',
            default => 'Well-structured Monolith with extractable services'
        };
    }
}

readonly class FrontendCraftsman
{
    public function getPreferredStack(): array
    {
        return [
            'frameworks' => ['Vue 3.5', 'Nuxt 4', 'Inertia', 'Quasar', 'Tauri 2', 'React 19'],
            'styling' => ['TailwindCSS v4', 'Modern CSS'],
            'philosophy' => 'User experience > fancy animations'
        ];
    }
}

readonly class DevOpsEngineer
{
    public function getWorkflow(): array
    {
        return [
            'containers' => 'Docker + Kubernetes',
            'cicd' => 'GitHub Actions + GitLab CI/CD',
            'cloud' => 'AWS/GCP with infrastructure as code',
            'monitoring' => 'Performance metrics & error tracking'
        ];
    }
}

readonly class AIIntegrator
{
    public function __construct(
        private(set) array $codingTools = ['Claude Code', 'Codex', 'Cursor'],
        private(set) array $crew = ['Explore', 'Plan', 'lara', 'fronty', 'designer', 'code-reviewer'],
        private(set) array $infrastructure = ['Subagents', 'Skills & hooks', 'MCP Servers'],
    ) {}

    public function augmentWorkflow(string $task): string
    {
        return match($task) {
            'planning' => 'Explore scouts, Plan splits the work into parallel tracks',
            'coding' => 'lara, fronty and designer build in parallel',
            'review' => 'code-reviewer first, then a human final call',
            'shipping' => 'Nothing ships without review',
            default => 'Human decisions, agent throughput'
        };
    }
}

$svetoslav = new readonly class (
    backend: new BackendArchitect,
    frontend: new FrontendCraftsman,
    devOp: new DevOpsEngineer,
    ai: new AIIntegrator,
    location: 'Plovdiv, Bulgaria (REMOTE)',
    yearsOfExperience: (int) date('Y') - 2012,
    availability: new DateTimeImmutable()->add(new DateInterval('P1W')),
){
    public function __construct (
        private(set) BackendArchitect $backend,
        private(set) FrontendCraftsman $frontend,
        private(set) DevOpsEngineer $devOp,
        private(set) AIIntegrator $ai,
        private(set) string $location,
        private(set) int $yearsOfExperience,
        private(set) DateTimeImmutable $availability,
    ) {}
};

spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'Profile\\')) {
        require __DIR__ . '/src/' . str_replace('\\', '/', substr($class, 8)) . '.php';
    }
});
require __DIR__ . '/src/functions.php';

use Profile\Data\ContributionCalendar;
use Profile\Fonts;
use Profile\Sheets\CrewSheet;
use Profile\Sheets\HeroSheet;
use Profile\Sheets\SkylineSheet;
use Profile\Sheets\StackSheet;
use Profile\Theme;

$config = require __DIR__ . '/config/profile.php';
$today = new DateTimeImmutable('now', new DateTimeZone('UTC'));
$config['years'] = (int) $today->format('Y') - $config['since'];

if (in_array('--output', $argv ?? [], true)) {
    $rule = $config['rules'][(int) $today->format('z') % count($config['rules'])];
    echo "{$config['name']}, {$config['role']}" . PHP_EOL
        . "📍 {$svetoslav->location} · {$svetoslav->yearsOfExperience} years" . PHP_EOL
        . '🤖 ' . implode(', ', $svetoslav->ai->codingTools) . ' · crew: ' . implode(', ', $svetoslav->ai->crew) . PHP_EOL
        . "📐 Rule of the day: $rule" . PHP_EOL;
    exit(0);
}

$token = getenv('GITHUB_TOKEN') ?: null;
$inCi = getenv('CI') === 'true';
$calendar = null;
if ($token !== null) {
    try {
        $calendar = ContributionCalendar::fetch($config['login'], $token);
    } catch (Throwable $e) {
        fwrite(STDERR, "❌ Contribution calendar: {$e->getMessage()}" . PHP_EOL);
        // Publishing without the skyline would leave a broken image on the profile.
        exit($inCi ? 1 : 0);
    }
} elseif ($inCi) {
    fwrite(STDERR, '❌ GITHUB_TOKEN is required in CI' . PHP_EOL);
    exit(1);
} else {
    fwrite(STDERR, '⚠️  No GITHUB_TOKEN, skipping the skyline. Try: GITHUB_TOKEN=$(gh auth token) php profile-generator.php' . PHP_EOL);
}

$fonts = new Fonts(__DIR__ . '/assets/fonts');
$dist = __DIR__ . '/dist';
if (! is_dir($dist) && ! mkdir($dist, 0o755, true)) {
    fwrite(STDERR, "❌ Cannot create $dist" . PHP_EOL);
    exit(1);
}

foreach (Theme::cases() as $theme) {
    $palette = $theme->palette();
    $sheets = [
        new HeroSheet($palette, $fonts, $config, $today),
        new CrewSheet($palette, $fonts, $config),
        new StackSheet($palette, $fonts, $config),
    ];
    if ($calendar !== null) {
        $sheets[] = new SkylineSheet($palette, $fonts, $config, $calendar, $today);
    }
    foreach ($sheets as $sheet) {
        $file = "$dist/{$sheet->slug()}-{$theme->value}.svg";
        file_put_contents($file, $sheet->render());
        echo '✅ ' . basename($file) . ' (' . number_format(filesize($file) / 1024, 1) . ' KB)' . PHP_EOL;
    }
}
