# moduleloader

## default import
index.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

$module = Module::import(__DIR__.'/module');

$module(); //=>Hello World!!

```

module.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

Module::exportDefault(function(){
  echo "Hello World!!";
});
```

## Named import
index.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

$hellomodule = Module::import(__DIR__.'/module', 'Hello');
$byemodule = Module::import(__DIR__.'/module', 'bye');
$onemodule = Module::import(__DIR__.'/module', 'one');

$hellomodule(); //=>Hello World!!
$byemodule(); //=>Seeyou goodbye

echo $onemodule; //=> 1
```

module.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

//hello
Module::export('Hello',function(){
  echo "Hello World!!";
});

//bye
$seeyou = function(){
  echo 'Seeyou goodbye';
};
Module::export('bye',$seeyou);

//one
Module::export('one',1);
```

## Style import
style.style.json
```
{
  ".homeContainer":{
    "margin":"10px",
    "padding":"0px"
  }
}
```

responsive-style.style.json
```
{
  "media-query":"@media screen and (min-width:480px)",

  ".homeContainer":{
    "margin":"10px",
    "padding":"0px"
  }
}
```

home.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

$Home = Module::import(__DIR__.'/HomeModule');

echo $Home;
```

HomeModule.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

$Home = function(){
  $style = Module::import('style');
  $responsivestyle = Module::import('responsive-style');

  return("
      <div class='{$style('.homeContainer')} {$responsivestyle('.homeContainer')}'>
        //..homecontent....
      </div>
  ");
};

Module::exportDefault($Home);
```
