<?php

$router = new \Routing\Router;
$router->get(['/examples', 'ExampleController@select']);
//$router->get(['/examples/{$id}', 'ExampleController@findById']);
//$router->post(['/examples/store', 'ExampleController@store']);
//$router->put(['/examples/{$id}', 'ExampleController@update']);
//$router->patch(['/examples/{$id}', 'ExampleController@update']);
//$router->delete(['/examples/{$id}', 'ExampleController@delete']);
$router->run();

