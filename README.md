# 🏝️ Blade Islands For Laravel

<p align="center">
  <img src="art/header.png" alt="Blade Islands for Laravel" width="1024">
</p>

[![Latest Stable Version](https://img.shields.io/packagist/v/eznix86/blade-islands)](https://packagist.org/packages/eznix86/blade-islands)
[![Total Downloads](https://img.shields.io/packagist/dt/eznix86/blade-islands)](https://packagist.org/packages/eznix86/blade-islands)
[![License](https://img.shields.io/packagist/l/eznix86/blade-islands)](LICENSE)

Server-side Blade directives for React, Vue, and Svelte islands in Laravel.

Blade Islands lets you render small React, Vue, or Svelte components inside Laravel Blade views without turning your application into a full single-page app.

This package provides the Blade directives and HTML output. The browser runtime lives in the npm package [`blade-islands`](https://github.com/eznix86/blade-islands).

## Contents

- [Why Blade Islands?](#why-blade-islands)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Available Directives](#available-directives)
- [How It Works](#how-it-works)
- [Vite Setup](#vite-setup)
- [Component Resolution](#component-resolution)
- [Custom Root](#custom-root)
- [Preserve Mounted Islands](#preserve-mounted-islands)
- [Options](#options)
- [Protocol](#protocol)
- [Requirements](#requirements)
- [Companion Package](#companion-package)
- [Blade Islands vs X](#blade-islands-vs-x)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Why Blade Islands?

Blade Islands works well when your application is mostly server-rendered but still needs interactive UI in places such as:

- search inputs
- dashboards
- maps
- counters
- filters
- dialogs

Instead of building entire pages in a frontend framework, you can keep Blade as your primary rendering layer and hydrate only the parts of the page that need JavaScript.

## Installation

Install the Laravel package:

```bash
composer require eznix86/blade-islands
```

Then install the browser runtime, your frontend framework, and the matching Vite plugin.

### React

```bash
npm install blade-islands react react-dom @vitejs/plugin-react
```

### Vue

```bash
npm install blade-islands vue @vitejs/plugin-vue
```

### Svelte

```bash
npm install blade-islands svelte @sveltejs/vite-plugin-svelte
```

## Quick Start

Add the runtime to `resources/js/app.js`, load that entry from your Blade layout, and render an island from Blade.

### React

`resources/js/app.js`

```js
import islands from 'blade-islands/react'

islands()
```

Blade layout:

```php
<head>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

```php
@react('ProfileCard', ['user' => $user])
```

This mounts `resources/js/islands/ProfileCard.jsx`.

### Vue

`resources/js/app.js`

```js
import islands from 'blade-islands/vue'

islands()
```

Blade layout:

```php
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

```php
@vue('ProfileCard', ['user' => $user])
```

This mounts `resources/js/islands/ProfileCard.vue`.

### Svelte

`resources/js/app.js`

```js
import islands from 'blade-islands/svelte'

islands()
```

Blade layout:

```php
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

```php
@svelte('ProfileCard', ['user' => $user])
```

This mounts `resources/js/islands/ProfileCard.svelte`.

## Available Directives

Blade Islands provides three directives:

```php
@react('Dashboard', ['user' => $user])
@vue('Support/TicketList', ['tickets' => $tickets])
@svelte('Cart/Drawer', ['count' => $count])
```

## How It Works

Blade Islands has two parts:

- this package renders island placeholders from Blade
- the npm runtime scans the DOM and mounts the matching frontend component

For example:

```php
@react('Account/UsageChart', ['stats' => $stats])
```

renders the metadata needed to mount:

```text
resources/js/islands/Account/UsageChart.jsx
```

## Vite Setup

Register the plugin for the framework you use.

### React

```js
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
})
```

If your Laravel layout loads a React entrypoint in development, include:

```php
@viteReactRefresh
```

### Vue

```js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
})
```

### Svelte

```js
import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'

export default defineConfig({
  plugins: [svelte()],
})
```

## Component Resolution

By default, the runtime looks for components in `resources/js/islands`.

Nested folders work automatically. For example:

```php
@vue('Billing/Invoices/Table', [...])
```

resolves to:

```text
resources/js/islands/Billing/Invoices/Table.vue
```

## Custom Root

This package does not resolve filesystem paths itself, but it works with custom roots configured in the browser runtime.

For example, if your frontend entry uses:

```js
import islands from 'blade-islands/vue'

islands({
  root: '/resources/js/widgets',
  components: import.meta.glob('/resources/js/widgets/**/*.vue'),
})
```

Then this Blade call:

```php
@vue('Dashboard', [...])
```

mounts:

```text
resources/js/widgets/Dashboard.vue
```

## Preserve Mounted Islands

Use `preserve: true` when the same DOM is processed more than once and you want Blade Islands to keep an existing island mounted instead of mounting it again.

This is useful when the page or a DOM fragment is recalculated and your frontend boot logic runs again.

```php
@react('Dashboard/RevenueChart', ['stats' => $stats], preserve: true)
@vue('Dashboard/RevenueChart', ['stats' => $stats], preserve: true)
@svelte('Dashboard/RevenueChart', ['stats' => $stats], preserve: true)
```

If you reuse a preserved component in a loop, pass a unique `key` so each island keeps its own identity:

```php
@foreach ($products as $product)
    @react('Product/Card', ['product' => $product], preserve: true, key: "product-{$product->id}")
@endforeach
```

## Options

Each directive accepts up to four arguments:

```php
@react($component, $props = [], $preserve = false, $key = null)
```

- `$component` - component name relative to the JavaScript component root
- `$props` - props encoded into the rendered HTML
- `$preserve` - keeps an existing island mounted when the same DOM is processed again
- `$key` - unique key for distinguishing repeated preserved islands

Named arguments are supported:

```php
@react(
    component: 'Dashboard',
    props: ['user' => $user],
    preserve: true,
    key: 'dashboard-main',
)
```

## Protocol

Blade Islands renders lightweight placeholders like:

```html
<div
  data-island="react"
  data-component="Dashboard"
  data-props="{&quot;user&quot;:{&quot;name&quot;:&quot;Bruno&quot;}}"
  data-preserve="true"
  data-key="react:dashboard"
></div>
```

## Requirements

- PHP 8.3+
- Laravel 13+

## Companion Package

Use this package with the separate browser runtime:

- npm package: `blade-islands`
- repository: `blade-islands`

## Blade Islands vs X

### Inertia.js

Inertia is a better fit when your application wants React, Vue, or Svelte to render full pages with a JavaScript-first page architecture.

Blade Islands is a better fit when your application is already Blade-first and you want to keep server-rendered pages while hydrating only selected components.

### MingleJS

MingleJS is often used in Laravel applications that embed React or Vue components, especially in Livewire-heavy codebases.

Blade Islands is more naturally suited to Blade-first applications that want progressive enhancement with minimal architectural change. It does not depend on Livewire, and it may also be used alongside Livewire when that fits your application.

### Laravel UI

Laravel UI is a legacy scaffolding package for frontend presets and authentication views.

Blade Islands solves a different problem: adding targeted client-side interactivity to server-rendered Blade pages.

## Testing

```bash
composer install
composer test
```

## Contributing

Contributions are welcome.

1. Fork the repository
2. Create a focused branch
3. Add or update tests
4. Run `composer test`
5. Open a pull request with a clear summary

## License

MIT
