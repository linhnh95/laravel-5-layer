## laravel-5-layer

Laravel 5 Layer is a package that helps to create structure and help with the project construction process.

## Installation

Require this package with composer. It is recommended to only require the package.

```shell
composer require linhnh95/laravel5layer
```

### Config

Add the ServiceProvider to the providers array in config/app.php

```php
App\Providers\RepositoryServiceProvider::class,
```

### Cache Query

Add the source code to the stores array in config/cache.php

```php
'request' => [
    'driver' => 'array'
]
```

Later add the source code to bottom public/index.php

```php
try {
    $app->make('cache')->store('request')->flush();
} catch (ReflectionException $ex) {
}
```

## Usage

After running the composer. Turn on the terminal screen and run the command


```shell
php artisan linh-5layer:init
```

To create a series of processing files for a Model using 5 layers

```shell
php artisan linh-5layer:create {Model}
```
