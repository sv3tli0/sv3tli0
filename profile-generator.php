<?php declare(strict_types=1);

/**
 * GitHub Profile Generator - Executable Demo
 * 
 * @author Svetoslav Mutev
 * @version 1.0
 */

readonly class BackendArchitect
{
    public function __construct(
        private(set) array $frameworks = ['Laravel', 'Symfony', 'Node.js'],
        private(set) array $databases = ['MySQL', 'PostgreSQL', 'Redis'],
    ) {}

    public function chooseArchitecture(string $need): string
    {
        return match($need) {
            'scalability' => 'Microservices with Docker & K8s',
            'performance' => 'Optimized APIs with caching layers',
            'organization' => 'Monorepo with shared libraries',
            default => 'Well-structured Monolith with extractable services'
        };
    }
}

readonly class FrontendCraftsman
{
    public function getPreferredStack(): array
    {
        return [
            'frameworks' => ['Vue.js', 'React', 'Alpine.js'],
            'styling' => ['SASS/SCSS', 'Modern CSS3', 'TailwindCSS', 'CSS Grid/Flexbox'],
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
            'cicd' => 'GitLab CI/CD + GitHub Actions',
            'cloud' => 'AWS/GCP with infrastructure as code',
            'monitoring' => 'Performance metrics & error tracking'
        ];
    }
}

readonly class AIIntegrator
{
    public function __construct(
        private(set) array $codingTools = ['Claude Code', 'Cursor IDE', 'GitHub Copilot'],
        private(set) array $llmApis = ['Anthropic Claude', 'OpenAI GPT', 'Local LLMs'],
        private(set) array $infrastructure = ['MCP Servers', 'AI Agents', 'Prompt Engineering'],
    ) {}

    public function augmentWorkflow(string $task): string
    {
        return match($task) {
            'coding' => 'Claude Code + Cursor IDE for AI-assisted development',
            'review' => 'AI-powered code review with contextual analysis',
            'architecture' => 'LLM-assisted design decisions and documentation',
            'testing' => 'AI-generated test cases with edge case discovery',
            'debugging' => 'Intelligent error analysis and fix suggestions',
            default => 'Human expertise enhanced by AI capabilities'
        };
    }
}

$svetoslav = new readonly class (
    backend: new BackendArchitect,
    frontend: new FrontendCraftsman,
    devOp: new DevOpsEngineer,
    ai: new AIIntegrator,
    location: 'Plovdiv, Bulgaria (REMOTE)',
    yearsOfExperience: 14,
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

// ═══════════════════════════════════════════════════════════
// Dynamic content generator - updates README.md daily
// ═══════════════════════════════════════════════════════════

/*
 * Dynamic content that could be updated daily
 */
readonly class DynamicContent
{
    public function __construct(
        private(set) DateTimeImmutable $currentDate = new DateTimeImmutable()
    ) {}
    
    public function getCurrentFocus(): string
    {
        $weekday = (int)$this->currentDate->format('N'); // 1=Monday, 7=Sunday

        if ($weekday >= 6) { // Weekend
            $weekendFocuses = [
                '🌲 Hiking in the Rhodope Mountains',
                '📚 Reading philosophy while drinking coffee',
                '🏔️ Mountain biking adventures',
                '☕ Discovering new coffee brewing methods',
                '🎣 Peaceful fishing by the Maritsa River',
                '🌿 Exploring Bulgarian nature reserves',
                '🎮 Gaming session to recharge creativity',
                '🧘 Balancing code life with mindfulness',
                '📖 Studying AI research papers over espresso',
                '🏕️ Camping in the Balkan Mountains',
                '🎵 Discovering new music while side-projecting',
                '🍳 Cooking Bulgarian recipes & experimenting',
            ];
            return $weekendFocuses[array_rand($weekendFocuses)];
        }

        $workFocuses = [
            // Laravel & PHP
            'Deep diving into Laravel 12 performance optimizations',
            'Mastering Vue 3 Composition API with TypeScript',
            'Exploring Symfony 7 new features and improvements',
            'Laravel Octane for high-performance applications',
            'Event-driven architecture with Laravel Events',
            'PHP 8.5 new features and performance improvements',
            'Laravel Reverb for real-time WebSocket broadcasting',
            'Building reusable Laravel packages for AI integrations',
            'Eloquent performance tuning with query profiling',
            'Laravel Pint & static analysis with PHPStan level 9',
            'Symfony Messenger for async task processing',
            'PHP Fibers for concurrent HTTP requests in APIs',

            // Frontend & Fullstack
            'Vue.js SSR with Nuxt.js architecture patterns',
            'Modern CSS Grid and Container Queries implementation',
            'TailwindCSS v4 with CSS-first configuration',
            'Inertia.js v2 for seamless SPA-like Laravel apps',
            'TypeScript strict mode across Vue.js projects',
            'Web Components for framework-agnostic UI elements',
            'Nuxt 4 server components and hybrid rendering strategies',
            'React Server Components for zero-bundle server rendering',
            'Next.js App Router patterns for React full-stack apps',
            'Vue 3 Vapor mode for compile-time optimized rendering',
            'Pinia store patterns with TypeScript for scalable state',

            // Symfony Deep Dives
            'Symfony Scheduler component for PHP-defined recurring tasks',
            'Symfony UX Turbo for Hotwire-powered reactive interfaces',
            'API Platform with Symfony for auto-generated REST & GraphQL',
            'Symfony Security component custom authenticators and voters',
            'Doctrine ORM performance tuning with second-level cache',

            // DevOps & Infrastructure
            'Docker multi-stage builds for PHP applications',
            'Microservices communication patterns with RabbitMQ',
            'API security with JWT and OAuth2 best practices',
            'Kubernetes autoscaling for PHP workloads',
            'GitHub Actions reusable workflows and composite actions',
            'Terraform modules for multi-cloud infrastructure',
            'Grafana + Prometheus monitoring for Laravel apps',
            'Serverless PHP on AWS Lambda with Bref framework',
            'GitLab CI/CD pipeline optimization for monorepos',
            'Cloudflare Workers for edge computing and caching',
            'ArgoCD GitOps workflows for Kubernetes deployments',
            'GitHub Codespaces dev containers for instant onboarding',

            // AI & LLM - Heavy focus
            'Building custom MCP servers for AI-enhanced workflows',
            'Claude Code sessions for complex refactoring tasks',
            'Integrating LLM APIs into production applications',
            'AI agent orchestration for automated development pipelines',
            'Cursor IDE AI-assisted pair programming sessions',
            'Prompt engineering for reliable code generation',
            'AI-powered code review and quality analysis',
            'Building intelligent development automation tools',
            'Fine-tuning AI workflows with custom system prompts',
            'MCP server development for database query assistants',
            'Claude API tool use for automated Laravel scaffolding',
            'RAG pipelines for codebase-aware AI assistants',
            'AI-driven test generation for legacy PHP codebases',
            'Evaluating local LLMs via Ollama for dev workflows',
            'Building AI-powered CLI tools with Claude SDK',
            'Structured output schemas for reliable AI integrations',
            'Multi-agent systems for complex development tasks',
            'AI-assisted database migration planning and review',
            'Developing .cursorrules for team-wide AI consistency',
            'Automating PR reviews with Claude Code hooks',
        ];

        // Use day + hour combo so content changes every 6h run
        $seed = (int)$this->currentDate->format('z') * 4 + intdiv((int)$this->currentDate->format('G'), 6);
        return $workFocuses[$seed % count($workFocuses)];
    }
    
    public function getExperienceDays(): int
    {
        $startDate = new DateTimeImmutable('2012-01-01');
        return $startDate->diff($this->currentDate)->days;
    }
    
    public function getTechTip(): string
    {
        $tips = [
            // Laravel Tips
            "🎼 Laravel: Use `Route::model()` for automatic model binding with custom logic",
            "🎼 Laravel: `DB::transaction()` with closures automatically rolls back on exceptions",
            "🎼 Laravel: Use `when()` method for conditional query building",
            "🎼 Laravel: `Collection::lazy()` for memory-efficient large dataset processing",
            "🎼 Laravel: Use `Cache::remember()` with closure for automatic cache-or-execute logic",
            "🎼 Laravel: `Str::of()` provides fluent string manipulation methods",
            "🎼 Laravel: `once()` helper memoizes expensive function calls within a request",
            "🎼 Laravel: Use `withoutWrapping()` on resources for cleaner API responses",
            "🎼 Laravel: `preventLazyLoading()` in dev catches N+1 queries early",
            "🎼 Laravel: Pipelines are perfect for multi-step data transformations",
            "🎼 Laravel: `Concurrency::run()` executes multiple closures in parallel",
            "🎼 Laravel: Use `Model::shouldBeStrict()` to enforce discipline in development",
            "🎼 Laravel: `dispatchAfterResponse()` runs jobs without making users wait",
            "🎼 Laravel: Reverb provides native WebSocket broadcasting without third parties",
            "🎼 Laravel: Use `Number::forHumans()` for readable file sizes and counts",

            // PHP Tips
            "🐘 PHP: `readonly` classes are perfect for DTOs and value objects",
            "🐘 PHP: Use `match()` expressions - they're stricter than `switch` statements",
            "🐘 PHP: `private(set)` properties enable asymmetric visibility in PHP 8.4+",
            "🐘 PHP: `array_is_list()` checks if array has consecutive integer keys from 0",
            "🐘 PHP: Use `enum` with backed values for type-safe constants",
            "🐘 PHP: `WeakMap` prevents memory leaks when storing object metadata",
            "🐘 PHP: Named arguments make function calls self-documenting and order-independent",
            "🐘 PHP: Constructor property promotion cuts boilerplate in half for value objects",
            "🐘 PHP: Fibers enable cooperative multitasking without callback hell",
            "🐘 PHP: `#[Override]` attribute catches broken parent method signatures at compile time",
            "🐘 PHP: `json_validate()` checks JSON without the overhead of decoding it",
            "🐘 PHP: First-class callable syntax `strlen(...)` creates closures from functions",
            "🐘 PHP: Intersection types (`Countable&Iterator`) enforce multiple interface contracts",
            "🐘 PHP: `array_any()` and `array_all()` replace verbose foreach validation loops",
            "🐘 PHP: Use `declare(strict_types=1)` in every file - type coercion hides bugs",
            "🐘 PHP: `DateTimeImmutable` over `DateTime` prevents accidental mutation side effects",

            // Symfony Tips
            "🎵 Symfony: Use `#[Route]` attributes instead of YAML for better IDE support",
            "🎵 Symfony: `ParamConverter` automatically converts request parameters to objects",
            "🎵 Symfony: Use `EventSubscriber` over `EventListener` for better organization",
            "🎵 Symfony: `Messenger` component handles async processing elegantly",
            "🎵 Symfony: Use `#[Cache]` attribute for automatic HTTP caching",
            "🎵 Symfony: `Serializer` with normalization contexts for flexible API responses",
            "🎵 Symfony: `#[AutowireLocator]` creates lazy service locators for optional deps",
            "🎵 Symfony: Use `#[AsMessageHandler]` attribute for clean message handler registration",
            "🎵 Symfony: `MapRequestPayload` attribute auto-deserializes JSON request bodies",
            "🎵 Symfony: Scheduler component replaces cron jobs with PHP-defined recurring tasks",

            // Vue.js Tips
            "🖖 Vue: `ref()` for primitives, `reactive()` for objects in Composition API",
            "🖖 Vue: `computed()` values are cached until dependencies change",
            "🖖 Vue: Use `watchEffect()` for side effects that depend on reactive data",
            "🖖 Vue: `defineEmits()` provides type-safe event emission in SFC",
            "🖖 Vue: `Suspense` component handles async components gracefully",
            "🖖 Vue: `teleport` renders components outside current component tree",
            "🖖 Vue: `defineModel()` simplifies two-way binding in custom components",
            "🖖 Vue: Use `shallowRef()` for large objects where deep reactivity is wasteful",
            "🖖 Vue: `provide/inject` with TypeScript generics gives type-safe dependency injection",
            "🖖 Vue: `v-memo` directive caches template sub-trees for list rendering performance",

            // React Tips
            "⚛️ React: Server Components render on the server with zero client-side JS bundle",
            "⚛️ React: `useOptimistic` provides instant UI feedback before server confirmation",
            "⚛️ React: `use()` hook unwraps promises and contexts with cleaner syntax",
            "⚛️ React: Server Actions handle form submissions without API route boilerplate",
            "⚛️ React: `React.memo()` prevents re-renders when props haven't changed",
            "⚛️ React: `useDeferredValue` keeps UI responsive during expensive re-renders",
            "⚛️ React: `useId()` generates stable unique IDs for SSR-compatible accessibility",
            "⚛️ React: Suspense boundaries let you show fallbacks while async content loads",

            // Nuxt Tips
            "💚 Nuxt: `useFetch()` with `key` option prevents redundant API calls on navigation",
            "💚 Nuxt: `definePageMeta()` sets layouts, middleware, and transitions per page",
            "💚 Nuxt: Server routes in `/server/api/` create full-stack endpoints instantly",
            "💚 Nuxt: `useAsyncData()` with `transform` shapes API responses before caching",
            "💚 Nuxt: Nitro server engine deploys to Cloudflare Workers, Vercel, or Node.js",
            "💚 Nuxt: `useState()` provides SSR-safe shared state without Pinia for simple cases",
            "💚 Nuxt: Route rules enable per-page rendering strategies - SSR, SPA, or prerendered",
            "💚 Nuxt: Auto-imports eliminate manual import statements for composables and utils",

            // CSS & Frontend Tips
            "🎨 CSS: `container-queries` adapt to parent size, not viewport size",
            "🎨 CSS: `aspect-ratio` property maintains proportions without padding hacks",
            "🎨 CSS: `:has()` pseudo-class enables parent selection based on children",
            "🎨 CSS: `grid-template-areas` makes complex layouts readable",
            "🎨 CSS: `clamp()` function provides responsive values without media queries",
            "🎨 CSS: `@layer` directive controls cascade order across stylesheets",
            "🎨 CSS: `color-mix()` creates dynamic color variations without preprocessors",
            "🎨 CSS: `@scope` rules limit styles to a DOM subtree without BEM naming",
            "🎨 Tailwind: v4 uses CSS-first config - no more `tailwind.config.js` needed",
            "🎨 CSS: `text-wrap: balance` creates visually even multi-line headings",

            // AI & LLM Tips
            "🤖 AI: Claude Code excels at complex multi-file refactoring with full codebase context",
            "🤖 AI: MCP servers let you extend AI assistants with custom tools and data sources",
            "🤖 AI: Use structured outputs (JSON mode) for reliable LLM API integrations",
            "🤖 AI: Cursor IDE's composer mode handles multi-file changes with AI precision",
            "🤖 AI: Prompt engineering tip - provide examples and constraints for consistent output",
            "🤖 AI: GitHub Copilot shines for boilerplate code and repetitive patterns",
            "🤖 AI: AI code review catches subtle bugs that manual review often misses",
            "🤖 AI: Chain AI agents for complex workflows - decompose, delegate, synthesize",
            "🤖 AI: Claude's extended thinking mode produces better architectural decisions",
            "🤖 AI: Use `.cursorrules` to teach Cursor your project conventions and patterns",
            "🤖 AI: MCP tool servers turn any API into an AI-accessible capability",
            "🤖 AI: AI pair programming is most effective when you review every suggestion critically",
            "🤖 AI: Use AI to generate test cases - it finds edge cases humans often overlook",
            "🤖 AI: Context window management is key - feed relevant code, not entire codebases",
            "🤖 AI: Combine Claude Code CLI with git hooks for AI-powered pre-commit checks",
            "🤖 AI: Local LLMs via Ollama are great for quick tasks without API latency",
            "🤖 AI: Claude Code's `/review` slash command provides instant PR-quality feedback",
            "🤖 AI: Use `CLAUDE.md` files to give AI persistent project context across sessions",
            "🤖 AI: MCP stdio transport is simpler; SSE transport scales for multi-user servers",
            "🤖 AI: Always version your system prompts - they're code, treat them like code",
            "🤖 AI: Claude's tool use lets you build AI agents that interact with real systems",
            "🤖 AI: RAG over your codebase gives AI assistants project-specific knowledge",
            "🤖 AI: Temperature 0 for deterministic tasks, higher for creative code generation",
            "🤖 AI: Cursor's `@codebase` mention indexes your entire project for context-aware help",
            "🤖 AI: Break complex AI tasks into smaller tool calls - reliability beats cleverness",
            "🤖 AI: Use AI to convert legacy PHP 5.x code to modern PHP 8.5 patterns",
            "🤖 AI: GitHub Copilot Workspace plans and implements features across multiple files",
            "🤖 AI: Claude Code subagents parallelize independent tasks for faster results",
            "🤖 AI: Prompt caching reduces API costs by 90% for repeated system prompts",
            "🤖 AI: Use AI-generated commit messages but always review them before pushing",
            "🤖 AI: MCP resources expose read-only data; tools expose actions - know the difference",
            "🤖 AI: Anthropic's batch API cuts costs in half for non-time-sensitive AI tasks",

            // Database & Architecture Tips
            "🗄️ DB: Use covering indexes to serve queries entirely from the index",
            "🗄️ DB: `EXPLAIN ANALYZE` reveals actual vs estimated query performance",
            "🗄️ DB: Partial indexes on frequently filtered columns save space and speed",
            "🗄️ DB: Use database-level `CHECK` constraints as a last line of validation",
            "🏛️ Arch: CQRS separates read and write models for independently scalable systems",
            "🏛️ Arch: Event sourcing stores state changes, not state - perfect audit trail",
            "🏛️ Arch: Strangler fig pattern migrates monoliths to microservices incrementally",

            // DevOps & Infrastructure Tips
            "🐳 Docker: Multi-stage builds keep production images lean and secure",
            "🐳 Docker: Use `--mount=type=cache` in builds to cache package manager downloads",
            "🐳 Docker: Compose `watch` mode hot-reloads containers during development",
            "🐳 Docker: Use health checks to prevent routing traffic to unready containers",
            "☸️ K8s: Horizontal Pod Autoscaler adjusts replicas based on CPU or custom metrics",
            "☸️ K8s: Init containers handle setup tasks before main containers start",
            "☸️ K8s: Use ConfigMaps for env-specific config, Secrets for sensitive data",
            "🔧 DevOps: Infrastructure as Code prevents configuration drift",
            "🔧 DevOps: Feature flags decouple deployment from release - ship dark, enable bright",
            "🔧 DevOps: Blue-green deployments enable zero-downtime releases with instant rollback",
            "🔧 DevOps: GitHub Actions matrix strategy tests across PHP versions in parallel",
            "🔧 DevOps: Use `act` to test GitHub Actions locally before pushing",
            "🔧 DevOps: Semantic versioning + conventional commits automate changelogs",
            "🔧 DevOps: GitLab CI `rules:` replaces `only/except` with flexible pipeline control",
            "☁️ Cloud: AWS Lambda + API Gateway runs PHP via Bref for serverless Laravel",
            "☁️ Cloud: Cloudflare Workers handle edge logic with sub-millisecond cold starts",

            // Security Tips
            "🔒 Security: Always sanitize user input, even from 'trusted' sources",
            "🔒 Security: Use `Content-Security-Policy` headers to prevent XSS attacks",
            "🔒 Security: Rotate API keys regularly and never commit them to version control",
            "🔒 Security: CORS headers should be as restrictive as possible - not `*`",
            "🔒 Security: Use `SameSite=Strict` cookies to prevent CSRF without tokens",
            "🔒 Security: Rate limiting on auth endpoints stops brute force and credential stuffing",

            // Performance & Testing Tips
            "⚡ Performance: Use database indexes on columns used in WHERE clauses",
            "⚡ Performance: HTTP/3 with QUIC reduces connection overhead for API-heavy apps",
            "⚡ Performance: Redis pipelines batch multiple commands in a single round-trip",
            "⚡ Performance: OPcache preloading eliminates file I/O for frequently used classes",
            "🧪 Testing: Write tests that verify behavior, not implementation details",
            "🧪 Testing: Pest's `arch()` tests enforce architectural rules automatically",
            "🧪 Testing: Use `Faker` sequences for deterministic yet realistic test data",
            "🧪 Testing: Mutation testing with Infection finds tests that pass but don't protect",
            "🧪 Testing: Playwright outperforms Selenium for modern E2E browser testing",
        ];
        
        return $tips[array_rand($tips)];
    }
}

/*
 * Invokable profile generator that handles both output modes
 */
readonly class ProfileGenerator
{
    public function __construct(
        private(set) object $svetoslav,
        private(set) DynamicContent $dynamic,
        private(set) string $readmePath = 'README.md',
        private(set) string $startMarker = '<!-- ### DAILY_UPDATE_START ### -->',
        private(set) string $endMarker = '<!-- ### DAILY_UPDATE_END ### -->'
    ) {}
    
    public function __invoke(bool $consoleOutput = false): void
    {
        if ($consoleOutput) {
            echo $this->generateConsoleContent();
        } else {
            $this->updateReadme($this->generateMarkdownContent());
        }
    }
    
    private function generateMarkdownContent(): string
    {
        $content = "## 🌟 Developer Status" . PHP_EOL . PHP_EOL;
        $content .= "*Last updated: " . (new DateTimeImmutable())->format('M j, Y \a\t H:i T') . "*" . PHP_EOL . PHP_EOL;
        
        $content .= "**📍 Location:** {$this->svetoslav->location}  " . PHP_EOL;
        $content .= "**⏱️ Experience:** " . number_format($this->dynamic->getExperienceDays()) . " days of coding  " . PHP_EOL;
        $content .= "**🎯 Current Focus:** {$this->dynamic->getCurrentFocus()}  " . PHP_EOL;
        $content .= "**🤖 AI Tools:** " . implode(', ', $this->svetoslav->ai->codingTools) . "  " . PHP_EOL;
        $content .= "**📅 Available:** {$this->svetoslav->availability->format('M j, Y')}  " . PHP_EOL . PHP_EOL;
        
        $content .= "### 🏗️ Architecture Approach" . PHP_EOL;
        $content .= "- **Scalability:** " . $this->svetoslav->backend->chooseArchitecture('scalability') . PHP_EOL;
        $content .= "- **Performance:** " . $this->svetoslav->backend->chooseArchitecture('performance') . PHP_EOL;
        $content .= "- **Default:** " . $this->svetoslav->backend->chooseArchitecture('default') . PHP_EOL . PHP_EOL;
        
        $content .= "### 💡 Today's Tech Tip" . PHP_EOL;
        $content .= "> " . $this->dynamic->getTechTip() . PHP_EOL . PHP_EOL;
        
        $content .= "*🤖 Generated by PHP " . PHP_VERSION . " • [View Source](profile-generator.php)*";
        
        return $content;
    }
    
    private function generateConsoleContent(): string
    {
        $output = '';
        $border = str_repeat("═", 60);
        $output .= "╔{$border}╗" . PHP_EOL;
        $output .= "║" . str_pad(" 🧙‍♂️ SVETOSLAV MUTEV - Senior Full-Stack Developer", 61) . "║" . PHP_EOL;
        $output .= "╚{$border}╝" . PHP_EOL . PHP_EOL;

        $output .= "📍 **Location:** {$this->svetoslav->location}" . PHP_EOL;
        $output .= "⏱️  **Experience:** " . number_format($this->dynamic->getExperienceDays()) . " days of crafting code" . PHP_EOL;
        $output .= "🎯 **Current Focus:** {$this->dynamic->getCurrentFocus()}" . PHP_EOL;
        $output .= "🤖 **AI Tools:** " . implode(', ', $this->svetoslav->ai->codingTools) . PHP_EOL;
        $output .= "📅 **Available:** {$this->svetoslav->availability->format('M j, Y')}" . PHP_EOL . PHP_EOL;

        $output .= "🏗️  **Architecture Expertise:**" . PHP_EOL;
        $output .= "   • Scalability: " . $this->svetoslav->backend->chooseArchitecture('scalability') . PHP_EOL;
        $output .= "   • Performance: " . $this->svetoslav->backend->chooseArchitecture('performance') . PHP_EOL;
        $output .= "   • Default: " . $this->svetoslav->backend->chooseArchitecture('default') . PHP_EOL . PHP_EOL;

        $frontendStack = $this->svetoslav->frontend->getPreferredStack();
        $output .= "🎨 **Frontend Stack:**" . PHP_EOL;
        $output .= "   • Frameworks: " . implode(', ', $frontendStack['frameworks']) . PHP_EOL;
        $output .= "   • Styling: " . implode(', ', $frontendStack['styling']) . PHP_EOL;
        $output .= "   • Philosophy: " . $frontendStack['philosophy'] . PHP_EOL . PHP_EOL;

        $devopsWorkflow = $this->svetoslav->devOp->getWorkflow();
        $output .= "🚀 **DevOps Workflow:**" . PHP_EOL;
        $output .= "   • Containers: " . $devopsWorkflow['containers'] . PHP_EOL;
        $output .= "   • CI/CD: " . $devopsWorkflow['cicd'] . PHP_EOL;
        $output .= "   • Cloud: " . $devopsWorkflow['cloud'] . PHP_EOL . PHP_EOL;

        $output .= "💡 **Today's Tech Tip:**" . PHP_EOL;
        $output .= "   " . $this->dynamic->getTechTip() . PHP_EOL . PHP_EOL;

        $output .= "📜 **Development Principles:**" . PHP_EOL;
        $principles = [
            'Clean Code > Clever Code',
            'Balance Business Needs with Technical Excellence', 
            'Performance Matters, Security by Design'
        ];
        foreach ($principles as $principle) {
            $output .= "   • $principle" . PHP_EOL;
        }

        $output .= PHP_EOL . str_repeat("─", 60) . PHP_EOL;
        $output .= "Generated on: " . (new DateTimeImmutable())->format('D, M j Y \a\t H:i:s T') . PHP_EOL;
        $output .= "🤖 Powered by PHP " . PHP_VERSION . " with modern features" . PHP_EOL;
        
        return $output;
    }
    
    private function updateReadme(string $newContent): void
    {
        if (!file_exists($this->readmePath)) {
            echo "❌ README.md not found!" . PHP_EOL;
            return;
        }
        
        $readme = file_get_contents($this->readmePath);
        
        $startPos = strpos($readme, $this->startMarker);
        $endPos = strpos($readme, $this->endMarker);
        
        if ($startPos === false || $endPos === false) {
            echo "❌ Markers not found in README.md!" . PHP_EOL;
            return;
        }
        
        $before = substr($readme, 0, $startPos + strlen($this->startMarker));
        $after = substr($readme, $endPos);
        
        $updatedReadme = $before . PHP_EOL . $newContent . PHP_EOL . $after;

        echo file_put_contents($this->readmePath, $updatedReadme) ?
            "✅ README.md updated successfully!" . PHP_EOL : "❌ Failed to write README.md!" . PHP_EOL;
    }
}

$dynamic = new DynamicContent();
$generator = new ProfileGenerator($svetoslav, $dynamic);

$generator(in_array('--output', $argv ?? []));