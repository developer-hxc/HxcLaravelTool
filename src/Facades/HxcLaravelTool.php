<?php
namespace Hxc\HxcLaravelTool\Facades;

use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 *  @method static mixed index()
 */
class HxcLaravelTool extends LaravelFacade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return "HxcLaravelTool";
    }
}
