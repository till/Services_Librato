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
     * Issue a request against the REST API.
     *
     * @param string $uri    (absolute)
     * @param string $method A constant from {@link \HTTP_Request}
     *
     * @return PEARHTTP_Response
     */
    protected function makeRequest($uri, $method = PEARHTTP::METHOD_GET)
    {
        try {
            $req = new PEARHTTP;
            $req->setAuth($this->user, $this->apiKey)
                ->setUrl($this->endpoint . $uri)
                ->setMethod($method);

            $response = $req->send();
            return $response;
        } catch (PEARHTTP_Exception $e) {
            var_dump($e); exit;
        }
    }

    /**
     * Parse the response!
     *
     * @param PEARHTTP_Response $response
     *
     * @return stdClass
     */
    protected function parseResponse(PEARHTTP_Response $response)
    {
        return json_decode($response->getBody());
    }
}
