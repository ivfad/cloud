<?php

namespace Core\Helpers;

interface Renderable
{
    public function render(): Renderable;

    public function getHtml(): string;
}