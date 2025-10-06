<?php

namespace Nur\Pathao;

use Illuminate\Support\ServiceProvider;
use Nur\Pathao\Services\PathaoService;

class PathaoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/pathao.php', 'pathao');

        $this->app->singleton('pathao', function () {
            return new PathaoService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/pathao.php' => config_path('pathao.php'),
        ], 'pathao-config');
    }
}
