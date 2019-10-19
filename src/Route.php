<?php

namespace ErickFirmo;

class Route {

    public $namespace = 'App\Controllers\\';
    public $routeName;
    public $getRoutes = [];
    public $postRoutes = [];
    public $putRoutes = [];
    public $patchRoutes = [];
    public $deleteRoutes = [];
    public $method;
    public $controller;
    public $parameterIndex = NULL;
    public $parameterValue = NULL;
    
    public function getNamespace() {
        return $this->namespace;
    }
    
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }
    
    public function get($route) {
        $this->getRoutes[$route[0]] = $route[1];
        return $this;
    }
    
    public function post($route) {
        $this->postRoutes[$route[0]] = $route[1];
        return $this;
    }
    
    public function put($route) {
        $this->putRoutes[$route[0]] = $route[1];
        return $this;
    }
    
    public function patch($route) {
        $this->patchRoutes[$route[0]] = $route[1];
        return $this;
    }
    
    public function delete($route) {
        $this->deleteRoutes[$route[0]] = $route[1];
        return $this;
    }
    
    public function getGetRoute($name) {
        return $this->validateRoute($this->getRoutes, $name);
    }
    
    public function getPostRoute($name) {
        return $this->validateRoute($this->postRoutes, $name);
    }
    
    public function getPutRoute($name) {
        return $this->validateRoute($this->putRoutes, $name);
    }
    
    public function getPatchRoute($name) {
        return $this->validateRoute($this->patchRoutes, $name);
    }
    
    public function getDeleteRoute($name) {
        return $this->validateRoute($this->deleteRoutes, $name);
    }
    
    public function getGetRoutes() {
        return $this->getRoutes;
    }
    
    public function getPostRoutes() {
        return $this->postRoutes;
    }
    
    public function getPutRoutes() {
        return $this->putRoutes;
    }
    
    public function getPatchRoutes() {
        return $this->patchRoutes;
    }
    
    public function getDeleteRoutes() {
        return $this->deleteRoutes;
    }
    
    public function validateRoute($routes, $name) {
        return isset($routes[$name]) ? $routes[$name] : die('Rota não definida.');
    }
    
    public function setController($controller) {
        $this->controller = $controller;
    }
    
    public function getController() {
        return $this->controller;
    }
    
    public function setMethod($method) {
        $this->method = $method;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function request_method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function request_uri() {
        return $_SERVER['REQUEST_URI'];
    }
    
    public function setRouteName() {
        $this->routeName = str_replace($this->getParameterValue(), '{$id}', $this->request_uri());
    }
    
    public function getRouteName() {
        return $this->routeName;
    }
    
    public function setParameters() {
        $url = explode('/', $this->request_uri());
        $urlParam = array_reverse($url);
        foreach ($urlParam as $key => $param) {
           if(is_numeric($param)) {
            $this->setParameterValue($param);
            $this->setParameterIndex($key);
           }
        }
    }
    
    public function setParameterValue($value) {
        $this->parameterValue = $value;
    }
    
    public function setParameterIndex($index) {
        $this->parameterIndex = $index;
    }
    
    public function getParameterIndex() {
        return $this->parameterIndex;
    }
    
    public function getParameterValue() {
        return $this->parameterValue;
    }
    
    public function setAction($action) {
        $actions = explode('@', $action);
        $this->setController($this->getNamespace().$actions[0]);
        $this->setMethod($actions[1]);
    }
    
    public function checkRequestType() {
        if($this->request_method() == 'GET') {
            $this->setAction($this->getGetRoute($this->getRouteName()));
        } elseif($this->request_method() == 'POST') {
            if(isset($_POST['_method'])) {
                switch ($_POST['_method']) {
                    case 'put':
                        $this->setAction($this->getPutRoute($this->getRouteName()));
                        break;
                    case 'patch':
                        $this->setAction($this->getPatchRoute($this->getRouteName()));
                        break;
                    case 'delete':
                        $this->setAction($this->getDeleteRoute($this->getRouteName()));
                }
            } else {
                $this->setAction($this->getPostRoute($this->getRouteName()));
            }
        }
    }
    
    public function run() {
        $this->setParameters();
        $this->setRouteName();
        $this->checkRequestType();
        $controller = $this->getController();
        $method = $this->getMethod();
        $parameterValue = $this->getParameterValue();
        return isset($parameterValue) && !is_null($parameterValue) ? (new $controller()->$method($parameterValue)) : (new $controller()->$method());
    }
    
}
