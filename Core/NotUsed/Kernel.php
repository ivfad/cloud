<?php

namespace Core\NotUsed;

use Core\Foundation\Http\Request;
use Core\Foundation\Http\Response;

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