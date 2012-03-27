<?php
namespace Services\Librato\Metrics;

/**
 * @author Till Klampaeckel <till@php.net>
 */
abstract class Collection
{
    /**
     * An Array of {@link \Services\Librato\Metrics\Metric}
     * @var array
     */
    protected $metrics;

    public function accept(Metric $metric)
    {
        if ($this->metrics === null) {
            $this->metrics = array();
        }
        $this->metrics[] = $metric;
        return $this;
    }

    /**
     * Get a JSON-encoded string of metrics.
     *
     * @return string
     */
    public function toJson()
    {
        $type = str_replace(
            'services\librato\metrics\\',
            '',
            strtolower(get_class($this))
        );

        $response        = array();
        $response[$type] = array();

        foreach ($this->metrics as $metric) {
            $response[$type][] = $metric->toArray();
        }
        return json_encode($response);
    }
}
