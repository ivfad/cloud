<?php

namespace Core\Foundation\Http;

use Core\Helpers\Renderable;

class Response
{
    private static int $status = 200;
    private static string $headers = 'Content-Type: text/html, charset: utf-8';
    private static mixed $content = '';

    public function __construct()
    {
    }

    /**
     * Sets http-status codes, sets content and sends response.
     * Used for errors
     * @param int $status
     * @param string $content
     * @return void
     */
    public static function error(int $status = 404, string $content = ''): void
    {
        self::status($status);
        self::setContent($content);
        self::send();
    }

    /**
     * Setter of the http-status code
     * @param $code
     * @return void
     */
    public static function status($code): void
    {
        self::$status = $code;
    }

    /**
     * Processes data depending on its content type.
     * Empty content is not processed additionally.
     * @param $content
     * @return void
     */
    public static function setContent($content): void
    {
        if ($content instanceof Renderable) {
            self::$content = $content->getHtml();
        } elseif (isset($content)) {
            self::json($content);
        }
    }

    /**
     * Json-encodes content and sets appropriate header
     * @param $content
     * @return void
     */
    private static function json($content): void
    {
        self::setHeaders('Content-Type: application/json, charset: utf-8');
        self::$content = json_encode($content);
    }

    /**
     * Setter of the http-header
     * @param $header
     * @return void
     */
    public static function setHeaders($header): void
    {
        self::$headers = $header;
    }

    /**
     * Send a new HTTP response
     * @return Response
     */
    public static function send(): Response
    {
        header(self::$headers);
        http_response_code(self::$status);
        echo self::$content;
        exit();
    }

    /**
     * * Sets http-status codes, sets content, sets headers and sends response.
     * Used for redirects
     * @param int $status
     * @param string $location
     * @return void
     */
    public static function redirect(int $status = 302, string $location = 'location: /'): void
    {
        self::status($status);
        self::setHeaders($location);
        self::send();
    }
}