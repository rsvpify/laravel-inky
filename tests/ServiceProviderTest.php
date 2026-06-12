<?php

namespace Rsvpify\Tests\LaravelInky;

use Rsvpify\LaravelInky\InkyCompiler;
use Rsvpify\LaravelInky\InkyCompilerEngine;

class ServiceProviderTest extends AbstractTestCase
{
    public function testRegistersInkyCompiler()
    {
        $this->assertTrue($this->app->bound('inky.compiler'));
        $this->assertInstanceOf(InkyCompiler::class, $this->app->make('inky.compiler'));
    }

    public function testRegistersInkyEngine()
    {
        $resolver = $this->app->make('view.engine.resolver');

        $this->assertInstanceOf(InkyCompilerEngine::class, $resolver->resolve('inky'));
    }
}
