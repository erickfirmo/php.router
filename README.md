<p align="center">

<img alt="PHP Router" src="./logo.png">

</p>

<h1 align="center">PHP Router</h1>

<p align="center">
A lightweight, object-oriented router for PHP applications.
</p>

<p align="center">
Created by <a href="https://erickfirmo.dev">Erick Firmo</a>.
</p>

<p align="center">

<img alt="GitHub language count" src="https://img.shields.io/github/languages/count/erickfirmo/php.router?color=%2304D361">
<img src="https://img.shields.io/packagist/dt/erickfirmo/router" alt="Total Downloads">
<img src="https://img.shields.io/packagist/v/erickfirmo/router" alt="Latest Stable Version">
<img alt="License" src="https://img.shields.io/badge/license-MIT-%2304D361">

</p>

## Requirements

- PHP 7.1 or higher
- A web server configured to rewrite URLs

The router expects your web server to forward application requests to your `index.php` entry point.

### Apache

On Apache, you can use `mod_rewrite` with a `.htaccess` file:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{SCRIPT_FILENAME} !-f
    RewriteCond %{SCRIPT_FILENAME} !-d
    RewriteCond %{SCRIPT_FILENAME} !-l

    RewriteRule ^(.*)$ /index.php/$1
</IfModule>
```

### Nginx

Nginx does not support `.htaccess`. Instead, URL rewriting must be configured in your server block.

For example:

```nginx
location / {
    try_files $uri $uri/ /index.php/$uri;
}
```

Make sure PHP is properly configured to process your application's `index.php` file.

## Installation

Install the package with Composer:

```bash
composer require erickfirmo/router
```

## Usage

Create a router instance and load your application's routes:

```php
<?php

// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create a router instance
$router = new \ErickFirmo\Router;

// Configure the router (optional)

// Load the application's routes
require_once __DIR__ . '/routes/web.php';

// Handle the current request
$router->run();
```

### Defining Routes

Routes can be defined using the HTTP methods supported by the router:


```php
<?php

  $router->get('/examples', ExampleController::class, 'index', 'examples.index');

  $router->get('/examples/{id}', ExampleController::class, 'show', 'examples.show');

  $router->post('/examples/store', ExampleController::class, 'store', 'examples.store');

  $router->put('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');

  $router->patch('/examples/update/{id}', ExampleController::class, 'update', 'examples.update');

  $router->delete('/examples/destroy/{id}', ExampleController::class, 'delete', 'examples.destroy');
```

A route can contain parameters using `{parameter}` syntax:

```php
  $router->get('/examples/{id}', ExampleController::class 'show', 'examples.show');
```

The route parameter is passed to the corresponding controller method:

```php
<?php

class ExampleController
{
    public function show(int $id)
    {
        echo $id;
    }
}
```

## HTTP Method Spoofing

HTML forms only support `GET` and `POST` requests natively. To submit a form to a `PUT`, `PATCH`, or `DELETE` route, include a hidden `_method` field:

```html
<input type="hidden" name="_method" value="PUT">
```

The router supports the following additional HTTP methods:

- `PUT`
- `PATCH`
- `DELETE`

No additional configuration is required for `GET` or `POST` requests.

## Configuration

The router provides several optional configuration methods.

### Namespace

If your controllers share a common namespace, you can define a default namespace for the router:

```php
<?php

$router->setNamespace('App\\Controllers\\');
```

This allows you to reference controllers without repeating the namespace in every route definition.

### Custom 404 Page

If no route matches the incoming request, the router returns a default error message.

You can provide your own 404 view using the `notFoundView()` method:

```php
<?php

$router->notFoundView(
    __DIR__ . '/../views/errors/404.php'
);
```

### Passing Request Data

You can pass request data to the router using the `setRequest()` method:

```php
<?php

$request = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? $_POST
    : $_GET;

$router->setRequest('request', $request);
```

The value passed to `setRequest()` is injected as the first argument of the matched controller method.

For example:

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

Objects can also be passed as request data:

```php
<?php

class ExampleController
{
    public function myMethod(object $request, int $id)
    {
        echo $request->name;
        echo $id;
    }
}
```

## License

This project is licensed under the MIT License.

See the [LICENSE](LICENSE) file for details.

<hr>

<p align="center">

<a href="https://linkedin.com/in/érick-firmo-24615b166">
Connect with me on LinkedIn
</a>
&nbsp; | &nbsp;
<a href="https://erickfirmo.dev">
Visit my website
</a>

</p>
