<?php

namespace Core\Router;

use Core\Exceptions\MiddlewareRoleException;
use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Middleware\Middleware;

class Router
{
    /**
     * Http-requests router
     *
     */

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

    /**
     * Transforms http-routes list into $routes array, according to http method and setting routes uri-params like {id}
     * @return void
     */
    private function addRoutes(): void
    {
        $routesList = $this->getRoutes();

        foreach ($routesList as $route) {
            $this->routes[$route->getMethod()][$route->getUri()] = $route;
            $route->setUriParams();
        }
    }

    /**
     * Getter of allowed http-routes list of the application
     * @return array
     */
    private function getRoutes(): array
    {
        return require_once BASE_PATH . '/src/routes.php';
    }

    /**
     * @throws MiddlewareRoleException|\Exception
     */
    public function dispatch(Request $request): mixed
    {
        $currentRoute = $this->findRoute($request->uri(), $request->method());

        if (!$currentRoute) {
            Response::error(404, 'Route does not exist');
        }

        $this->checkAccess($currentRoute);

        $params = $this->getParams($request->uri(), $currentRoute);
        $action = $currentRoute->getAction();

        if (is_array($action)) {
            $action = $this->createController($currentRoute->getAction());
        }

        return call_user_func($action, $request, $params);
    }

    /**
     * Checks access rights to the requested resource.
     * In case of problems, appropriate response should be sent at the Middleware level.
     * @param $route
     * @return void
     */
    private function checkAccess($route): void
    {
        $role = $route->getMiddleware() ?? false;
        if ($role) {
            try {
                Middleware::resolve($role);
            } catch (MiddlewareRoleException $e) {
                echo 'MiddlewareRoleException: ' . $e->getMessage();
            }
        }
    }

    /**
     * Creates an instance of requested controller.
     * Will be replaced by a factory pattern implementation in the nearest future.
     * @param array $controller
     * @return array
     * @throws \Exception
     */
    public function createController(array $controller): array
    {
        [$controllerClassName, $action] = $controller;

        if (!class_exists($controllerClassName)) {
            throw new \Exception("Controller '$controllerClassName' not found");
        }

        $controllerObject = new $controllerClassName;

        return [$controllerObject, $action];
    }

    /**
     * Method searches for a sample of current route in $routes array.
     * If current route's method and uri are already set in routes array - route found.
     * Otherwise, searching for an appropriate route with variable parameters is performed.
     * To do this, the number of variable parameters of the saved route is compared to the number of unequal URI parts of current route and saved route.
     * Only alphanumeric  characters in current URI are allowed.
     * Example#1: current URI - /example/12/abc compared to saved route - /example/{id}/{name}, with two variable parameters {id} and {name}. Result - Route.
     * Example#2: current URI - /example/12 compared to saved route - /example/{id}/{name}, with two variable parameters {id} and {name}. Result - null.
     * @param string $uri
     * @param string $method
     * @return Route|null
     */
    private function findRoute(string $uri, string $method): ?Route
    {
        if (isset($this->routes[$method][$uri])) {

            return $this->routes[$method][$uri];
        }

        $currentUriParts = array_filter(explode('/', $uri), 'ctype_alnum');

        foreach($this->routes[$method] as $savedRoute) {

            if(!$savedRoute->getUriParams()) continue;

            $savedRouteParts = explode('/', $savedRoute->getUri());
            array_shift($savedRouteParts);

            if(count($currentUriParts) !== count($savedRouteParts)) {
                continue;
            }

            $differentParts = array_diff($currentUriParts, $savedRouteParts);

            $savedRouteParams = $savedRoute->getUriParams();
            if(count($differentParts) !== count($savedRouteParams)) {
                continue;
            }

            $route = $this->routes[$method][$savedRoute->getUri()];

            return $route;
        }

        return null;
    }

    /**
     * Get URI parameters of the current route
     * @param $uri
     * @param $currentRoute
     * @return array
     */
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