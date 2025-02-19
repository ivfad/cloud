<?php

namespace Core\Foundation;

use Core\Foundation\Helpers\Renderable;

class View implements Renderable
{
    private string $template = '';
    private array $data = [];
    private string $html = '';

    public function __construct()
    {
    }

    public function render(): Renderable
    {
        ob_start();
        require_once BASE_PATH . 'src\Views\\' . $this->template;
        $this->html = ob_get_contents();
        ob_end_clean();

        return $this;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function setTemplate($template): void
    {
        $this->template = $template;
    }
}