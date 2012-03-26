<?php
namespace Services\Librato;

use \Services\Librato;
use \Services\Librato\Metrics\Metric;

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
     * @param array $metrics ... of \Services\Librato\Metrics\Metric.
     *
     * @return true
     * @throws \RuntimeException On empty array collection.
     * @throws \InvalidArgumentException When the metric is not an instance of Metric.
     */
    public function update(array $metrics)
    {
        if (empty($metrics)) {
            throw new \RuntimeException("...");
        }

        $gauges = array();

        foreach ($metrics as $metric) {
            if (!($metric instanceof Metric)) {
                throw new \InvalidArgumentException("A metric must be of type '\Services\Librato\Metrics\Metric'");
            }
            $gauges[] = $metric->toArray();
        }
        if (empty($gauges)) {
            throw new \RuntimeException("...");
        }
        $payLoad  = array('gauges' => $gauges);
        $response = $this->makeRequest('/metrics', Request2::METHOD_POST, $payLoad);
        $body     = $this->parseResponse($response);
        if (empty($body)) {
            return true;
        }
        throw new \DomainException("Unknown response: {$body}");
    }

    /**
     * Issue a request against the REST API.
     *
     * @param string $uri     (absolute)
     * @param string $method  A constant from {@link \HTTP_Request}
     * @param mixed  $payLoad Payload.
     *
     * @return HttpResponse
     */
    protected function makeRequest($uri, $method = Request2::METHOD_GET, $payLoad = null)
    {
        try {
            $req = $this->getRequest($this->user, $this->apiKey)
                ->setUrl($this->endpoint . $uri)
                ->setMethod($method);

            /**
             * @desc This is a hack, but why would you not use JSON?
             */
            if ($method == Request2::METHOD_POST) {
                $req->setHeader('Content-Type: application/json');
            }
            if ($payLoad !== null) {
                $req->setBody(json_encode($payLoad));
            }

            $response = $req->send();

            return $response;

        } catch (HttpException $e) {
            throw new Exception("Most likely a runtime issue.", null, $e);
        }
    }

    /**
     * Parse the response!
     *
     * @param HttpResponse $response
     *
     * @return stdClass
     * @throws \RuntimeException When the API returns an error.
     */
    protected function parseResponse(HttpResponse $response)
    {
        $json = $response->getBody();
        $body = @json_decode($json);

        // evaluate response status etc.
        if ($response->getStatus() != 200) {

            $message = '';

            if ($body === false || $body === null) {
                $message .= $json;
            } else {

                $errors = $body->errors;
                foreach ($errors as $error) {
                    if (!empty($message)) {
                        $message .= ', ';
                    }
                    if (is_string($error)) {
                        $message .= $error;
                    } elseif ($error instanceof \stdClass) {
                        $error = (array) $error;
                        foreach ($error as $k => $v) {
                            if (!empty($message)) {
                                $message .= ', ';
                            }
                            $message .= "{$k}: ";
                            if (is_array($v)) {
                                $message .= implode(', ', $v);
                            } else {
                                $message .= $v;
                            }
                        }
                    } else {
                        var_dump($error); exit;
                        $message .= implode(', ', $error);
                    }
                }
            }
            throw new \RuntimeException($message);
        }
        return $body;
    }
}
