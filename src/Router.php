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

    // Return http request method
    private function request_method() : string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    // Return path from current url
    private function request_path() : string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    // Checks and sets http verb
    private function checkHttpMethod() : bool
    {
        if($this->request_method() == 'POST')
            $this->httpMethod = (isset($_POST['_method']) && in_array($_POST['_method'], $this->acceptedHttpMethods)) ? $_POST['_method'] : 'post';

        if(!$this->route)
            return false;

        return $this->route['http_method'] == $this->httpMethod ? true : false;
    }

    // Creating route
    private function createRoute(string $httpMethod, string $path, string $controller, string $method, string $name='') : array
    {
        $name = !$name ? $path : $name;
        $route['name'] = $name;
        $route['path'] = $path;
        $route['controller'] = $this->getNamespace() . $controller;
        $route['method'] = $method;
        $route['http_method'] = $httpMethod;

        return $route;
    }

    // Creates a key map of the parameters passed in the route
    private function createSegmentsMap(string $path, array $segments_map=[]) : array
    {
        $array_path = explode('/', $path);
        array_shift($array_path);
        foreach($array_path as $key => $segment) 
            if (preg_match('/{(.*?)}/', $segment))
                $segments_map[$key] = $segment;

        return $segments_map;
    }

    // Defines route name and arguments that will be passed
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

        // Inserting in the route list
        $this->routeList[$route['http_method']][$route['path']] = $route;
    }


    // Return controllers namespace
    private function getNamespace() : string
    {
        return $this->namespace;
    }

    // Define controllers namespace
    public function setNamespace(string $namespace) : void
    {
        $this->namespace = $namespace;
    }

    // Set preview file to 404 error
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

    // Gets an array of parameters from a method
    private function get_method_argNames(string $class, string $method) : array
    {
        $reflectionMethod =  new \ReflectionMethod($class, $method);

        $params = $reflectionMethod->getParameters();

        $paramNames = array_map(function( $item ){
            return $item->getName();
        }, $params);

        return $paramNames;
    }

    // Run the router
    public function run()
    {
        try {
            // Check http verb of current request
            if(!$this->checkHttpMethod()) {
                // Exception
                http_response_code(404);
                if(!$this->notFoundView) {
                    header("HTTP/1.0 404 Not Found");
                    throw new \Exception('404 Not Found');
                    exit();
                }
                include $this->notFoundView;
                exit();
            }

            // Get and invert route argument array
            $this->arguments = $this->route['segments'];

            // Defining controller and method
            $controllerName = $this->route['controller'];
            $methodName = $this->route['method'];

            // Invoke array of parameters of the method to be called
            $ReflectionMethod =  new \ReflectionMethod($controllerName, $methodName);
            $params = $ReflectionMethod->getParameters();
            $paramNames = array_map(function( $item ){
                return $item->getName();
            }, $params);

            // Checks for request as method argument
            if(isset($paramNames[0]) && $paramNames[0] == $this->requestVarName && $this->request)
            {
                // Add request object to argument list
                $this->arguments = array_reverse($this->arguments);
                array_push($this->arguments, $this->request);
                $this->arguments = array_reverse($this->arguments);
            }

            // Validating data type of parameters
            foreach ($params as $key => $p)
            {
                // Checks for configured type
                if($p->getType()) {
                    // Check if it is an integer
                    if(isset($this->arguments[$key]))
                    {
                        if($p->getType()->getName() == 'int')
                        {
                            if (!is_numeric($this->arguments[$key])) {
                                throw new \InvalidArgumentException('Argument '.($key+1).' passed to Router must be of the type '.$p->getType()->getName().', '.gettype($this->arguments[$key]).' given.');
                            }
        
                        // Check if it's a string
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

    // Create GET route
    public function get(string $path, string $controller, string $method, string $name='') : void
    {
        // Creating route
        $route = $this->createRoute('get', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Defining route arguments/segments array
        $segments_map = $this->createSegmentsMap($path);

        // Defines name of the route that will be executed
        $this->setRoute($route, $segments_map);

    }

    // Create POST route
    public function post(string $path, string $controller, string $method, string $name='') : void
    {
        // Creating route
        $route = $this->createRoute('post', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Defining route arguments/segments array
        $segments_map = $this->createSegmentsMap($path);

        // Defines name of the route that will be executed
        $this->setRoute($route, $segments_map);
    }

    // Create PUT route
    public function put(string $path, string $controller, string $method, string $name='') : void
    {
        // Creating route
        $route = $this->createRoute('put', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Defining route arguments/segments array
        $segments_map = $this->createSegmentsMap($path);

        // Defines name of the route that will be executed
        $this->setRoute($route, $segments_map);
    }

    // Create PATCH route
    public function patch(string $path, string $controller, string $method, string $name='') : void
    {
        // Creating route
        $route = $this->createRoute('patch', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Defining route arguments/segments array
        $segments_map = $this->createSegmentsMap($path);

        // Defines name of the route that will be executed
        $this->setRoute($route, $segments_map);
    }

    // Create DELETE route
    public function delete(string $path, string $controller, string $method, string $name='') : void
    {
        // Creating route
        $route = $this->createRoute('delete', $path, $controller, $method, $name);

        if(!$name)
            $name = $path;

        // Defining route arguments/segments array
        $segments_map = $this->createSegmentsMap($path);

        // Defines name of the route that will be executed
        $this->setRoute($route, $segments_map);
    }
}
