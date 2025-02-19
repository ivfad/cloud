<?php

namespace Core\Foundation\Helpers;

interface Renderable
{
    public function render(): Renderable;
    public function getHtml(): string;
}