# Router

Um roteador PHP leve e simples orientado a objetos. Desenvolvido por Érick Firmo (BR) http://erickfirmo.dev


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
  $router = new \ErickFirmo\Router;

  // Definição de rotas

  // Executa o roteador
  $router->run();

```

### Definindo rotas
Exemplos de uso:
```php
  $router->get(['/example', 'ExampleController@select']);
  $router->get(['/example/{$id}', 'ExampleController@findById']);
  $router->post(['/example/store', 'ExampleController@store']);
  $router->put(['/example/{$id}', 'ExampleController@update']);
  $router->patch(['/example/{$id}', 'ExampleController@update']);
  $router->delete(['/example/{$id}', 'ExampleController@delete']);
```

### Namespace
Se todas as suas classes de manipulação estiverem em um mesmo namespace, você poderá definir o namespace padrão para usar na instância do roteador via `setNamespace()`:
```php
  $router->setNamespace('App\Controllers\\');
  $router->get(['/example', 'ExampleController@select']);
  $router->post(['/example/store', 'ExampleController@store']);
```

<!--## Licença
` erickfirmo/router` é uma biblioteca de código aberto licenciado sob a licença <a href="https://opensource.org/licenses/MIT" target="_blank">MIT</a>.-->


<!--<a href="https://erickfirmo.dev" target="_blank">Érick Firmo</a>-->
