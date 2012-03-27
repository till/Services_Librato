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
     * Get an array representation of the metrics.
     *
     * @return array
     */
    public function toArray()
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
        return $response;
    }

    /**
     * Just like {@link self::toArray()}, but in JSON.
     *
     * @return string
     */
    public function toJson()
    {
        $response = $this->toArray();
        return json_encode($response);
    }
}
