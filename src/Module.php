<?php
namespace Yakisova41\ModuleLoader;

class Module
{
    public static  $modules = [];
    
    public static function import($moduleImportName, $modulePath)
    {
        self::ErrorHandle();

        $imports = [];
        
        foreach($moduleImportName as $Key  => $moduleName)
        {

            if(preg_match("/^{.*}$/", $moduleName))
            {
                preg_match('/^{(\w+).*}$/',$moduleName, $moduleName);
                $imports[] = ['type'=>2,'name'=>$moduleName[1]];
            }
            else
            {
                $imports[] = ['type'=>1,'name'=>$moduleName];
            }
        }

        self::moduleFileRequire($modulePath);

        $modules = self::searchModules($imports);
        self::setGlobalVar($modules);

        return $modules;
    }

    private static function moduleFileRequire($modulePath)
    {
        $backtrace = debug_backtrace();
        $importFilePath = $backtrace[count($backtrace) - 1]['file'];
        $importFileDir = dirname($importFilePath);

        if(!preg_match('/^.*\.php$/',$modulePath))
        {
            $modulePath = $modulePath.'.php';
        }

        $requirePath = rtrim($importFileDir, '\\/') . DIRECTORY_SEPARATOR . rtrim($modulePath, '\\/');

        if(file_exists($requirePath))
        {
            require_once $requirePath;
        }
        else
        {
            trigger_error("Module file \"$requirePath\" not found", E_USER_WARNING);
        }
        
    }

    private static function searchModules($imports)
    {
        $result = [];

        foreach($imports as $key => $import)
        {
            if($import['type'] === 2)
            {
                if(isset(self::$modules[$import['name']]))
                {
                    $match = self::$modules[$import['name']];
                    $result[] = [
                        'type'=>2,
                        'name'=>$import['name'],
                        'callback'=>$match['callback']
                    ];
                }
                else
                {
                    trigger_error("Named module \"".$import['name']."\" not found", E_USER_WARNING);
                }
            }
            else if($import['type'] === 1)
            {
                if(isset(self::$modules['[Yakisova41/ModuleLoader]default']))
                {
                    $match = self::$modules['[Yakisova41/ModuleLoader]default'];
                    $result[] = [
                        'type'=>1,
                        'name'=>$import['name'],
                        'callback'=>$match['callback']
                    ];
                }
            }
        }

        return $result;
    }

    private static function setGlobalVar($modules)
    {
        foreach($modules as $module)
        {
            $GLOBALS[$module['name']] = $module['callback'];
        }
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