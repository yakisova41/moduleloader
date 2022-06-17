<?php
namespace Yakisova41\ModuleLoader;

class Loader
{
    private static $module = [];

    public static function import($modulePath, $moduleName = false)
    {
        require_once($modulePath);
        $module = self::$module;
        self::$module = [];

        if(!$moduleName){
            return $module['default'];
        }
        else{
            return $module[$moduleName];
        }
    }

    public static function exportDefault($callback)
    {
        self::$module['default'] = $callback;
    }

    public static function export($name, $callback)
    {
        self::$module[$name] = $callback; 
    }
}