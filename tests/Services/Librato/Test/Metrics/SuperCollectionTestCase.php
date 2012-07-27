<?php
namespace Services\Librato\Test\Metrics;

use \Services\Librato\Metrics\Metric;
use \Services\Librato\Metrics\Counters;
use \Services\Librato\Metrics\Gauges;
use \Services\Librato\Metrics\SuperCollection;

class SuperCollectionTestCase extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $counter               = new Metric('example.counter');
        $counter->value        = 1;
        $counter->measure_time = 1332848921;

        $counters = new Counters();
        $counters->accept($counter);

        $gauge               = new Metric('some.gauge');
        $gauge->value        = 1;
        $gauge->measure_time = 1332848921;

        $gauges = new Gauges();
        $gauges->accept($gauge);

        $superCollection = new SuperCollection;
        $superCollection->accept($gauges)->accept($counters);

        $payLoad = $superCollection->toArray();
        //var_dump(json_encode($payLoad));
		$this->markTestIncomplete("Not done yet!");
    }
}
