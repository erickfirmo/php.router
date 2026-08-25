<p align="center">

<img alt="PHP Router" src="./logo.png">

</p>

<h1 align="center">PHP Router Package</h1>

<blockquote align="center">

A simple object-oriented PHP router. Developed by Érick Firmo (BR) https://erickfirmo.dev

</blockquote>

<p align="center">

  <img alt="GitHub language count" src="https://img.shields.io/github/languages/count/erickfirmo/php.router?color=%2304D361">

  <img src="https://img.shields.io/packagist/dt/erickfirmo/router" alt="Total Downloads">

  <img src="https://img.shields.io/packagist/v/erickfirmo/router" alt="Latest Stable Version">

  <a href="https://erickfirmo.dev">

```
<img alt="Made by Erick Firmo" src="https://img.shields.io/badge/made%20by-Érick%20Firmo-%2304D361">
```

  </a>

  <img alt="License" src="https://img.shields.io/badge/license-MIT-%2304D361">

</p>

## :electric_plug: Prerequisites

* PHP >= 7
* <a href="https://github.com/erickfirmo/.htaccess/blob/master/.htaccess" target="_blank">URL rewriting</a>

## Installation

Install the package using Composer:

```sh
composer require erickfirmo/router
```

## Usage

```php
<?php

  // Require Composer's autoloader

  require_once __DIR__ . '/vendor/autoload.php';

  // Create the router instance

  $router = new \ErickFirmo\Router;

  // Define optional settings

  // Load the routes file

  require_once __DIR__ . '/routes/web.php';

  // Run the router

  $router->run();
```

### Defining Routes

Example of a routes file:

```php
<?php

  $router->get('/examples', ExampleController::class, 'index', 'examples.index');

  $router->get('/examples/{id}', ExampleController::class, 'show', 'examples.show');

  $router->post('/examples/store', ExampleController::class, 'store', 'examples.store');

  $router->put('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');

  $router->patch('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');

  $router->delete('/examples/destroy/{id}', ExampleController::class, 'delete', 'examples.destroy');
```

### Setting the HTTP Verb in Forms

To specify the HTTP verb in your form, add a hidden input named `_method` with the desired HTTP verb as its value.

The supported values are `PUT`, `PATCH`, and `DELETE`.

Example:

```html
<input type="hidden" name="_method" value="PUT">
```

This is not necessary when using `GET` or `POST` routes.

## Optional Settings

### Namespace

If all of your route-handling classes use the same namespace, you can define a default namespace for the router instance using the `setNamespace(string $namespace)` method:

```php
<?php

  $router->setNamespace('App\Controllers\\');
```

### Error Page

By default, the router returns an error message when a requested route is not defined.

You can specify a custom 404 error page using the `notFoundView(string $view)` method:

```php
<?php

  // Define a custom 404 error page

  $router->notFoundView(__DIR__.'/../views/errors/404.php');
```

### Passing Request Data

You can pass request data to the router using the `setRequest(string $name, Object|array $request)` method.

The provided value will be passed as the first argument to the matched controller method.

Example:

```php
<?php

  // Create an array containing the request data

  $request = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;

  // Pass the request data to the router

  $router->setRequest('request', $request);
```

The request data can then be accessed from your controller:

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

You can also pass an object as the request parameter:

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

## :copyright: License

MIT License.

See [LICENSE](LICENSE) for details.

<hr/>

<h3 align="center">

<a href="http://linkedin.com/in/érick-firmo-24615b166">Connect with me on LinkedIn</a> | <a href="https://erickfirmo.dev">View my CV</a>

</h3>
