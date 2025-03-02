<?php

namespace Core\Router;

use Core\Exceptions\AuthenticationException;
use Core\Exceptions\AuthorizationException;
use Core\Exceptions\MiddlewareRoleException;
use Core\Exceptions\RouteNotFoundException;
use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;
use Core\Helpers\ActionFactory;
use Core\Middleware\Middleware;
use Exception;
use Throwable;

class Router
{
    /**
     * Class is used to compare current URI with the list of preset routes,
     * start the verification of permission to view the content of the called route
     * and apply necessary controller to the route
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
     * Handles the request by comparing current URI with the list of preset routes,
     * checking permission to view the content and applying a necessary action from ActionFactory
     * @param Request $request
     * @return mixed
     */
    public function dispatch(Request $request): mixed
    {
        try {
            $currentRoute = $this->findRoute($request->uri(), $request->method(), $request->get());

            if (!$currentRoute) {
                throw new RouteNotFoundException('Route does not exist');
            }
            $this->checkAccess($currentRoute);

            $action = $currentRoute->getAction();
            $callableAction = ActionFactory::create($action);

            $params = $this->getParams($request->uri(), $currentRoute);

            return call_user_func($callableAction, $request, $params);

        } catch (AuthenticationException $e) {
            Response::error(401, $e->getMessage());
        } catch (AuthorizationException $e) {
            Response::error(403, $e->getMessage());
        } catch (RouteNotFoundException $e) {
            Response::error(404, $e->getMessage());
        } catch (MiddlewareRoleException|Exception $e) {
            Response::error(500, $e->getMessage());
        } catch (Throwable $e) {
            Response::error(500, 'Internal Server Error:' . $e->getMessage());
        }
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
    private function findRoute(string $uri, string $method, $params): ?Route
    {
        if (isset($this->routes[$method][$uri])) {

            return $this->routes[$method][$uri];
        }

        $currentUriParts = array_filter(explode('/', $uri), 'ctype_alnum');

        foreach ($this->routes[$method] as $savedRoute) {

            if (!$savedRoute->getUriParams()) continue;

            $savedRouteParts = explode('/', $savedRoute->getUri());
            array_shift($savedRouteParts);


            if (count($currentUriParts) !== count($savedRouteParts)) {
                continue;
            }

            $differentParts = array_diff($currentUriParts, $savedRouteParts);

            $savedRouteParams = $savedRoute->getUriParams();
            if (count($differentParts) !== count($savedRouteParams)) {
                continue;
            }
            $route = $this->routes[$method][$savedRoute->getUri()];

            return $route;
        }

        return null;
    }

    /**
     * Checks access rights to the requested resource.
     * @param Route $route
     * @return void
     * @throws MiddlewareRoleException
     */
    private function checkAccess(Route $route): void
    {
        $role = $route->getMiddleware() ?? false;
        if ($role) {
            Middleware::resolve($role);
        }
    }

    /**
     * Get URI parameters of the current route
     * @param string $uri
     * @param Route $currentRoute
     * @return array
     */
    private function getParams(string $uri, Route $currentRoute): array
    {
        $uriParts = explode('/', $uri);
        array_shift($uriParts);
        $parameters = [];

        if ($currentRoute->getUriParams()) {
            foreach ($currentRoute->getUriParams() as $key => $paramName) {
                $parameters[$paramName] = $uriParts[$key];
            }
        }

        return $parameters;
    }
}