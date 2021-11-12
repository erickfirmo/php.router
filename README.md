# Router
Simple object-oriented PHP router. developed by Érick Firmo (BR) http://erickfirmo.dev


## Requirements
- PHP >= 5.4
- <a href="https://github.com/erickfirmo/.htaccess/blob/master/.htaccess" target="_blank">URL rewrite</a>


## Install
To install with composer:


```sh
composer require erickfirmo/router
```


## Usage
```php
<?php

  // Requires composer autoloader
  require __DIR__ . '/vendor/autoload.php';

  // Creating the router instance
  $router = new \ErickFirmo\Router;

  // Defining routes

  // Run the router
  $router->run();

```

### Defining routes
Examples:
```php
<?php

  $router->get('/examples', ExampleController::class, 'index', 'examples.index');
  $router->get('/examples/{id}', ExampleController::class, 'show', 'examples.show');
  $router->post('/examples/store', ExampleController::class, 'store', 'examples.store');
  $router->put('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');
  $router->patch('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');
  $router->delete('/examples/destroy/{id}', ExampleController::class, 'delete', 'examples.destroy');
```



### Namespace
If all of your manipulation classes are in the same namespace, you can set the default namespace to use in the router instance with `setNamespace ()`:

```php
<?php

  $router->setNamespace('App\Controllers\\');
  $router->get('/examples', ExampleController::class, 'index', 'examples.index');
  $router->post('/examples/store', ExampleController::class, 'store', 'examples.store');
```

## Error page
By default, will be return a message error for routes not defined. You can set a custom page for this error, using `notFoundView()` method after instantiate the router:
```php
<?php

  // Creating the router instance
  $router = new \ErickFirmo\Router;

  // Defining custom error page 404
  $router->notFoundView(__DIR__.'/../views/errors/404.php');

```

<!--## License -->


<!--<a href="https://erickfirmo.dev" target="_blank">Érick Firmo</a>-->
