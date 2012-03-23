<?php
namespace Services\Librato\Testing;

use \Services\Librato\Metrics\Metric;

class MetricTestCase extends \PHPUnit_Framework_TestCase
{
    public function testMetric()
    {
        $metric = new \Services\Librato\Metrics\Metric('foo');
        $metric->value  = 10;
        $metric->period = '10s';

        $this->assertEquals('{"name":"foo","value":10,"period":"10s"}', $metric->toJson());
    }
}
