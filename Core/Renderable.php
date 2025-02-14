<?php

namespace Core;

interface Renderable
{
//    public function render(): string;
    public function render(): Renderable;
    public function getHtml(): string;
}