<?php
namespace Hxc\ToolLaravel;
use Hxc\ToolLaravel\Controller\TestController;
use \Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    protected $defer = true;

    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton('HxcLaravelTool',function ($app){
            return new TestController();
        });
    }
}
