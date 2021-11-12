<?php

namespace ErickFirmo;

class Router
{
    private $namespace = '';
    private $routeName;
    private $routeList = [];
    private $method;
    private $controller;
    private $notFoundView = null;
    private $arguments = [];
    private $acceptedHttpMethods = ['get', 'post', 'put', 'patch', 'delete'];
    private $httpMethod = 'get';
    private $route = [];
    private $request;
    private $requestVarName = 'request';

    // Retorna método de requisição http
    private function request_method() : string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    // Retorna path da url atual
    private function request_path() : string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    // Verifica e define verbo http
    private function checkHttpMethod() : bool
    {
        if($this->request_method() == 'POST')
            $this->httpMethod = (isset($_POST['_method']) && in_array($_POST['_method'], $this->acceptedHttpMethods)) ? $_POST['_method'] : 'post';

        if(!$this->route)
            return false;

        return $this->route['http_method'] == $this->httpMethod ? true : false;
    }

    // Cria a rota
    private function createRoute(string $httpMethod, string $path, string $controller, string $method, string $name='') : array
    {
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
    private function createSegmentsMap(string $path, array $segments_map=[]) : array
    {
        $array_path = explode('/', $path);
        array_shift($array_path);
        foreach($array_path as $key => $segment) 
            if (preg_match('/{(.*?)}/', $segment))
                $segments_map[$key] = $segment;

        return $segments_map;
    }

    // Define nome da rota e argumentos que serão passados
    private function setRoute(array $route, array $segments_map) : void
    {
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
        
        if($routeName == $route['path'] )
        {
            if(!isset($this->routeList[$this->httpMethod]))
            {
                $this->route = $route;
            } elseif(!array_key_exists($this->request_path(), $this->routeList[$this->httpMethod])) {
                $this->route = $route;
            }
        }

        // Inserindo na lista de rotas
        $this->routeList[$route['http_method']][$route['path']] = $route;
    }


    // Retorna namespace dos controllers
    private function getNamespace() : string
    {
        return $this->namespace;
    }


    // Define namespace dos controllers
    public function setNamespace(string $namespace) : void
    {
        $this->namespace = $namespace;
    }

    // Define arquivo de visualização para erro 404
    public function notFoundView(string $view) : void
    {
        $this->notFoundView = $view;
    }

    // Define request
    public function setRequest(string $requestVarName, $request) : void
    {
        if (!is_array($request) && !is_object($request)) {
            throw new \InvalidArgumentException('Argument 2 passed to '.__METHOD__.' must be of the type Object or array, '.gettype($request).' given.');
        }

        $this->requestVarName = $requestVarName;
        $this->request = $request;
    }

    // Pega array de parametros de um método
    private function get_method_argNames(string $class, string $method) : array
    {
        $reflectionMethod =  new \ReflectionMethod($class, $method);

        $params = $reflectionMethod->getParameters();

        $paramNames = array_map(function( $item ){
            return $item->getName();
        }, $params);

        return $paramNames;
    }

    // Executa rota já definida
    public function run()
    {
        try {
            // Define verbo http da requisição atual
            if(!$this->checkHttpMethod()) {
                // exception
                http_response_code(404);
                if(!$this->notFoundView) {
                    header("HTTP/1.0 404 Not Found");
                    print '404 Not Found';
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
            $ReflectionMethod =  new \ReflectionMethod($controllerName, $methodName);
            $params = $ReflectionMethod->getParameters();
            $paramNames = array_map(function( $item ){
                return $item->getName();
            }, $params);

            // Verifica se existe $request como argmento do método
            if(isset($paramNames[0]) && $paramNames[0] == $this->requestVarName && $this->request)
            {
                // Adiciona objeto $request a lista de argumentos
                $this->arguments = array_reverse($this->arguments);
                array_push($this->arguments, $this->request);
                $this->arguments = array_reverse($this->arguments);
            }

            // Validando tipo de dado dos parametros
            foreach ($params as $key => $p)
            {
                // Verifica se há tipo configurado
                if($p->getType()) {
                    // Verifica se é um inteiro
                    if(isset($this->arguments[$key]))
                    {
                        if($p->getType()->getName() == 'int')
                        {
                            if (!is_numeric($this->arguments[$key])) {
                                throw new \InvalidArgumentException('Argument '.($key+1).' passed to Router must be of the type '.$p->getType()->getName().', '.gettype($this->arguments[$key]).' given.');
                            }
        
                        // Verifica se é uma string
                        } elseif($p->getType()->getName() == 'string') {
                            if (!is_string($this->arguments[$key])) {
                                throw new \InvalidArgumentException('Argument '.($key+1).'2 passed to Router must be of the type '.$p->getType()->getName().', '.gettype($this->arguments[$key]).' given.');
                            }
                        }
                    }
                    
                }
            }
        
            return call_user_func_array([new $controllerName(), $methodName], $this->arguments);
            
        } catch (\Throwable $th) {

            throw $th;

        } catch (\Exception $e) {
            
            throw $e;
        }
    }

    // Cria rota GET
    public function get(string $path, string $controller, string $method, string $name='') : void
    {
        // Criando rota
        $route = $this->createRoute('get', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);

    }

    // Cria rota POST
    public function post(string $path, string $controller, string $method, string $name='') : void
    {
        // Criando rota
        $route = $this->createRoute('post', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    // Cria rota PUT
    public function put(string $path, string $controller, string $method, string $name='') : void
    {
        // Criando rota
        $route = $this->createRoute('put', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    // Cria rota PATCH
    public function patch(string $path, string $controller, string $method, string $name='') : void
    {
        // Criando rota
        $route = $this->createRoute('patch', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Definindo array de argumentos/segmentos da rota (passar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }

    // Cria rota DELETE
    public function delete(string $path, string $controller, string $method, string $name='') : void
    {
        // Criando rota
        $route = $this->createRoute('delete', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Definindo array de argumentos/segmentos da rota (passarar para o construct do route)
        $segments_map = $this->createSegmentsMap($path);

        // Define nome da rota que será executada
        $this->setRoute($route, $segments_map);
    }
}
