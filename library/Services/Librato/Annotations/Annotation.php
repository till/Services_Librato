<?php
namespace Services\Librato\Annotations;

/**
 * @category   Services
 * @package    Services_Librato
 * @subpackage Services_Librato_Annotations
 * @author     Ulf Härnhammar <ulfharn@gmail.com>
 * @author     Till Klampaeckel <till@lagged.biz>
 * @version
 * @license
 * @link
 */
class Annotation
{
    /**
     * @var array
     */
    protected $payLoad = array(
        'title'      => null,
        'source'     => null,
        'start_time' => null,
        'end_time'   => null,
        'rel'        => null,
        'href'       => null,
        'label'      => null,
    );

    /**
     * @var string
     */
    protected $stream;

    /**
     * CTOR
     *
     * @param string $title  The name of the annotation.
     * @param string $stream The stream.
     *
     * @throws \InvalidArgumentException When one is empty.
     */
    public function __construct($title, $stream)
    {
        if (empty($stream) or empty($title)) {
            throw new \InvalidArgumentException('Cannot be empty.');
        }

        $this->payLoad['title'] = $title;
        $this->stream           = $stream;
    }

    /**
     * Set the payload variables.
     *
     * @param string $var
     * @param string $value
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When unknown variable is set.
     * @throws \InvalidArgumentException When the timestamp value is not an int.
     */
    public function __set($var, $value)
    {
        if (false === array_key_exists($var, $this->payLoad)) {
            throw new \InvalidArgumentException("Cannot set '$var'.");
        }

        static $timestamps = array('start_time', 'end_time');
        if (true === in_array($var, $timestamps)) {
            if (false === is_int($value)) {
                throw new \InvalidArgumentException("Value for '$var' must be a unix timestamp.");
            }
        }

        $this->payLoad[$var] = $value;
        return $this;
    }

    /**
     * Get the name of the stream.
     *
     * @return string
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Used to generate the payload!
     *
     * @return array
     */
    public function toArray()
    {
        $keep = array_filter($this->payLoad, function($value) {
            if ($value !== null) {
                return $value;
            }
        });

        return $keep;
    }
}
