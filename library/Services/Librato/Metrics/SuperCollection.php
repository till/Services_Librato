<?php
namespace Services\Librato\Metrics;

use \Services\Librato\Metrics\Collection;

/**
 * @author Till Klampaeckel <till@php.net>
 */
class SuperCollection
{
    protected $collections = array();

    /**
     * @param Collection $collection
     *
     * @return $this
     */
    public function accept(Collection $collection)
    {
        $this->collections[] = $collection;
        return $this;
    }

    public function toArray()
    {
        $response = array();
        foreach ($this->collections as $collection) {
            $arr = $collection->toArray();
            $response[key($arr)] = current($arr);
        }
        return $response;
    }
}
