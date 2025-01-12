<?php

namespace Core;

class Response
{
    /**
     * Create a new HTTP response.
     * @param mixed $content
     * @param int $statusCode
     * @param string $headers
     */
    public function __construct(
        private mixed $content = '',
        private int $statusCode = 200,
        private string $headers = '',
    )
    {
    }

//    public function send(): void
    public function send(): Response
    {
        header($this->headers);
        http_response_code($this->statusCode);
//        echo $this->content;
//        dd($this->content);
        echo $this->content;
//        dd($this->content);
        exit();
//        return $this;
    }

    public function json(): Response
    {
//        header("Access-Control-Allow-Origin: *");
//        header("Content-Type: application/json; charset=UTF-8");
        $this->setHeaders('Content-Type: application/json, charset: utf-8');
        $this->setContent(json_encode($this->content));
        $this->setStatusCode(202);
        return $this;
    }

    public function setContent($content): void
    {
        if($content instanceof Renderable) {
            $content = $content->render();
        }
        $this->content = $content;
    }

    public function setHeaders($headers = []): void
    {
        $this->headers = $headers;
    }

    public function setStatusCode($statusCode = 200): void
    {
        $this->statusCode = $statusCode;
    }
}