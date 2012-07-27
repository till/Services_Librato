<?php
$rootDir = dirname(__DIR__);

if (file_exists($rootDir . '/vendor/autoload.php')) {
    require $rootDir . '/vendor/autoload.php';
} else {
    require_once dirname(__DIR__) . '/library/Services/Librato/Autoloader.php';
    $loader = new \Services\Librato\Autoloader();
    $loader->register();
}
