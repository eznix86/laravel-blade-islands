<?php

namespace Eznix86\BladeIslands\Tests;

use Eznix86\BladeIslands\BladeIslandsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [BladeIslandsServiceProvider::class];
    }
}
