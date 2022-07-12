# moduleloader

index.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

Module::import('module', 'module.php');
Module::import('moduleAAAA', 'module.php');

$module(); //=>Hello World!!
$moduleAAAA(); //=>Hello World!!
```

module.php
```php
<?php
use Yakisova41\ModuleLoader\Export;

Export::default(function(){
  echo "Hello World!!";
});
```

## 名前付きimport
index.php
```php
<?php
use Yakisova41\ModuleLoader\Module;

Module::import('{Hello}', 'module.php');
Module::import('{bye}','module.php');
Module::import('{one}','module.php');

$Hello(); //=>Hello World!!
$bye(); //=>Seeyou goodbye

echo $one; //=> 1
```

module.php
```php
<?php
use Yakisova41\ModuleLoader\Export;

//hello
Export::export('Hello',function(){
  echo "Hello World!!";
});

//bye
$seeyou = function(){
  echo 'Seeyou goodbye';
};
Export::export('bye',$seeyou);

//one
Export::export('one',1);
```
