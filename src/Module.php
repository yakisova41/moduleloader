<?php
namespace Yakisova41\ModuleLoader;

class Module
{
    private static $modulePath;
    private static $importMode;
    private static $moduleName;
    private static $modules = [];
    private static $styles = "";
    private static $classes = [];
    
    public static function import(
        $modulePath,
        $moduleName = false
    )
    {
        self::ErrorHandle();

        if(file_exists($modulePath.'.php'))
        {
            self::$modulePath = $modulePath;
            self::$importMode = 'module';
        }
        else if(file_exists($modulePath.'.style.json'))
        {
            self::$modulePath = $modulePath;
            self::$importMode = 'style';
        }
        else
        {
            trigger_error("File \"$modulePath\" not found", E_USER_WARNING);
            return;
        }

        if(self::$importMode === 'module')
        {
            if($moduleName)
            {
                self::$moduleName = $moduleName;
            }
            else
            {
                self::$moduleName = 'default';
            }

            return self::moduleImport($modulePath.'.php');
        }
        else if(self::$importMode === 'style')
        {
            return self::styleImport($modulePath.'.style.json');
        }
    }

    public static function exportDefault(
        $callBack
    )
    {
        self::$modules['default'] = $callBack;
    }

    public static function export(
        $callBack,
        $moduleName
    )
    {
        self::$modules[$moduleName] = $callBack;
    }

    public static function style()
    {
        return "<style>".self::$styles."</style>";
    }

    private static function moduleImport(
        $modulePath
    )
    {
        if(file_exists($modulePath))
        {
            require($modulePath);

            if(isset(self::$modules[self::$moduleName]))
            {
                return self::$modules[self::$moduleName];
            }

            trigger_error(self::$moduleName." module not found in imported file", E_USER_WARNING);
            return;
        }
    }

    private static function styleImport(
        $stylePath
    )
    {
        $styleArray = \json_decode(\file_get_contents($stylePath));
        $styleStr = "";
        $classes = [];
        $last = "";

        foreach($styleArray as $styleSelector => $stylePropatys)
        {  
            if($styleSelector === 'media-query')
            {
                $styleStr = $styleStr.$stylePropatys."{".$styleStr;
                $last = $last.'}';
            }
            else
            {
                $hash = self::ramdom(15);
                self::$classes[$styleSelector] = $hash;
                $styleSelector = $hash;

                $styles = "";
                foreach($stylePropatys as $stylePropaty => $value)
                {
                    $styles = $styles.$stylePropaty.':'.$value.';';
                }
            }
            $styleStr = $styleStr.'.'.$styleSelector."{".$styles."}";
        }
    
        self::$styles = self::$styles.$styleStr.$last;

        return function($className){
            
            return self::$classes[$className];
        };
    }
    
    private static function ramdom($length)
    {
        $words = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'
        ];

        $hash = "";
        $i = 0;

        while($i < $length)
        {
            $hash = $hash.$words[array_rand($words)];
            $i++;
        }

        return $hash;
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
