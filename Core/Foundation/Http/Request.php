<?php

namespace Core\Foundation\Http;

class Request
{
    /**
     * @var array The list of allowed HTTP methods, besides GET and POST
     */
    private static array $methodsList = ['PUT', 'PATCH', 'DELETE'];

    /**
     * @param array $getParams The GET parameters
     * @param array $postParams The POST parameters
     * @param array $cookies The COOKIE parameters
     * @param array $files The FILES parameters
     * @param array $server The SERVER parameters
     */
    public function __construct(
        private readonly array $getParams,
        private readonly array $postParams,
        private readonly array $cookies,
        private readonly array $files,
        private readonly array $server,
    )
    {
    }

    /**
     * Create a request, based on the current PHP global variables
     * @return static
     */
    public static function createFromGlobals(): static
    {
        if(in_array($_SERVER['REQUEST_METHOD'], self::$methodsList)) {
            $postParams = self::handleRawContent();
        } else {
            $postParams = $_POST;
        }
        return new static($_GET, $postParams, $_COOKIE, $_FILES, $_SERVER);
    }

    /**
     * Getter of $_GET parameters of current request
     * @return array
     */
    public function get(): array
    {
        return $this->getParams;
    }

    /**
     * Getter of $_POST parameters of current request
     * @return array
     */
    public function post(): array
    {
        return $this->postParams;
    }

    /**
     * Parse request URL and return its path
     * @return string
     */
    public function uri(): string
    {
        return parse_url($this->server['REQUEST_URI'])['path'] ?? '/';
    }

    /**
     * Get current request's method
     * @return string
     */
    public function method(): string
    {
        if (isset($_POST['_method']) && in_array($_POST['_method'], self::$methodsList)) {
            return $_POST['_method'];
        }

        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get files from the request
     * @return array
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * Converts raw input to $postParams array. Applied to http-methods from $methodsList like 'PUT'
     * @return array
     */
    private static function handleRawContent(): array
    {
        $raw = file_get_contents("php://input");

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $boundaryPos = strpos($contentType, 'boundary=');

        if ($boundaryPos === false) {
            return [];
        }

        $boundary = '--' . substr($contentType, $boundaryPos + strlen('boundary='));
        $blocks = explode($boundary, $raw);
        array_pop($blocks);

        $data = [];

        foreach ($blocks as $block) {
            $block = trim($block);

            if (empty($block)) continue;

            $namePos = strpos($block, 'name="');

            if ($namePos === false) continue;

            $nameStart = $namePos + 6;
            $nameEnd = strpos($block, '"', $nameStart);
            $name = substr($block, $nameStart, $nameEnd - $nameStart);

            $valuePos = strpos($block, "\r\n\r\n");

            if ($valuePos === false) continue;

            $valueStart = $valuePos + 4;
            $value = substr($block, $valueStart);

            $value = rtrim($value, "\r\n");

            $data[$name] = $value;
        }

        return $data;
    }
}