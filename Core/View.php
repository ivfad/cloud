<?php

namespace Core;

class View implements Renderable
{
    private string $template = '';
    private array $data = [];
    private string $html = '';

    public function __construct()
    {
//    public function __construct($template, $data){
//        $this->$template = $template;
//        $this->$data = $data;
    }

//    public function render(): string
    public function render(): Renderable
    {
        ob_start();
//        dd($this->template);
        require_once base_path('src\Views\\' . $this->template);
        $this->html = ob_get_contents();
        ob_end_clean();

        return $this;
//        extract($this->data);
//        include $this->templatePath;
//        return ob_get_clean();
//        $view = require_once base_path("index.view.php');
//        dd(12);
//dd($this->template);
//        dd($this->template);
//        $int = require_once base_path($this->template);
//        dd(17);
//        dd(require_once (base_path($this->template)));
//        dd(25);
//        require_once (base_path($this->template));
//        include (base_path($this->template));
//        dd(15);
//        include 'application/views/' . $this->template;
//        return '12';
    }

    public function getHtml(): string
    {
        return $this->html;

//        require_once base_path($this->template);
//        dd($this->$template);
    }

    public function setTemplate($template): void
    {
        $this->template = $template;

//        require_once base_path($this->template);
//        dd($this->$template);
    }
}