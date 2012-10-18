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
 * @desc Delete all the annotations!
 */
$s = $annotations->getStream();
foreach ($s['annotations'] as $st) {
    echo $st['name'] . "\n";
    var_dump($annotations->deleteStream($st['name']));
}

