<?php

namespace Rsvpify\LaravelInky;

use Illuminate\Support\ServiceProvider;

class InkyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerExtension();

        $this->publishes([
            __DIR__ . '/config/inky.php' => config_path('inky.php'),
        ]);
    }

    public function register(): void
    {
        $app = $this->app;
        $resolver = $app['view.engine.resolver'];

        $app->singleton('inky.compiler', function ($app) {
            $cache = $app['config']['view.compiled'];

            return new InkyCompiler($app['blade.compiler'], $app['files'], $cache);
        });

        $resolver->register('inky', fn () => new InkyCompilerEngine($app['inky.compiler'], $app['files']));
    }

    protected function registerExtension(): void
    {
        $this->app['view']->addExtension('inky.php', 'inky');
    }
}
