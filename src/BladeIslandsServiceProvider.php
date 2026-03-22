<?php

declare(strict_types=1);

namespace Eznix86\BladeIslands;

use Eznix86\BladeIslands\Support\IslandRenderer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Override;

class BladeIslandsServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(IslandRenderer::class);
    }

    public function boot(): void
    {
        foreach (['react', 'vue', 'svelte'] as $framework) {
            Blade::directive($framework, fn (string $expression): string => $this->renderIsland($framework, $expression));
        }
    }

    private function renderIsland(string $framework, string $expression): string
    {
        return "<?php echo app('".IslandRenderer::class."')->renderDirective('{$framework}', {$expression}); ?>";
    }
}
