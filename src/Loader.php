<?php
namespace Yakisova41\ModuleLoader;

class Loader
{
    private static $module = [];

    public static function import($modulePath, $moduleName = false)
    {
        self::ErrorHandle();

        if(file_exists($modulePath))
        {
            require_once($modulePath);
        }
        else
        {
            $escaped_path = htmlspecialchars($modulePath);
            trigger_error("File \"$escaped_path\" not found", E_USER_WARNING);

            return null;
        }

        $module = self::$module;
        self::$module = [];

        if(!$moduleName)
        {
            if(isset($module['default']))
            {
                return $module['default'];
            }
            else
            {
                trigger_error("Default module not found in imported file", E_USER_WARNING);
                return null;
            }
        }
        else
        {
            if(isset($module[$moduleName]))
            {
                return $module[$moduleName];
            }
            else
            {
                $escaped_modulename = htmlspecialchars($moduleName);
                trigger_error("Named module \"$escaped_modulename\" not found", E_USER_WARNING);
                return null;
            }    
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

    private static function ErrorHandle()
    {
        set_error_handler(function($errno, $errstr){
            if (!(error_reporting() & $errno))
            {
                return;
            }

            $errstr = htmlspecialchars($errstr);
            $backtraces = debug_backtrace();
            $lastbacktrace = $backtraces[array_key_last($backtraces)];

            $errfile = $lastbacktrace['file'];
            $errline = $lastbacktrace['line'];

            switch ($errno)
            {
                case E_USER_WARNING:
                    echo "<br /><b>ModuleLoader Warning</b>: $errstr in <b>$errfile</b> on line <b>$errline</b><br />\n";
                    break;
                case E_USER_ERROR:
                    echo "<br /><b>ModuleLoader Error</b>: $errstr in <b>$errfile</b> on line <b>$errline</b><br />\n";
                    break;
            }
        });
    }
}
