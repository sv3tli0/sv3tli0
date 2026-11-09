<?php declare(strict_types=1);

return [
    'login' => 'sv3tli0',
    'name' => 'Svetoslav Mutev',
    'role' => 'Full-stack developer, agent orchestrator',
    'lead' => [
        'I design the system. A crew of AI agents',
        'builds it in parallel, and every change',
        'is reviewed before it ships.',
    ],
    'since' => 2012,
    'class' => 'Agent orchestrator',
    'base' => 'Plovdiv, BG, remote',
    'alignment' => 'Chaotic good',

    'crew' => [
        // [title, skills that run it]
        'stages' => [
            ['Kickoff', '/kickoff'],
            ['Plan', '/plan'],
            ['Build', '/develop  /parallel'],
            ['Test', '/test  /qa'],
            ['Review', '/review'],
            ['Ship', '/commit  /deploy'],
        ],
        // tag => [name, what it does, is human]
        'lanes' => [
            'SM' => ['S. Mutev', 'architect, final say', true],
            'EX' => ['Explore', 'maps the codebase', false],
            'PL' => ['Plan', 'splits work into tracks', false],
            'LA' => ['lara', 'Laravel APIs, queues', false],
            'FR' => ['fronty', 'Vue + Inertia screens', false],
            'DS' => ['designer', 'Tailwind v4 UI', false],
            'CR' => ['code-reviewer', 'security, quality', false],
        ],
        // id => [lane, stage index, label]
        'steps' => [
            'brief' => ['SM', 0, 'brief'],
            'map' => ['EX', 0, 'map the code'],
            'tracks' => ['PL', 1, 'steps + tracks'],
            'ok' => ['SM', 1, 'approve plan'],
            'api' => ['LA', 2, 'API'],
            'ui' => ['FR', 2, 'screens'],
            'kit' => ['DS', 2, 'UI kit'],
            'pest' => ['LA', 3, 'Pest'],
            'e2e' => ['FR', 3, 'tests'],
            'rev' => ['CR', 4, 'review'],
            'call' => ['SM', 4, 'final call'],
            'ship' => ['SM', 5, 'ship it'],
        ],
        'flow' => [
            ['brief', 'map'], ['map', 'tracks'], ['tracks', 'ok'],
            ['ok', 'api'], ['ok', 'ui'], ['ok', 'kit'],
            ['api', 'pest'], ['ui', 'e2e'],
            ['pest', 'rev'], ['e2e', 'rev'], ['kit', 'rev'],
            ['rev', 'call'], ['call', 'ship'],
        ],
        'toolbox' => 'Codex, Cursor, Laravel Boost, MCP servers, hooks and 22 custom skills.',
    ],

    // Section drawing, top floor first. [what, why, hatch: steel|cross|lines|dots|slant|concrete]
    'stack' => [
        ['Quasar, Tauri 2', 'iOS, Android, desktop and web from one codebase', 'slant'],
        ['Vue 3.5, Nuxt 4, Inertia, Tailwind v4', 'Vite 8, TypeScript, Pinia; React 19 when asked', 'dots'],
        ['Docker, Octane + FrankenPHP, CI/CD', 'GitHub Actions, GitLab CI, AWS and GCP', 'lines'],
        ['MySQL, PostgreSQL, Redis', 'Horizon queues, Reverb websockets, Scout search', 'cross'],
        ['Laravel 13, Symfony 8, Filament 5', 'Frameworks, admin panels, Cashier + Stripe', 'steel'],
    ],
    'foundation' => ['PHP, from 5.2 to 8.5', 'The foundation, poured in 2012', 'concrete'],
    'walls' => ['Pest, Larastan, Vitest', 'The walls: every floor is tested'],
    'crane' => ['Claude Code + subagents', 'Codex, Cursor', 'Laravel Boost, MCP servers', 'Skills and hooks'],

    // One shows on the hero each day.
    'rules' => [
        'scout first, fan out second',
        'small model for lookups, strong model for judgment',
        'depth in a few agents beats breadth in many',
        'every change gets a review: an agent first, then me',
        'answer the question before touching the code',
        'refactors and features never share a commit',
        'say what a fan-out costs before launching it',
        'agents write the code, I own the decisions',
        'read the docs for the version you actually run',
        'tests green or it did not happen',
        'specialists over generalists',
        'no new dependency without a reason',
    ],
];
