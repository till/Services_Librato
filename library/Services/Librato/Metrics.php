<?php
namespace Services\Librato;

use \Services\Librato;
use \Services\Librato\Metrics\Metric;
use \Services\Librato\Metrics\Collection;
use \Services\Librato\Metrics\SuperCollection;

use \HTTP_Request2 as Request2;
use \HTTP_Request2_Response as HttpResponse;
use \HTTP_Request2_Exception as HttpException;

/**
 * @category   Services
 * @package    Services_Librato
 * @subpackage Services_Librato_Metrics
 * @author     Till Klampaeckel <till@lagged.biz>
 * @version
 * @license
 * @link
 */
class Metrics extends Librato
{
    /**
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @var string $endpoint
     */
    protected $endpoint = 'https://metrics-api.librato.com/v1';

    /**
     * @var string $user Most likely the email address of your account.
     */
    protected $user;

    /**
     * __construct
     *
     * @param string $user
     * @param string $apiKey
     *
     * @return $this
     */
    public function __construct($user, $apiKey)
    {
        if ( (empty($user)) or (empty($apiKey)) ) {
            throw new \InvalidArgumentException('Cannot be empty.');
        }

        $this->user   = $user;
        $this->apiKey = $apiKey;
    }

    /**
     * Delete a metric.
     *
     * @param string $name Name of the metric to delete.
     *
     * @return boolean
     */
    public function delete($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("Cannot be empty.");
        }

        $response = $this->makeRequest('/metrics/' . $name, Request2::METHOD_DELETE);
        if ($response->getStatus() == 204) {
            return true;
        }
        return false;
    }

    /**
     * Return a metric - returns 'all' (first page) if no parameter is set.
     *
     * @param mixed $name
     *
     * @return stdClass
     */
    public function get($name = null)
    {
        $uri = '/metrics';
        if (!empty($name)) {
            $uri .= '/' . $name;
        }
        $response = $this->makeRequest($uri);
        return $this->parseResponse($response);
    }

    /**
     * This updates a metric, or creates it.
     *
     * The best is to supply a Collection or SuperCollection object to get your
     * metrics posted.
     *
     * @param mixed $metrics ... of \Services\Librato\Metrics\Metric.
     *
     * @return true
     * @throws \RuntimeException On empty array collection.
     * @throws \InvalidArgumentException When the metric is not an instance of Metric.
     */
    public function update($metrics)
    {
        if (empty($metrics)) {
            throw new \RuntimeException("No metrics?");
        }

        if (is_array($metrics)) {
            $payLoad  = $this->convertArraytoGauges($metrics);
        } elseif ($metrics instanceof Collection) {
            $payLoad = $metrics->toArray();
        } elseif ($metrics instanceof SuperCollection) {
            $payLoad = $metrics->toArray();
        } else {
            throw new \InvalidArgumentException("The metrics have to be a stacked array or Collection object.");
        }

        $response = $this->makeRequest('/metrics', Request2::METHOD_POST, $payLoad);
        $body     = $this->parseResponse($response);
        if (empty($body)) {
            return true;
        }
        throw new \DomainException("Unknown response: {$body}");
    }

    /**
     * We default to gauges for simplicity.
     *
     * @param array $metrics
     *
     * @return array
     */
    protected function convertArrayToGauges(array $metrics)
    {
        $gauges = array();

        foreach ($metrics as $metric) {
            if (!($metric instanceof Metric)) {
                throw new \InvalidArgumentException("A metric must be of type '\Services\Librato\Metrics\Metric'");
            }
            $gauges[] = $metric->toArray();
        }

        return array('gauges' => $gauges);
    }
}
