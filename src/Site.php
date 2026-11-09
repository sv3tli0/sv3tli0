<?php declare(strict_types=1);

namespace Profile;

use DateTimeImmutable;
use Profile\Data\ContributionCalendar;
use RuntimeException;

/** The interactive drawing set for GitHub Pages: every sheet inlined, both themes, plus the work-order form. */
final readonly class Site
{
    private const array SECTIONS = [
        'hero' => ['Overview', 'Click any bubble for its spec card, or send a feature through the crew.'],
        'crew' => ['Crew', 'Hover a lane to trace its work. Click a name for its spec card.'],
        'stack' => ['Stack', 'Hover a floor to pick it out. Click the crane for the toolbox.'],
        'skyline' => ['Site plan', 'Hover a building to see the day.'],
    ];

    /** @param array<string, mixed> $config */
    public function __construct(
        private array $config,
        private DateTimeImmutable $today,
        private ?ContributionCalendar $calendar,
        private string $root,
    ) {}

    public function build(string $dist): void
    {
        $this->copy("{$this->root}/site/site.css", "$dist/site.css");
        $this->copy("{$this->root}/site/site.js", "$dist/site.js");
        if (! is_dir("$dist/fonts") && ! mkdir("$dist/fonts", 0o755, true)) {
            throw new RuntimeException("Cannot create $dist/fonts");
        }
        foreach (Fonts::FACES as [, , $file]) {
            $this->copy("{$this->root}/assets/fonts/$file", "$dist/fonts/$file");
        }
        file_put_contents("$dist/favicon.svg", $this->favicon());
        file_put_contents("$dist/.nojekyll", '');
        file_put_contents("$dist/index.html", $this->html());
    }

    private function html(): string
    {
        $c = $this->config;
        $sheets = $this->sheets();
        $nav = '';
        $sections = '';
        $n = 0;
        foreach (self::SECTIONS as $slug => [$title, $hint]) {
            if (! isset($sheets[$slug])) {
                continue;
            }
            $n++;
            $nav .= "<a href=\"#sheet-$slug\"><b>$n</b> $title</a>";
            $sections .= "<section id=\"sheet-$slug\" class=\"plate\" aria-label=\"Sheet $n, " . e($title) . '">'
                . '<div class="v-dark">' . $sheets[$slug]['dark'] . '</div>'
                . '<div class="v-light">' . $sheets[$slug]['light'] . '</div>'
                . $this->controls($slug, $hint)
                . '</section>';
        }
        $turnstile = $c['tickets']['turnstile'] !== ''
            ? '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>'
            : '';
        $data = json_encode($this->data(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $name = e($c['name']);
        $role = e($c['role']);
        $site = e($c['site']);

        return <<<HTML
            <!doctype html>
            <html lang="en">
            <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>$name</title>
            <meta name="description" content="$role. A drawing set of how I build: the crew of AI agents, the stack and a year of work.">
            <meta name="color-scheme" content="light dark">
            <meta property="og:title" content="$name">
            <meta property="og:description" content="$role. I design the system, a crew of AI agents builds it, and every change is reviewed before it ships.">
            <meta property="og:url" content="$site">
            <link rel="icon" href="favicon.svg" type="image/svg+xml">
            <link rel="preload" href="fonts/BarlowSemiCondensed-Regular.woff2" as="font" type="font/woff2" crossorigin>
            <link rel="stylesheet" href="site.css">
            <script>try{var t=localStorage.getItem('theme');if(t==='light'||t==='dark')document.documentElement.dataset.theme=t}catch(e){}</script>
            $turnstile
            </head>
            <body>
            <a class="skip" href="#main">Skip to the drawings</a>
            <header class="bar">
              <a class="brand" href="#sheet-hero"><span class="tag">SM</span><span class="who">$name</span></a>
              <nav class="index" aria-label="Drawing index">$nav<a class="cta" href="#request">Work order</a></nav>
              <output class="coords" aria-hidden="true">X 0000&#8195;Y 0000</output>
              <div class="theme" role="radiogroup" aria-label="Theme">
                <button type="button" role="radio" data-theme-set="auto" aria-checked="true">Auto</button>
                <button type="button" role="radio" data-theme-set="light" aria-checked="false">Light</button>
                <button type="button" role="radio" data-theme-set="dark" aria-checked="false">Dark</button>
              </div>
            </header>
            <main id="main">
            $sections
            {$this->order()}
            </main>
            {$this->footer()}
            <aside class="card" data-card hidden aria-labelledby="card-title">
              <button type="button" class="close" data-close aria-label="Close the spec card">&#215;</button>
              <div class="card-head"><span class="tag" data-card-tag></span><div><h2 id="card-title" data-card-name></h2><p data-card-role></p></div></div>
              <p class="card-about" data-card-about></p>
              <h3 data-card-list-title>Works at</h3>
              <ul data-card-list></ul>
            </aside>
            <div class="tip" data-tip hidden></div>
            <div class="cross" aria-hidden="true"><i></i><i></i></div>
            <script type="application/json" id="profile-data">$data</script>
            <script type="module" src="site.js"></script>
            </body>
            </html>
            HTML;
    }

    /** @return array<string, array{dark: string, light: string}> */
    private function sheets(): array
    {
        $out = [];
        foreach (Theme::cases() as $theme) {
            foreach (SheetSet::build($theme->palette(), Fonts::none(), $this->config, $this->today, $this->calendar) as $sheet) {
                $prefix = $sheet->slug() . '-' . $theme->value . '-';
                $out[$sheet->slug()][$theme->value] = $sheet->inline($prefix)->render();
            }
        }

        return $out;
    }

    private function controls(string $slug, string $hint): string
    {
        $hint = '<p class="hint">' . e($hint) . '</p>';

        return match ($slug) {
            'hero' => '<div class="console">'
                . '<form class="sim" data-sim><label for="feature">Send a feature through the crew</label>'
                . '<div class="row"><input id="feature" name="feature" value="Add subscription billing" maxlength="60" autocomplete="off" spellcheck="false">'
                . '<button type="submit">Run it</button><button type="button" class="ghost" data-replay="hero">Replay the drawing</button></div></form>'
                . '<ol class="log" data-log aria-live="polite"><li class="idle">Press “Run it” and watch the crew pick the feature up.</li></ol>'
                . '<p class="hint">A simulation of how a feature moves through the crew. Click any bubble for its spec card, or the red note for another rule.</p></div>',
            'skyline' => '<div class="under">' . $hint . '<button type="button" class="ghost" data-replay="skyline">Rebuild the year</button></div>',
            default => '<div class="under">' . $hint . '</div>',
        };
    }

    private function order(): string
    {
        $t = $this->config['tickets'];
        $kinds = '';
        $first = true;
        foreach ($t['kinds'] as $value => $label) {
            $kinds .= '<label class="kind"><input type="radio" name="kind" value="' . e($value) . '"' . ($first ? ' checked' : '') . '><span>' . e($label) . '</span></label>';
            $first = false;
        }
        $timelines = '<option value="">Not sure yet</option>';
        foreach ($t['timelines'] as $timeline) {
            $timelines .= '<option>' . e($timeline) . '</option>';
        }
        $captcha = $t['turnstile'] !== '' ? '<div class="cf-turnstile wide" data-sitekey="' . e($t['turnstile']) . '" data-theme="auto"></div>' : '';

        return <<<HTML
            <section id="request" class="plate order" aria-labelledby="order-title">
              <div class="order-sheet">
                <div class="order-head">
                  <h2 id="order-title">Work order</h2>
                  <p>A new product, contract work in your team, help setting up an agent workflow, or just hello. Only I see what you send; nothing here is published.</p>
                </div>
                <form data-ticket novalidate>
                  <div class="fields">
                    <label>Your name<input name="name" required maxlength="80" autocomplete="name"></label>
                    <label>Email for the reply<input name="email" type="email" required maxlength="120" autocomplete="email"></label>
                    <fieldset class="kinds wide"><legend>What is it</legend>$kinds</fieldset>
                    <label>Timeline<select name="timeline">$timelines</select></label>
                    <label class="wide">Details<textarea name="message" required minlength="20" maxlength="4000" rows="6" placeholder="What are we building, for whom, and what does done look like?"></textarea></label>
                    <label class="hp" aria-hidden="true">Leave this empty<input name="company_site" tabindex="-1" autocomplete="off"></label>
                    $captcha
                  </div>
                  <div class="actions"><button type="submit">File the work order</button><p class="status" data-status role="status"></p></div>
                </form>
                <div class="filed" data-filed hidden tabindex="-1">
                  <span class="stamp">Filed</span>
                  <p data-filed-text></p>
                </div>
                <dl class="order-block">
                  <div><dt>Received by</dt><dd class="sig">S. Mutev</dd></div>
                  <div><dt>Visible to</dt><dd>only me</dd></div>
                  <div><dt>Reply</dt><dd>by email</dd></div>
                </dl>
              </div>
            </section>
            HTML;
    }

    private function footer(): string
    {
        $c = $this->config['contact'];

        return '<footer class="foot"><nav aria-label="Elsewhere">'
            . '<a href="' . e($c['github']) . '">GitHub</a><a href="' . e($c['linkedin']) . '">LinkedIn</a>'
            . '<a href="' . e($c['x']) . '">X</a><a href="mailto:' . e($c['email']) . '">Email</a></nav>'
            . '<p>Drawn by a PHP script and redrawn every six hours. Keys: <kbd>1</kbd>–<kbd>4</kbd> sheets, <kbd>T</kbd> theme, <kbd>Esc</kbd> close.</p></footer>';
    }

    /** @return array<string, mixed> */
    private function data(): array
    {
        $crew = $this->config['crew'];
        $agents = [];
        foreach ($crew['lanes'] as $tag => [$name, $role, $human, $about]) {
            $agents[$tag] = ['name' => $name, 'role' => $role, 'human' => $human, 'about' => $about, 'steps' => []];
        }
        foreach ($crew['steps'] as [$lane, $stage, $label]) {
            [$title, $skills] = $crew['stages'][$stage];
            $agents[$lane]['steps'][] = ['stage' => $title, 'label' => $label, 'skills' => preg_replace('/\s+/', ' ', $skills)];
        }

        return [
            'agents' => $agents,
            'crane' => ['lines' => $this->config['crane'], 'toolbox' => $crew['toolbox']],
            'rules' => $this->config['rules'],
            'tickets' => [
                'endpoint' => $this->config['tickets']['endpoint'],
                'kinds' => $this->config['tickets']['kinds'],
                'email' => $this->config['contact']['email'],
            ],
        ];
    }

    private function favicon(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="12" fill="#12355C"/>'
            . '<circle cx="32" cy="32" r="21" fill="none" stroke="#EEF4FB" stroke-width="3"/><path d="M11 32h42" stroke="#A9C4E2" stroke-width="1.5"/>'
            . '<text x="32" y="28" text-anchor="middle" font-family="Arial Narrow,Arial,sans-serif" font-weight="700" font-size="15" fill="#EEF4FB">SM</text></svg>';
    }

    private function copy(string $from, string $to): void
    {
        if (! copy($from, $to)) {
            throw new RuntimeException("Cannot copy $from to $to");
        }
    }
}
