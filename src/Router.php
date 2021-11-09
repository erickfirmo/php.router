<?php

namespace ErickFirmo;

class Router {

    public $namespace = 'App\Controllers\\';
    public $routeName;

    public $getRoutes = [];
    public $postRoutes = [];
    public $putRoutes = [];
    public $patchRoutes = [];
    public $deleteRoutes = [];

    public $routeList = [];

    public $method;
    public $controller;
    public $parameterIndex = null;
    public $parameterValue = null;
    public $notFoundView = null;

    public $arguments = [];
    public $numberOfArguments = null;
    
    public function getNamespace() {
        return $this->namespace;
    }
    
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function setArguments($route) {
        
        $route = ltrim($route, '/');

        // pega nomes dos parametros da rota
        preg_match_all('/{(.*?)}/', $route, $paramNames);

        return $paramNames;
    }
    
    public function get($route, $controller, $method, $name=null) {

        $name = !$name ? $route : $name;
        $this->getRoutes[$name]['name'] = $name;
        $this->getRoutes[$name]['action'] = "$controller@$method";

        $parameters = $this->setArguments($route);
        
        // colocar esse bloco no metodo acima setArguments, depois renomear para mapping
        $segments = explode('/', $this->request_path());
        array_shift($segments);
        $array_route = explode('/', $route);
        array_shift($array_route);
        $paramMap = [];



        foreach($segments as $key => $value) {

            if ($value != $array_route[$key]) {
                $paramMap[$array_route[$key]] = $value;


                $segments[$key] = $array_route[$key];
            }

        }


        $this->getRoutes[$name]['parameters'] = $paramMap;

        // definindo nome da rota
        $this->routeName = '/' . implode('/', $segments);


        // pega nomes dos parametros da rota
        return $this;
    }
    
    public function post($route, $controller, $method) {
        $this->postRoutes[$route] = "$controller@$method";
        return $this;
    }
    
    public function put($route, $controller, $method) {
        $this->putRoutes[$route] = "$controller@$method";
        return $this;
    }
    
    public function patch($route, $controller, $method) {
        $this->patchRoutes[$route] = "$controller@$method";
        return $this;
    }
    
    public function delete($route, $controller, $method) {
        $this->deleteRoutes[$route] = "$controller@$method";
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

    public function notFoundView($view) {
        return $this->notFoundView = $view;
    }
    
    public function validateRoute($routes, $name) {
        if(!isset($routes[$name])) {
            // exception
            http_response_code(404);
            if(!$this->notFoundView) {
                header("HTTP/1.0 404 Not Found");
                echo '404 Not Found';
                exit();
            }
            include $this->notFoundView;
            exit();
        } 
        
        return $routes[$name];
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
    
    public function request_path() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    
    
    
    
    
    public function setAction($action) {
        $actions = explode('@', $action);
        $this->setController($this->getNamespace().$actions[0]);
        $this->setMethod($actions[1]);
    }

    public function findRoute()
    {
        #foreach($this->getGetRoutes())
    }
    
    public function checkRequestType() {

        $routeName = $this->getRouteName();

        if($this->request_method() == 'GET') {

            //var_dump($this->getGetRoute($routeName));

            $this->setAction($this->getGetRoute($routeName)['action']);
        } elseif($this->request_method() == 'POST') {
            if(isset($_POST['_method'])) {
                switch ($_POST['_method']) {
                    case 'put':
                        $this->setAction($this->getPutRoute($routeName));
                        break;
                    case 'patch':
                        $this->setAction($this->getPatchRoute($routeName));
                        break;
                    case 'delete':
                        $this->setAction($this->getDeleteRoute($routeName));
                }
            } else {
                $this->setAction($this->getPostRoute($routeName));
            }
        }
    }
    
    public function run() {

        //$this->setArgumentsNames();
        //$this->setRouteName();

        $this->checkRequestType();

        $controller = $this->getController();
        $method = $this->getMethod();
        $route = $this->getGetRoute($this->getRouteName());


        #$this->numberOfArguments = 2;
        #array_push($this->arguments, 'Request 1');

        $this->arguments = $route['parameters'];

        //array_push($this->arguments, $_POST);



        return call_user_func_array(array(new $controller(), $method), array_values($this->arguments));

        #return isset($parameterValue) && !is_null($parameterValue) ? (new $controller())->$method($teste, $parameterValue) : (new $controller())->$method();
        //return isset($parameterValue) && !is_null($parameterValue) ? (new $controller())->$method($teste, $parameterValue) : (new $controller())->$method();
    
    
    }


    public function setRouteName()
    {
        //$this->routeName = str_replace($this->getParameterValue(), '{$id}', $this->request_path());
        //$this->routeName = str_replace($this->getParameterValue(), '{$id}', $this->request_path());
    }
    
    public function getRouteName() {
        return $this->routeName;
    }

    /*public function setArgumentsNames() {
        // deve pegar multiplos argumentos (palavras dentro das chaves)
        // e adicionar ao array $this->arguments
        $url = explode('/', $this->request_path());
        array_shift($url);


        // mapping
        //var_dump($url);


        $this->setArguments();


        foreach($url as $key => $value) {
            //$value
        }
        //$this->setArguments();

        die();

        //array_shift($url);
        //$urlParam = array_reverse($url);
        //foreach ($url as $key => $param) {

            /*if(is_numeric($param)) {


            */


            /*
            
            $this->setParameterValue($param);
            $this->setParameterIndex($key);
            }*/


           // verifica se é parametro
           // deve usar array de argumentos (ex: 'id' => 122, 'name' => 'Erick')
        //}
    //}

    /*public function setArguments() {
        
        $url = ltrim($this->request_path(), '/');

        // pega nomes dos parametros da rota
        preg_match_all('/{(.*?)}/', $url, $paramNames);

        return $paramNames;
    }*/
    
}
