<?php

namespace Core\Foundation;

use Core\Helpers\Renderable;

class View implements Renderable
{
    private string $template = '';
    private array $data = [];
    private string $html = '';

    public function __construct()
    {
    }

    /** Loads and executes the specified template and saves the result in $html variable
     * @return Renderable
     */
    public function render(): Renderable
    {
        ob_start();
        $slash = DIRECTORY_SEPARATOR;
        require_once BASE_PATH . 'src' . $slash . 'Templates' . $slash . $this->template;
        $this->html = ob_get_contents();
        ob_end_clean();

        return $this;
    }

    /**
     * Getter of html-content of view
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Setter of html-template for view
     * @param $template
     * @return void
     */
    public function setTemplate($template): void
    {
        $this->template = $template;
    }
}