<?php
namespace Services\Librato\Testing;

use \Services\Librato\Metrics\Metric;

class MetricTestCase extends \PHPUnit_Framework_TestCase
{
    public function testMetric()
    {
        $metric               = new Metric('foo');
        $metric->value        = 10;
        $metric->period       = '10s';
        $metric->measure_time = 'ts';

        $this->assertEquals('{"name":"foo","value":10,"measure_time":"ts","period":"10s"}', $metric->toJson());
    }

    public function testMeasureTime()
    {
        $metric        = new Metric('counter');
        $metric->value = 1;

        $data = $metric->toArray();
        $this->assertArrayHasKey('measure_time', $data);
        $this->assertInternalType('int', $data['measure_time']);
    }

    /**
     * @expectedException \LogicException
     */
    public function testLogicException()
    {
        $metric = new Metric('test');
        $metric->toArray();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNumericValue()
    {
        $metric        = new Metric('string-value');
        $metric->value = 'hello world';
    }
}
