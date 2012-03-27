<?php
use \Services\Librato\Metrics;
use \Services\Librato\Metrics\Metric;

require './autoload.php';
if (!file_exists(__DIR__ . '/config.php')) {
    die("No config - check out config.php-dist!");
}
$config = include __DIR__ . '/config.php';

$metrics = new Metrics(
    $config->user,
    $config->apiKey
);

/**
 * @desc Publish some metrics!
 */
$counter1         = new Metric('example-counter1');
$counter1->value  = 1;
$counter1->source = 'test';

$counter2         = new Metric('example-counter2');
$counter2->value  = 2;
$counter2->source = 'test';

$response = $metrics->update(array($counter1, $counter2));
var_dump($response);
