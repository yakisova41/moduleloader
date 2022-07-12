<?php
namespace Yakisova41\ModuleLoader;
use Yakisova41\ModuleLoader\Module;

class Export
{
    public static function default($callback)
    {
        Module::$modules['[Yakisova41/ModuleLoader]default'] = [
            'type'=>1,
            'callback'=>$callback,
        ];
    }

    public static function export($name, $callback)
    {
        Module::$modules[$name] = [
            'type'=>2,
            'callback'=>$callback
        ];
    }

}