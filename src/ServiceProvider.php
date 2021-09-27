<?php
namespace Hxc\HxcLaravelTool;

use Hxc\HxcLaravelTool\Commands\Curd;
use Hxc\HxcLaravelTool\Commands\MakeRepository;
use Hxc\HxcLaravelTool\Controllers\TestController;
use \Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Configs/hxc.php' => config_path('hxc.php')
        ],'hxc-config');
        $this->publishes([
            __DIR__.'/Routes/api.php.php' => base_path('routes/api.php')
        ],'hxc-routes');
        $this->publishes([
            __DIR__.'/Helpers/Functions.php' => app_path('Helpers/Functions.php')
        ],'hxc-functions');
    }

    public function register()
    {
        $this->app->singleton('HxcLaravelTool',function ($app){
            return new TestController();
        });

        if($this->app->runningInConsole()){
            $this->commands([
                Curd::class,
                MakeRepository::class
            ]);
        }
    }
}
