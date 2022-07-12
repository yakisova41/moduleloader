# moduleloader

index.php
```php
<?php
use Yakisova41\ModuleLoader\Loader;

$module = Loader::import('./module.php');

$module(); //=>Hello World!!

```

module.php
```php
<?php
use Yakisova41\ModuleLoader\Loader;

Loader::exportDefault(function(){
  echo "Hello World!!";
});
```

## 名前付きimport
index.php
```php
<?php
use Yakisova41\ModuleLoader\Loader;

$hellomodule = Loader::import('./module.php', 'Hello');
$byemodule = Loader::import('./module.php', 'bye');
$onemodule = Loader::import('./module.php', 'one');

$hellomodule(); //=>Hello World!!
$byemodule(); //=>Seeyou goodbye

echo $onemodule; //=> 1
```

module.php
```php
<?php
use Yakisova41\ModuleLoader\Loader;

//hello
Loader::export('Hello',function(){
  echo "Hello World!!";
});

//bye
$seeyou = function(){
  echo 'Seeyou goodbye';
};
Loader::export('bye',$seeyou);

//one
Loader::export('one',1);
```
