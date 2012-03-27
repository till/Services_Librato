<?php
use \Services\Librato\Metrics;
use \Services\Librato\Metrics\Metric;
use \Services\Librato\Metrics\Counters;
use \Services\Librato\Metrics\Gauges;
use \Services\Librato\Metrics\SuperCollection;

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
 * @desc Publish metrics of different types: counters and gauges.
 */
$counter1         = new Metric('example-counter1');
$counter1->value  = 1;
$counter1->source = 'test';

$counter2         = new Metric('example-counter2');
$counter2->value  = 2;
$counter2->source = 'test';

$counters = new Counters;
$counters->accept($counter1)->accept($counter2);

$gauge        = new Metric('example-gauge');
$gauge->value = rand(1,10);

$gauges = new Gauges;
$gauges->accept($gauge);

$collection = new SuperCollection;
$collection->accept($counters)->accept($gauges);

$response = $metrics->update($collection);
var_dump($response);
