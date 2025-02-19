<?php

namespace Core\Foundation\Http;

use Core\Foundation\Helpers\Renderable;
use JetBrains\PhpStorm\NoReturn;

class Response
{
    private static int $status = 200;
    private static string $headers = 'Content-Type: text/html, charset: utf-8';
    private static mixed $content = '';

    public function __construct()
    {
    }

    /**
     * Send a new HTTP response
     * @return Response
     */
    #[NoReturn] public static function send(): Response
    {
        header(self::$headers);
        http_response_code(self::$status);
        echo self::$content;
        exit();
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
     * Processes data depending on its content type.
     * Empty content (e.g. redirect) is not processed additionally.
     * @param $content
     * @return void
     */
    public static function setContent($content): void
    {
        if($content instanceof Renderable) {
            self::$content = $content->getHtml();
        } elseif(isset($content)) {
            self::json($content);
        }
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
     * Setter of the http-header
     * @param $header
     * @return void
     */
    public static function setHeaders($header): void
    {
        self::$headers = $header;
    }

    /**
     * Sets http-status codes, sets content and sends response. Used for errors.
     * @param int $status
     * @param string $content
     * @return void
     */
    #[NoReturn] public static function error(int $status = 404, string $content = ''): void
    {
        self::status($status);
        self::setContent($content);
        self::send();
    }

    /**
     * * Sets http-status codes, sets content, sets headers and sends response. Used for redirects.
     * @param int $status
     * @param string $location
     * @return void
     */
    #[NoReturn] public static function redirect(int $status = 302, string $location = 'location: /'):void
    {
        self::status($status);
        self::setHeaders($location);
        self::send();
    }
}