<?php
$base = dirname(__DIR__);
require_once $base . '/library/Services/Librato/Metrics.php';

if (!file_exists(__DIR__ . '/config.php')) {
    die("No config - check out config.php-dist!");
}
$config = include __DIR__ . '/config.php';

$metrics = new \Services\Librato\Metrics(
    $config->user,
    $config->apiKey
);

/**
 * @desc Delete all metrics which start with 'blah-foo-miss-'
 */
$filter = 'blah-foo-miss-';

while (true) {
    $gauges = $metrics->get();
    foreach ($gauges->metrics as $metric) {
        if (strpos($metric->name, $filter) === false) {
            continue;
        }
        echo "Deleting {$metric->name}" . PHP_EOL;
        if ($metrics->delete($metric->name) === false) {
            echo "Error deleting: {$metric->name}" . PHP_EOL;
            exit;
        }
    }
    echo "Still left: {$gauges->query->total}" . PHP_EOL;
    if ($gauges->query->total < 20) {
        break;
    }
}
