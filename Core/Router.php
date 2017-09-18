<?php

/**
 * Router
 *
 * 
 */

namespace Core;

class Router
{

    // Routes array
    protected $routes = [];

    // Parameters from the matched route 
    protected $params = [];

    //Add a route to the routing table
    public function add($route, $params = [])
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables 
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end.. + case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    // Get all the routes
    public function getRoutes()
    {
        return $this->routes;
    }

    // match the route
    public function match($url)
    {
        // Match to the fixed URL format /controller/action
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {

                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    public function dispatch($url)
    {
        // remove the query string variables e.g. ?category=members&tag=active_members
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (preg_match('/action$/i', $action) == 0) {
                    $controller_object->$action();
                } else {
                    throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
                }
            } else {
                echo "<h1>Page not found.. You're now being redirected to the <a href='/'>homepage</a>.";
                header( "refresh:2;url=/" );
            }
        } else {
                echo "<h1>Page not found.. You're now being redirected to the <a href='/'>homepage</a>.";
                header( "refresh:2;url=/" );
        }
    }

    // converts to StudlyCaps
    public function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    // converts to camelCase
    public function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    // get matched parameters
    public function getParams()
    {
        return $this->params;
    }

    protected function removeQueryStringVariables($url) {
        if ($url != "") {
            $parts = explode("&", $url, 2);

            if (strpos($parts[0], "=") === false) {
                $url = $parts[0];
            } else {
                $url = "";
            }
        }
        return $url;
    }

    // get the namepsace
    protected function getNamespace() {

        // default namespace
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        } // if "namespace" param exists => concatinate

        return $namespace;
    }
}
