<?php

require __DIR__.'/../vendor/autoload.php';

if (! function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return __DIR__.'/fixtures/config'.($path !== '' ? DIRECTORY_SEPARATOR.$path : '');
    }
}

if (! function_exists('resource_path')) {
    function resource_path(string $path = ''): string
    {
        return __DIR__.'/fixtures/resources'.($path !== '' ? DIRECTORY_SEPARATOR.$path : '');
    }
}
