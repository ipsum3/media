<?php

namespace Ipsum\Media;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{

    protected $commands = [
        \Ipsum\Media\app\Console\Commands\Install::class,
    ];

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;



    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViews();

        $this->publishFiles();

    }


    public function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/ressources/views', 'IpsumMedia');
    }



    public function publishFiles()
    {
        $this->publishes([
            __DIR__.'/ressources/views' => resource_path('views/ipsum/media'),
        ], 'views');

        $this->publishes([
            __DIR__.'/config' => config_path(),
        ], 'install');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/config/ipsum/media.php', 'ipsum.media'
        );

        $this->mergeConfigFrom(
            __DIR__.'/config/croppa.php', 'croppa'
        );

        app()->config["filesystems.disks.uploads"] = [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'url' => env('APP_URL').'/uploads',
            'visibility' => 'public',
            'throw' => false,
        ];

        app()->config["filesystems.disks.crops"] = [
            'driver' => 'local',
            'root' => public_path('uploads/crops'),
            'url' => env('APP_URL').'/uploads/crops',
            'visibility' => 'public',
            'throw' => false,
        ];

        // register the artisan commands
        $this->commands($this->commands);
    }
}
