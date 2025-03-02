<?php

namespace Core\Foundation;


abstract class Controller
{
    public Model $model;
    public View $view;

    function __construct()
    {
    }
}