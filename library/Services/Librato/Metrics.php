<?php
namespace Services\Librato;

require_once 'HTTP/Request2.php';
use \HTTP_Request2 as PEARHTTP;
use \HTTP_Request2_Response as PEARHTTP_Response;
use \HTTP_Request2_Exception as PEARHTTP_Exception;

class Metrics
{
    protected $apiKey;

    protected $endpoint = 'https://metrics-api.librato.com/v1';

    protected $user;

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

        $response = $this->makeRequest('/metrics/' . $name, 'DELETE');
        if ($response->getStatus() == 204) {
            return true;
        }
        return false;
    }

    public function get($name = null)
    {
        $uri = '/metrics';
        if (!empty($name)) {
            $uri .= '/' . $name;
        }
        $response = $this->makeRequest($uri);
        return $this->parseResponse($response);
    }

    protected function makeRequest($uri, $method = \HTTP_Request2::METHOD_GET)
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
     * @param \HTTP_Request2_Response $response
     *
     * @return stdClass
     */
    protected function parseResponse(PEARHTTP_Response $response)
    {
        return json_decode($response->getBody());
    }
}
