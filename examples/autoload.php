<?php
use \Services\Librato\Autoloader;

$base = dirname(__DIR__);
require_once $base . '/library/Services/Librato/Autoloader.php';

$autoloader = new Autoloader();
$autoloader->register();
