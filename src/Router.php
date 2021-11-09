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
    public $acceptedHttpMethods = ['get', 'post', 'put', 'patch', 'delete'];
    public $httpMethod = 'get';
    public $route = [];
    
    public function getNamespace() {
        return $this->namespace;
    }
    
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function getRouteName() {
        return $this->routeName;
    }
    
    public function getAction($action) {
        $actions = explode('@', $action);
        return [
            'controller' => $this->getNamespace() . $actions[0],
            'action' => $actions[1],
        ];
    }

    // Cria a rota
    public function createRoute($httpMethod, $path, $controller, $method, $name=null) {
        // Criando rota
        $name = !$name ? $path : $name;
        $route['name'] = $name;
        $route['path'] = $path;
        $route['controller'] = $this->getNamespace() . $controller;
        $route['method'] = $method;
        $route['http_method'] = $httpMethod;

        return $route;
    }

    // Cria mapa de chaves dos parametros passados na rota
    public function createSegmentsMap($path, $segments_map=[]) {
        $array_path = explode('/', $path);
        array_shift($array_path);
        foreach($array_path as $key => $segment) 
            if (preg_match('/{(.*?)}/', $segment))
                $segments_map[$key] = $segment;

        return $segments_map;
    }

    public function setRoute($route, array $segments_map) {
        $array_url = explode('/', $this->request_path());
        array_shift($array_url);

        $args = [];

        foreach ($segments_map as $key => $segment) {
            array_push($args, $array_url[$key]);
            $array_url[$key] = $segment;
        }

        $routeName = '/' . implode('/', $array_url);
        $route['segments'] = $args;

        if($routeName == $route['path']) {
            $this->route = $route;
        }
    }

    // Retorna método de requisição http
    public function request_method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    // Retorna path da url atual
    public function request_path() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function getRoute($name, $httpMethod) : array
    {
        return $this->routeList[$httpMethod][$name];
    }
    
    public function notFoundView($view)
    {
        return $this->notFoundView = $view;
    }

    // Cria rota GET
    public function get($path, $controller, $method, $name=null)
    {
        // Criando rota
        $route = $this->createRoute('get', $path, $controller, $method, $name);

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Inserindo na lista de rotas
        $this->routeList['get'][$name] = $route;

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    // Cria rota POST
    public function post($path, $controller, $method, $name=null)
    {
        // Criando rota
        $route = $this->createRoute('post', $path, $controller, $method, $name);

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Inserindo na lista de rotas
        $this->routeList['post'][$name] = $route;

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    // Cria rota PUT
    public function put($path, $controller, $method, $name=null)
    {
        // Criando rota
        $route = $this->createRoute('put', $path, $controller, $method, $name);

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Inserindo na lista de rotas
        $this->routeList['put'][$name] = $route;

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    // Cria rota PATCH
    public function patch($path, $controller, $method, $name=null)
    {
        // Criando rota
        $route = $this->createRoute('patch', $path, $controller, $method, $name);

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Inserindo na lista de rotas
        $this->routeList['patch'][$name] = $route;

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    // Cria rota DELETE
    public function delete($path, $controller, $method, $name=null)
    {
        // Criando rota
        $route = $this->createRoute('delete', $path, $controller, $method, $name);

        // Definindo array de argumentos/segmentos da rota (passarar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Inserindo na lista de rotas
        $this->routeList['delete'][$name] = $route;

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    public function checkHttpMethod(array $request) {

        if($this->request_method() == 'POST' && isset($request['_method']))
            $this->httpMethod = (isset($request['_method']) && in_array($request['_method'], $this->acceptedHttpMethods)) ? $request['_method'] : 'post';

        if(!$this->route)
            return false;

        return $this->route['http_method'] == $this->httpMethod ? true : false;
    }

    public function run(Object $request) {

        // Define request padrão do router caso não esteja definida
        if(!$request) {
            $request = new \Core\Request;
        }

        // Define verbo http da requisição atual
        if(!$this->checkHttpMethod($request->all())) {
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

        // Adiciona objeto Request a lista de argumentos
        $this->arguments = $this->route['segments'];
        array_push($this->arguments, $request);
        $this->arguments = array_reverse($this->arguments);

        // Definindo controller e método
        $controller = $this->route['controller'];
        $method = $this->route['method'];

        /*
        OR
        $action = $this->getAction($this->route['action']);
        $controller = $action['controller'];
        $method = $action['method'];
        */

        //var_dump($this->arguments);exit;
        
        try {
            return call_user_func_array(array(new $controller(), $method), $this->arguments);
        } catch (\Exception $e) {
            throw $e->getMessage();
        }

    }
}
