<?php

namespace Core;

use Core\Middleware\Middleware;

class Router
{

    protected array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    public function __construct()
    {
        $this->addRoutes();
    }

    private function addRoutes(): void
    {
        $routesList = $this->getRoutes();
        foreach ($routesList as $route) {
            $this->routes[$route->getMethod()][$route->getUri()] = $route;
            $route->setUriParams();
        }
    }

    private function getRoutes(): array
    {
        return require_once BASE_PATH . 'routes.php';
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
//        dd($_SERVER);
        exit('404 Not found');
    }

    public function route(Request $request): mixed
    {
        $currentRoute = $this->findRoute($request->uri(), $request->method());
        if (!$currentRoute) {
            $this->abort(404);
        }

        $role = $currentRoute->getMiddleware() ?? false;

        if ($role) {
            Middleware::resolve($role);
        }

        $params = $this->getParams($request->uri(), $currentRoute);
        $action = $currentRoute->getAction();
//        dd($request);

        if (is_array($action)) {
            $action = $this->useController($currentRoute->getAction());
        }
        return call_user_func($action, $request, $params);
    }

    public function useController($controller)
    {
        [$controller, $action] = $controller;
        $controller = new $controller; //Seems not good. Mb rework later

        return [$controller, $action];
    }

    private function findRoute(string $uri, string $method): ?Route
    {
        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];

            return $route;
        }

        $uriParts = explode('/', $uri);
        array_shift($uriParts); // trims the first empty element

        foreach($this->routes[$method] as $savedRoute) {

            if(!$savedRoute->getUriParams()) continue;

            $savedRouteParts = explode('/', $savedRoute->getUri());
            array_shift($savedRouteParts);

            if(count($uriParts) !== count($savedRouteParts)) {
                continue;
            }

            $savedParams = $savedRoute->getUriParams();

            for ($i = 0; $i < count($savedRouteParts); $i++) {

                if(!(isset($savedParams[$i])))
                {
                    if ($uriParts[$i] === $savedRouteParts[$i]) continue;
                    continue 2;
                }
                if (!ctype_alnum($uriParts[$i])) continue 2;

            }

            $route = $this->routes[$method][$savedRoute->getUri()];

            return $route;
        }

        return null;

    }

    private function getParams($uri, $currentRoute): array
    {
        $uriParts = explode('/', $uri);
        array_shift($uriParts);

        $parameters = [];

        if($currentRoute->getUriParams()) {
            foreach ($currentRoute->getUriParams() as $key => $paramName) {
                $parameters[$paramName] = $uriParts[$key];
            }
        }

        return $parameters;
    }

}