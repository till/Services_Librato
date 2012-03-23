<?php
namespace Services\Librato;

require_once 'HTTP/Request2.php';
use \HTTP_Request2 as PEARHTTP;
use \HTTP_Request2_Response as PEARHTTP_Response;
use \HTTP_Request2_Exception as PEARHTTP_Exception;

/**
 * @category   Services
 * @package    Services_Librato
 * @subpackage Services_Librato_Metrics
 * @author     Till Klampaeckel <till@lagged.biz>
 * @version
 * @license
 * @link
 */
class Metrics
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

        $response = $this->makeRequest('/metrics/' . $name, PEARHTTP::METHOD_DELETE);
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
     * @param \Services\Librato\Metrics\Metric $metric
     *
     * @return $this
     */
    public function update(\Services\Librato\Metrics\Metric $metric)
    {
        
    }

    /**
     * Issue a request against the REST API.
     *
     * @param string $uri     (absolute)
     * @param string $method  A constant from {@link \HTTP_Request}
     * @param mixed  $payLoad Payload.
     *
     * @return PEARHTTP_Response
     */
    protected function makeRequest($uri, $method = PEARHTTP::METHOD_GET, $payLoad = null)
    {
        static $req = null;
        try {
            if ($req === null) {
                $req = new PEARHTTP;
                $req->setAuth($this->user, $this->apiKey);
            }
            $req->setUrl($this->endpoint . $uri)
                ->setMethod($method);

            /**
             * @desc This is a hack, but why would you not use JSON?
             */
            if ($method == PEARHTTP::METHOD_POST) {
                $req->setHeader('Content-Type: application/json');
            }
            if ($payLoad !== null) {
                $req->setBody(json_encode($payLoad));
            }

            $response = $req->send();

            return $response;

        } catch (PEARHTTP_Exception $e) {
            throw Exception("Most likely a runtime issue.", null, $e);
        }
    }

    /**
     * Parse the response!
     *
     * @param PEARHTTP_Response $response
     *
     * @return stdClass
     * @throws \RuntimeException When the API returns an error.
     */
    protected function parseResponse(PEARHTTP_Response $response)
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
