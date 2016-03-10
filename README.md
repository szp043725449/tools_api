# Laravel ToolsService

## Documentation

## Installation

Require this package  

```php
php composer.phar require "tools/api:dev-master" -vvv
```

After adding the package, add the ServiceProvider to the providers array in `config/app.php`

```php
Tools\Api\ToolsServiceProvider::class,
```

Optionally you can register the ToolsService facade:

```php
'toolsServer'      => Tools\Api\Facades\ToolsService::class,
```

To publish the config use:

```php
php artisan vendor:publish --tag="config"
```

sign Middleware:

```php
/**
 * @Middleware("hsign")
 */
```

When use toolsService verification,you should increased verification message in: 
```php
resources\lang\zh_cn\validation.php
```