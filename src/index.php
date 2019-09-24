<?php

$route = new \Routing\Route;
$route->get(['/examples', 'ExampleController@select']);
//$route->get(['/examples/{$id}', 'ExampleController@findById']);
//$route->post(['/examples/store', 'ExampleController@store']);
//$route->put(['/examples/{$id}', 'ExampleController@update']);
//$route->patch(['/examples/{$id}', 'ExampleController@update']);
//$route->delete(['/examples/{$id}', 'ExampleController@delete']);
$route->run();

