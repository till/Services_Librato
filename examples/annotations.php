<?php
use \Services\Librato\Annotations;

require './autoload.php';
if (!file_exists(__DIR__ . '/config.php')) {
    die("No config - check out config.php-dist!\n");
}
$config = include __DIR__ . '/config.php';

$annotations = new Annotations(
    $config->user,
    $config->apiKey
);

/**
 * @desc Publish some annotations!
 */
var_dump($annotations->get('test-stream'));
var_dump($annotations->create('test-stream', 'test-title1', 'test-source', 'test-desc', time() - 60, time() - 5));
var_dump($annotations->create('test-stream', 'test-title2'));
