<?php

declare(strict_types=1);

namespace Eznix86\BladeIslands\Support;

use Illuminate\Support\HtmlString;
use InvalidArgumentException;

use function throw_if;
use function throw_unless;

class IslandRenderer
{
    public function renderDirective(string $framework, mixed ...$arguments): HtmlString
    {
        $component = $arguments['component'] ?? $arguments[0] ?? null;
        $props = $arguments['props'] ?? $arguments[1] ?? [];
        $preserve = $arguments['preserve'] ?? $arguments[2] ?? false;
        $key = $arguments['key'] ?? $arguments[3] ?? null;

        throw_if(! is_string($component) || $component === '', InvalidArgumentException::class, 'Blade islands require a component name.');
        throw_unless(is_array($props), InvalidArgumentException::class, 'Blade islands props must be an array.');

        return $this->render($framework, $component, $props, (bool) $preserve, $key);
    }

    public function render(
        string $framework,
        string $component,
        array $props = [],
        bool $preserve = false,
        ?string $key = null,
    ): HtmlString {
        $attributes = [
            'data-island' => $framework,
            'data-component' => $component,
            'data-props' => htmlspecialchars(json_encode($props, JSON_THROW_ON_ERROR), ENT_QUOTES, 'UTF-8', false),
        ];

        if ($preserve) {
            $attributes['data-preserve'] = 'true';
            $attributes['data-key'] = $this->normalizeKey($framework, $component, $key);
        }

        $html = '<div';

        foreach ($attributes as $name => $value) {
            $html .= sprintf(' %s="%s"', $name, $value);
        }

        $html .= '></div>';

        return new HtmlString($html);
    }

    private function normalizeKey(string $framework, string $component, ?string $key): string
    {
        if ($key !== null && $key !== '') {
            return $key;
        }

        return mb_strtolower(sprintf('%s:%s', $framework, $component));
    }
}
