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
     * @param int $code
     * @return void
     */
    public static function status(int $code): void
    {
        self::$status = $code;
    }

    /**
     * Processes data depending on its content type.
     * Empty content is not processed additionally.
     * @param mixed $content
     * @return void
     */
    public static function setContent(mixed $content): void
    {
        if ($content instanceof Renderable) {
            self::$content = $content->getHtml();
        } elseif (isset($content)) {
            self::json($content);
        }
    }

    /**
     * Json-encodes content and sets appropriate header
     * @param mixed $content
     * @return void
     */
    private static function json(mixed $content): void
    {
        self::setHeaders('Content-Type: application/json, charset: utf-8');
        self::$content = json_encode($content);
    }

    /**
     * Setter of the http-header
     * @param string $header
     * @return void
     */
    public static function setHeaders(string $header): void
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
     * Sets http-status codes, sets content, sets headers and sends response.
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

    /**
     *  Sets http-status codes, sets content, sets headers for delivering a file.
     *  Used for sending files
     * @param int $status
     * @param string $file
     * @param string|null $filename
     * @return void
     */
    public static function sendFile(int $status = 200, string $file, string $filename = null): void
    {
        self::status($status);
        $filename = $filename ?? basename($file);
        self::setHeaders('Content-Type: application/octet-stream');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . $filename  . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        readfile($file);

        self::send();
    }
}