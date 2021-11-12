# Router
Simple object-oriented PHP router. Developed by Érick Firmo (BR) https://erickfirmo.dev


## Requirements
- PHP >= 7
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
  require_once __DIR__ . '/vendor/autoload.php';

  // Creating the router instance
  $router = new \ErickFirmo\Router;

  // Defining optional settings

  // Load routes file
  require_once __DIR__ . '/routes/web.php';

  // Run the router
  $router->run();

```

### Defining routes
Routes file example:
```php
<?php

  $router->get('/examples', ExampleController::class, 'index', 'examples.index');
  $router->get('/examples/{id}', ExampleController::class, 'show', 'examples.show');
  $router->post('/examples/store', ExampleController::class, 'store', 'examples.store');
  $router->put('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');
  $router->patch('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');
  $router->delete('/examples/destroy/{id}', ExampleController::class, 'delete', 'examples.destroy');

```

### Optional Settings

#### Namespace
If all of your manipulation classes are in the same namespace, you can set the default namespace to use in the router instance with `setNamespace(string $namespace)`:

```php
<?php

  $router->setNamespace('App\Controllers\\');

```

#### Error Page
By default, will be return a message error for routes not defined. You can set a custom page for this error, using `notFoundView(string $view)` method after instantiate the router:
```php
<?php

  // Defining custom error page 404
  $router->notFoundView(__DIR__.'/../views/errors/404.php');

```

#### Passing Request Values
We can pass a request to our router using the `setRequest(string $name, Object|array $request)` method. This value will be used as the first argument of the called method. Example of using the request parameter:
```php
<?php

  // Creating array with request data
  $request = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;

  // Passing request data
  $router->setRequest('request', $request);

```
Getting the request parameter in example of controller:
```php
<?php

  class ExampleController
  {
    public function myMethod(array $request, int $id)
    {
      echo $request['name'];
      echo $id;
    }

  }

```
We can also pass an object as a parameter:
```php
<?php

  class ExampleController
  {
    public function myMethod(Object $request, int $id)
    {
      echo $request->name;
      echo $id;
    }

  }

```


<!--## License -->


<a href="https://erickfirmo.dev" target="_blank">Érick Firmo</a>
