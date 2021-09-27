<?php
namespace Hxc\ToolLaravel\Facades;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class HxcLaravelTool extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return "HxcLaravelTool";
    }
}
