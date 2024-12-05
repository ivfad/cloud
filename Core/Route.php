<?php

namespace Core;

class Route
{
    public function __construct(
        private $uri,
        private $method,
        private $action,
        private $middleware = null,
        private $uriParams = null,
    )
    {
    }

    public static function get(string $uri, $action): static
    {
        return new static($uri, 'GET', $action);
    }

    public static function post(string $uri, $action): static
    {
        return new static($uri, 'POST', $action);
    }

    public static function put(string $uri, $action): static
    {
        return new static($uri, 'PUT', $action);
    }

    public static function patch(string $uri, $action): static
    {
        return new static($uri, 'PATCH', $action);
    }

    public static function delete(string $uri,$action): static
    {
        return new static($uri, 'DELETE', $action);
    }

    public function access($role):self
    {
        $this->middleware = $role;
//        dd($this->middleware);
        return $this;
    }

    public function getUri():string
    {
        return $this->uri;
    }

    public function getMethod():string
    {
        return $this->method;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setUriParams():void
    {
        $uriParts = explode('/', $this->uri);
        array_shift($uriParts); // trims the first empty element
        $characters = ['{', '}'];
        for($i = 0; $i < count($uriParts); $i++) {
            if (preg_match("/[{][\w]+[}]/", $uriParts[$i])) {
                $part = str_replace($characters, '', $uriParts[$i]);
                $this->uriParams[intval($i)] = $part;
            }
        }
    }

    public function getUriParams()
    {
        return $this->uriParams;
    }

    public function getMiddleware()
    {
        return $this->middleware;
    }
    }