<?php

$router = new \ErickFirmo\Router;
$router->get('/', HomeController::class, 'index', 'home');
#$router->get('/customer', CustomerController::class, 'index');
#$router->get('/customer/create', CustomerController::class, 'create', 'customers.create');
##$router->get('/customer/edit/{$id}', CustomerController::class, 'edit');
#$router->get('/customer/{id}', CustomerController::class, 'show', 'customers.show');
#$router->get('/customer/create', CustomerController::class, 'create', 'customers.create');
#$router->post('/customer/store', CustomerController::class, 'store');
#$router->put('/customer/update/{$id}', CustomerController::class, 'update');
#$router->delete('/customer/destroy/{$id}', CustomerController::class, 'destroy');
$router->run();

