<?php

namespace Core\Router;

use Closure;

class Route
{
    /**
     * Class used to define the form of description of routes with its URIs, http-methods, actions and middleware roles.
     * List of routes is set in src/routes.php file
     *
     */

    public function __construct(
        private readonly string $uri,
        private readonly string $method,
        private readonly array|Closure $action,
        private ?string $middleware = null,
        private ?array $uriParams = null,
    )
    {
    }

    /**
     * Create a route with get method
     * @param string $uri
     * @param $action
     * @return static
     */
    public static function get(string $uri, $action): static
    {
        return new static($uri, 'GET', $action);
    }

    /**
     * Create a route with post method
     * @param string $uri
     * @param $action
     * @return static
     */
    public static function post(string $uri, $action): static
    {
        return new static($uri, 'POST', $action);
    }

    /**
     * Create a route with put method
     * @param string $uri
     * @param $action
     * @return static
     */
    public static function put(string $uri, $action): static
    {
        return new static($uri, 'PUT', $action);
    }

    /**
     * Create a route with patch method
     * @param string $uri
     * @param $action
     * @return static
     */
    public static function patch(string $uri, $action): static
    {
        return new static($uri, 'PATCH', $action);
    }

    /**
     * Create a route with delete method
     * @param string $uri
     * @param $action
     * @return static
     */
    public static function delete(string $uri, $action): static
    {
        return new static($uri, 'DELETE', $action);
    }


    /**
     * Get route's uri
     * @return string
     */
    public function getUri():string
    {
        return $this->uri;
    }

    /**
     * Get route's method
     * @return string
     */
    public function getMethod():string
    {
        return $this->method;
    }

    /**
     * Get route's action
     * @return array|Closure
     */
    public function getAction(): array|Closure
    {
        return $this->action;
    }

    /**
     * Set route's middleware-role
     * @param $role
     * @return $this
     */
    public function access($role):self
    {
        $this->middleware = $role;

        return $this;
    }

    /**
     * Get route's middleware-role
     * @return string|null
     */
    public function getMiddleware(): ?string
    {
        return $this->middleware;
    }

    /**
     * Set routes variable parameters like {id}
     * @return void
     */
    public function setUriParams():void
    {
        $uriParts = explode('/', $this->uri);
        array_shift($uriParts);
        $characters = ['{', '}'];

        for($i = 0; $i < count($uriParts); $i++) {
            if (preg_match("/[{][\w]+[}]/", $uriParts[$i])) {
                $part = str_replace($characters, '', $uriParts[$i]);
                $this->uriParams[intval($i)] = $part;
            }
        }
    }

    /**
     * Get routes variable parameters like {id}
     * @return array|null
     */
    public function getUriParams(): ?array
    {

        return $this->uriParams;
    }


    }