# Router

Um roteador PHP leve e simples orientado a objetos. Construído por Érick Firmo (BR) http://erickfirmo.dev


## Requerimentos
- PHP 5.4 ou superior
- <a href="https://github.com/erickfirmo/.htaccess" target="_blank">Reescrita de URL</a>


## Instalação
Para instalar com o composer:


```sh
composer require erickfirmo/router
```


## Uso Básico
```php
<?php

  // Requer o autoloader do composer
  require __DIR__ . '/vendor/autoload.php';

  // Criando a instância do roteador
  $route = new \ErickFirmo\Route;

  // Definição de rotas

  // Executa o roteador
  $route->run();

```

### Definindo rotas
Exemplos de uso:
```php
  $route->get(['/example', 'ExampleController@select']);
  $route->get(['/example/{$id}', 'ExampleController@findById']);
  $route->post(['/example/store', 'ExampleController@store']);
  $route->put(['/example/{$id}', 'ExampleController@update']);
  $route->patch(['/example/{$id}', 'ExampleController@update']);
  $route->delete(['/example/{$id}', 'ExampleController@delete']);
```

### Namespace
Se a todas as suas classes de manipulação estiverem em um mesmo namespace, você poderá definir o namespace padrão para usar na instância do roteador via `setNamespace()`:
```php
  $route->setNamespace('App\Controllers\\');
  $route->get(['/example', 'ExampleController@select']);
  $route->post(['/example/store', 'ExampleController@store']);
```

<!--## Licença
` erickfirmo/router` é uma biblioteca de código aberto licenciado sob a licença <a href="https://opensource.org/licenses/MIT" target="_blank">MIT</a>.-->


<!--<a href="https://erickfirmo.dev" target="_blank">Érick Firmo</a>-->
