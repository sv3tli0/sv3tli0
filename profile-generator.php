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
                '🌿 Exploring Bulgarian nature reserves'
            ];
            return $weekendFocuses[array_rand($weekendFocuses)];
        }
        
        $workFocuses = [
            'Deep diving into Laravel 12 performance optimizations',
            'Mastering Vue 3 Composition API with TypeScript',
            'Exploring Symfony 7 new features and improvements',
            'Docker multi-stage builds for PHP applications',
            'Microservices communication patterns with RabbitMQ',
            'Modern CSS Grid and Container Queries implementation',
            'API security with JWT and OAuth2 best practices',
            'Laravel Octane for high-performance applications',
            'Vue.js SSR with Nuxt.js architecture patterns',
            'Symfony Messenger for async task processing',
            'PHP 8.4 property hooks and new features',
            'Event-driven architecture with Laravel Events',
            'Building custom MCP servers for AI-enhanced workflows',
            'Claude Code sessions for complex refactoring tasks',
            'Integrating LLM APIs into production applications',
            'AI agent orchestration for automated development pipelines',
            'Cursor IDE AI-assisted pair programming sessions',
            'Prompt engineering for reliable code generation',
            'AI-powered code review and quality analysis',
            'Building intelligent development automation tools'
        ];
        
        $dayOfYear = (int)$this->currentDate->format('z');
        return $workFocuses[$dayOfYear % count($workFocuses)];
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
            
            // PHP Tips  
            "🐘 PHP: `readonly` classes are perfect for DTOs and value objects",
            "🐘 PHP: Use `match()` expressions - they're stricter than `switch` statements",
            "🐘 PHP: `private(set)` properties enable asymmetric visibility in PHP 8.4",
            "🐘 PHP: `array_is_list()` checks if array has consecutive integer keys from 0",
            "🐘 PHP: Use `enum` with backed values for type-safe constants",
            "🐘 PHP: `WeakMap` prevents memory leaks when storing object metadata",
            
            // Symfony Tips
            "🎵 Symfony: Use `#[Route]` attributes instead of YAML for better IDE support",
            "🎵 Symfony: `ParamConverter` automatically converts request parameters to objects",
            "🎵 Symfony: Use `EventSubscriber` over `EventListener` for better organization",
            "🎵 Symfony: `Messenger` component handles async processing elegantly",
            "🎵 Symfony: Use `#[Cache]` attribute for automatic HTTP caching",
            "🎵 Symfony: `Serializer` with normalization contexts for flexible API responses",
            
            // Vue.js Tips
            "🖖 Vue: `ref()` for primitives, `reactive()` for objects in Composition API",
            "🖖 Vue: `computed()` values are cached until dependencies change",
            "🖖 Vue: Use `watchEffect()` for side effects that depend on reactive data",
            "🖖 Vue: `defineEmits()` provides type-safe event emission in SFC",
            "🖖 Vue: `Suspense` component handles async components gracefully",
            "🖖 Vue: `teleport` renders components outside current component tree",
            
            // CSS Tips
            "🎨 CSS: `container-queries` adapt to parent size, not viewport size",
            "🎨 CSS: `aspect-ratio` property maintains proportions without padding hacks",
            "🎨 CSS: `:has()` pseudo-class enables parent selection based on children",
            "🎨 CSS: `grid-template-areas` makes complex layouts readable",
            "🎨 CSS: `clamp()` function provides responsive values without media queries",
            "🎨 CSS: `@layer` directive controls cascade order across stylesheets",
            
            // AI & LLM Tips
            "🤖 AI: Claude Code excels at complex multi-file refactoring with full codebase context",
            "🤖 AI: MCP servers let you extend AI assistants with custom tools and data sources",
            "🤖 AI: Use structured outputs (JSON mode) for reliable LLM API integrations",
            "🤖 AI: Cursor IDE's composer mode handles multi-file changes with AI precision",
            "🤖 AI: Prompt engineering tip - provide examples and constraints for consistent output",
            "🤖 AI: GitHub Copilot shines for boilerplate code and repetitive patterns",
            "🤖 AI: AI code review catches subtle bugs that manual review often misses",
            "🤖 AI: Chain AI agents for complex workflows - decompose, delegate, synthesize",

            // General Tips
            "🔒 Security: Always sanitize user input, even from 'trusted' sources",
            "⚡ Performance: Use database indexes on columns used in WHERE clauses",
            "🐳 Docker: Multi-stage builds keep production images lean and secure",
            "🔧 DevOps: Infrastructure as Code prevents configuration drift",
            "🧪 Testing: Write tests that verify behavior, not implementation details"
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
        $content = "## 🌟 Daily Developer Status" . PHP_EOL . PHP_EOL;
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