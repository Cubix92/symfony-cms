<?php

use Eventviva\ImageResize;

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/../app/autoload.php';

$allowedSizes = array(
    'regular' => '450x300', 'banner' => '1872x800', 'logo' => '320x85', 'minibanner' => '234x100',
    'thumbnail' => '100x100'
);

$file = $_SERVER['REQUEST_URI'];

if(!file_exists($file)) {
    if(key_exists($_GET['size'], $allowedSizes)) {
        $originalFile = __DIR__ . str_replace($_GET['size'] . '~', '', $file);
        $size = explode('x', $allowedSizes[$_GET['size']]);

        $image = new ImageResize( $originalFile );
        $image->crop($size[0], $size[1])
            ->save(__DIR__ . $file)
            ->output();
    }
}


