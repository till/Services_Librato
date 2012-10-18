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
     */
    public function __set($var, $value)
    {
        if (false === array_key_exists($var, $this->payLoad)) {
            throw new \InvalidArgumentException("Cannot set '$var'.");
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
