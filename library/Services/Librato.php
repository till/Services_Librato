<?php
namespace Services;

require_once 'HTTP/Request2.php';
use \HTTP_Request2 as Request2;
use \HTTP_Request2_Response as HttpResponse;
use \HTTP_Request2_Exception as HttpException;

/**
 * @author Till Klampaeckel <till@php.net>
 */
abstract class Librato
{
    /**
     * HTTP client object
     * @var \HTTP_Request2 $req
     */
    protected $req;

    /**
     * @param mixed $mixed
     *
     * @return Librato
     * @throws \InvalidArgumentException
     */
    public function accept($mixed)
    {
        if ($mixed instanceof Request2) {
            $this->req = $mixed;
            return $this;
        }
        throw new \InvalidArgumentException('Unknown value.');
    }

    /**
     * @param string $user
     * @param string $apiKey
     *
     * @return Request2
     */
    public function getRequest($user, $apiKey)
    {
        if ($this->req === null) {
            $this->req = new Request2;
            $this->req->setAdapter('curl')
                ->setHeader('user-agent', 'Services_Librato');
        }
        $this->req->setAuth($user, $apiKey);
        return $this->req;
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
            throw new \Exception('Most likely a runtime issue.', null, $e);
        }
    }

    /**
     * Parse the response!
     *
     * @param HttpResponse $response
     *
     * @return stdClass (if it can't parse the JSON, it will create
     *                  a new stdClass)
     * @throws \RuntimeException When the API returns an error.
     */
    protected function parseResponse(HttpResponse $response)
    {
        $json = $response->getBody();
        $body = $bodyfix = @json_decode($json);
        if (empty($bodyfix)) {
            $bodyfix = new stdClass;
            $bodyfix->empty = 'yes';
        }

        if ($response->getStatus() == 200) {
            return $bodyfix;
        }

        $message = '';
        if ($body === false || $body === null) {
            $message = $json;
        } else {
            $errors = $body->errors;
            foreach ($errors as $error) {
                if (!empty($message)) {
                    $message .= ', ';
                }
                if (is_string($error)) {
                    $message .= $error;
                }
            }
        }
        throw new \RuntimeException($message);
        return $bodyfix;
    }
}
