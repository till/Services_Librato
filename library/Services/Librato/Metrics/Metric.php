<?php
namespace Services\Librato\Metrics;

/**
 * This is an OO abstraction around individual metrics.
 */
class Metric
{
    protected $name;

    protected $store = array(
        'value'        => null,
        // these are optional
        'measure_time' => null,
        'source'       => null,
        'display_name' => null,
        'description'  => null,
        'period'       => null,
        'attributes'   => null,
    );

    /**
     * @param string $name
     *
     * @return $this
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Yay - convenience!
     *
     * @param string $var
     * @param mixed  $value
     *
     * @return $this
     * @uses   self::$store
     * @throws \InvalidArgumentException
     */
    public function __set($var, $value)
    {
        if (!array_key_exists($var, $this->store)) {
            throw new \InvalidArgumentException(
                "Unknown property {$var}, we support: " . implode(', ', array_keys($this->store))
            );
        }
        $this->store[$var] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        $array = array('name' => $this->name);
        foreach ($this->store as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $array[$k] = $v;
        }
        return json_encode($array);
    }
}
