<?php

namespace Core;

class Kernel
{
    public function __construct()
    {

    }

    public function handle(Request $request): Response
    {
        $content = 'Test content';
        return new Response($content);
    }
}