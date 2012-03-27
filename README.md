## Services\Librato

This is a work in progress where I implement the Librato API as I move forward.

Current work in progress: `Services\Librato\Metrics`:

 * delete metrics
 * publish metrics
 * get metrics

## Services\Librato\Metrics

The most interesting bit is publishing metrics. Currently, you can public an individual metric, or multiple.

A metric is created with the following code:

    <?php
    use Services\Librato\Metrics\Metric;
    $metric         = new Metric('counter);
    $metric->value  = 1;
    $metric->source = 'production'; // this is optional

By default my code assumes 'gauges', so in order to send a counter, you'll have to do the following:

    <?php
    use Services\Librato\Metrics;
    use Services\Librato\Metrics\Counters;
    // create metric

    $counters = new Counters();
    $counters->accept($metric);
    
    $metrics = new Metrics('youremail', 'yourapikey');
    var_dump($metrics->update($counters)); // should output 'true'

If you'd like to mix counters and gauges and submit them in one request (preferrable :)) use a `SuperCollection`:

    <?php
    use Services\Librato\Metrics;
    use Services\Librato\Metrics\Counters;
    use Services\Librato\Metrics\Gauges;
    use Services\Librato\Metrics\SuperCollection;
    
    // create metrics
    $counters = new Counters;
    $counters->accept($metric);
    
    $gauges = new Gauges;
    $gauges->accept($anotherMetric);

    $collection = new SuperCollection;
    $collection->accept($counters)->accept($gauges);

    $metrics = new Metrics('youremail', 'yourapikey');
    var_dump($metrics->update($counters)); // should output 'true'

For more, check out the examples directory!

## License

New BSD License.
