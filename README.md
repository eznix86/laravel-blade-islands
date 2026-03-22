# Blade Islands For Laravel

Server-side Blade directives for React, Vue, and Svelte islands in Laravel.

This package is the Laravel half of Blade Islands. It renders the HTML contract in Blade. The client-side runtime lives in the separate npm package [`blade-islands`](https://github.com/eznix86/blade-islands).

## What Is This?

This mounts JS framework components on top of Laravel Blade templates by rendering lightweight island placeholders.

It provides Blade directives for:

- `@react`
- `@vue`
- `@svelte`

Those placeholders contain the metadata the frontend runtime needs to mount the matching component on the client.

## Quick Start

Install the package:

```bash
composer require eznix86/blade-islands
```

Use a directive in Blade:

```php
@react('Dashboard', ['user' => ['name' => 'Bruno']])
```

Pair it with the JS runtime in `resources/js/app.js`:

```js
npm install blade-islands react react-dom

import islands from 'blade-islands/react'

islands()
```

If you use React islands in development, add the React refresh preamble to your Blade layout:

```php
<head>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

## Extensive Usage

### Directives

```php
@react('Dashboard', ['user' => ['name' => 'Bruno']])
@vue('Support/Map', ['lat' => 48.85, 'lng' => 2.35])
@svelte('Cart/Drawer', ['count' => 3])
```

### Blade Layout Setup

Use the normal Laravel Vite tags in your layout:

```php
<head>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

`@viteReactRefresh` is required for React islands in development. Vue and Svelte do not need an extra Blade directive, but their Vite plugins still need to be installed on the JS side.

### API

Each directive accepts up to four arguments:

```php
@react($component, $props = [], $preserve = false, $key = null)
```

- `$component` - component name relative to the JS component root
- `$props` - props encoded into `data-props`
- `$preserve` - marks the island as preserved so the runtime can skip remounting the same DOM node
- `$key` - optional stable key for repeated preserved islands

### Named Arguments

Named arguments are supported:

```php
@react(
    component: 'Dashboard',
    props: ['user' => ['name' => 'Bruno']],
    preserve: true,
    key: 'dashboard-main',
)
```

### Nested Components

Nested folders are supported:

```php
@vue('Support/Map', ['lat' => 48.85, 'lng' => 2.35])
```

which matches:

```text
resources/js/islands/Support/Map.vue
```

### Component Preservation

```php
@react('Support/Map', [...], preserve: true)
@vue('Support/Map', [...], preserve: true)
@svelte('Support/Map', [...], preserve: true)
```

This skips remounting that same DOM node on later boot passes.

This is useful to prevent re-renders of components.

Use preserve for singleton-style islands:

```php
@react('Dashboard', ['user' => $user], true)
```

Use an explicit key for repeated preserved islands:

```php
@react('Product/Card', ['product' => $product], true, "product-{$product->id}")
```

If you omit the key, the package falls back to a stable key built from the island type and component name, like `react:dashboard`.

### Custom Root

This package does not resolve filesystem paths itself, but it works with custom roots configured in the JS runtime.

For example, if the JS side uses:

```js
npm install blade-islands vue

import islands from 'blade-islands/vue'

islands({
  root: '/resources/js/widgets',
  components: import.meta.glob('/resources/js/widgets/**/*.vue'),
})
```

then this Blade call:

```php
@vue('Dashboard', [...])
```

resolves to:

```text
resources/js/widgets/Dashboard.vue
```

### Vite Plugins

The JS runtime repo documents the required Vite plugins:

- `@vitejs/plugin-react` for React
- `@vitejs/plugin-vue` for Vue
- `@sveltejs/vite-plugin-svelte` for Svelte

### Rendered Output

```html
<div
  data-island="react"
  data-component="Dashboard"
  data-props="{&quot;user&quot;:{&quot;name&quot;:&quot;Bruno&quot;}}"
  data-preserve="true"
  data-key="react:dashboard"
></div>
```

## Testing

```bash
composer install
composer test
```

## Contributing

Contributions are welcome.

Recommended workflow:

1. Fork the repository
2. Create a focused branch
3. Add or update tests
4. Run `composer test`
5. Open a pull request with a clear summary

## Companion Package

This package only renders Blade output. Use it with the separate runtime package:

- npm package: `blade-islands`
- repository: `blade-islands`

## Requirements

- PHP 8.3+
- Laravel 13+

## License

MIT
