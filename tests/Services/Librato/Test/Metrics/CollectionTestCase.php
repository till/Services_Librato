<?php
namespace Services\Librato\Test\Metrics;

use \Services\Librato\Metrics\Metric;
use \Services\Librato\Metrics\Counters;
use \Services\Librato\Metrics\Gauges;

class CollectionTestCase extends \PHPUnit_Framework_TestCase
{
    public function testCounter()
    {
        $counter               = new Metric('example.counter');
        $counter->value        = 1;
        $counter->measure_time = 1332848921;

        $collection = new Counters();
        $collection->accept($counter);

        $this->assertEquals(
            '{"counters":[{"name":"example.counter","value":1,"measure_time":1332848921}]}', 
            $collection->toJson()
        );
    }

    public function testGauges()
    {
        $gauge               = new Metric('some.gauge');
        $gauge->value        = 1;
        $gauge->measure_time = 1332848921;

        $collection = new Gauges();
        $collection->accept($gauge);

        $this->assertEquals(
            '{"gauges":[{"name":"some.gauge","value":1,"measure_time":1332848921}]}', 
            $collection->toJson()
        );
    }
}
