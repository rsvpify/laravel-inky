<?php

namespace Rsvpify\Tests\LaravelInky;

use Rsvpify\LaravelInky\InkyServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            InkyServiceProvider::class,
        ];
    }
}
