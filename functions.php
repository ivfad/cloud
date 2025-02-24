<?php


/**
 * @param $value
 * @return void
 */
function dd($value): void
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';

    die();
}


