<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders a react island placeholder', function (): void {
    $html = Blade::render("@react('Dashboard', ['user' => ['name' => 'Bruno']])");

    expect($html)->toContain('data-island="react"')
        ->toContain('data-component="Dashboard"')
        ->toContain('&quot;name&quot;:&quot;Bruno&quot;');
});

it('renders nested component names', function (): void {
    $html = Blade::render("@vue('Support/Map', ['lat' => 48.85, 'lng' => 2.35])");

    expect($html)->toContain('data-island="vue"')
        ->toContain('data-component="Support/Map"')
        ->toContain('&quot;lat&quot;:48.85');
});

it('renders preserved islands with a fallback key', function (): void {
    $html = Blade::render("@svelte('CartDrawer', ['count' => 2], true)");

    expect($html)->toContain('data-preserve="true"')
        ->toContain('data-key="svelte:cartdrawer"');
});

it('renders preserved islands with an explicit key', function (): void {
    $html = Blade::render("@react('ProductCard', ['product' => ['id' => 10]], true, 'product-10')");

    expect($html)->toContain('data-preserve="true"')
        ->toContain('data-key="product-10"');
});

it('supports named directive arguments', function (): void {
    $html = Blade::render("@react(component: 'Dashboard', props: ['user' => ['name' => 'Bruno']], preserve: true, key: 'dashboard-main')");

    expect($html)->toContain('data-component="Dashboard"')
        ->toContain('data-preserve="true"')
        ->toContain('data-key="dashboard-main"')
        ->toContain('&quot;name&quot;:&quot;Bruno&quot;');
});
