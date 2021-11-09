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
    
    // Define namespace dos controllers
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    // Retorna namespace dos controllers
    public function getNamespace() {
        return $this->namespace;
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

    // Define nome da rota e argumentos que serão passados
    public function setRoute($route, array $segments_map) {
        $array_url = explode('/', $this->request_path());
        array_shift($array_url);

        $args = [];

        foreach ($segments_map as $key => $segment) {
            if(array_key_exists($key, $array_url)) {
                array_push($args, $array_url[$key]);
                $array_url[$key] = $segment;
            }
            
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
    
    // Define arquivo de visualização para erro 404
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

    // Verifica e define verbo http
    public function checkHttpMethod(array $request) {

        if($this->request_method() == 'POST')
            $this->httpMethod = (isset($request['_method']) && in_array($request['_method'], $this->acceptedHttpMethods)) ? $request['_method'] : 'post';

        if(!$this->route)
            return false;

        return $this->route['http_method'] == $this->httpMethod ? true : false;
    }

    // Executa rota já definida
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

        // Pega e inverte array de argumentos da rota
        $this->arguments = $this->route['segments'];

        // Definindo controller e método
        $controllerName = $this->route['controller'];
        $methodName = $this->route['method'];

        // Invoca array de parametros do método que será chamado
        $params = $this->get_method_argNames($controllerName, $methodName);

        // Verifica se existe objeto Request como argumento do método
        if(isset($params[0]) && $params[0] == 'request')
        {
            // Adiciona objeto Request a lista de argumentos
            $this->arguments = array_reverse($this->arguments);
            array_push($this->arguments, $request);
            $this->arguments = array_reverse($this->arguments);
        }
        
        try {
            return call_user_func_array(array(new $controllerName(), $methodName), $this->arguments);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Pega array de parametros de um método
    protected function get_method_argNames($class, $method) {
        $ReflectionMethod =  new \ReflectionMethod($class, $method);

        $params = $ReflectionMethod->getParameters();

        $paramNames = array_map(function( $item ){
            return $item->getName();
        }, $params);

        return $paramNames;
    }
}
