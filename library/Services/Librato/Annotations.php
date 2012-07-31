<?php
namespace Services\Librato;

use \Services\Librato;

use \HTTP_Request2 as Request2;
use \HTTP_Request2_Response as HttpResponse;
use \HTTP_Request2_Exception as HttpException;

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
class Annotations extends Librato
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
     * @param string $user   the user
     * @param string $apiKey the API key
     *
     * @return $this
     */
    public function __construct($user, $apiKey)
    {
        $this->user   = $user;
        $this->apiKey = $apiKey;
    }

    /**
     * Create an annotation event.
     *
     * @todo add links?
     *
     * @param string $stream      the annotation stream (doesn't need to exist)
     * @param string $title       the annotation title
     * @param string $source      the annotation source
     * @param string $description the annotation description
     * @param int    $start_time  Unix timestamp for the start of the event
     * @param int    $end_time    Unix timestamp for the end of the event
     *
     * @return boolean
     */
    public function create($stream, $title, $source = null, $description = null,
                           $start_time = null, $end_time = null)
    {
        if ( (empty($stream)) or (empty($title)) ) {
            throw new \InvalidArgumentException('Cannot be empty.');
        }

        $payLoad = array('title' => $title);
        if ($source !== null) {
            $payLoad['source'] = $source;
        }
        if ($description !== null) {
            $payLoad['description'] = $description;
        }
        if ($start_time !== null) {
            $payLoad['start_time'] = $start_time;
        }
        if ($end_time !== null) {
            $payLoad['end_time'] = $end_time;
        }

        $response = $this->makeRequest('/annotations/' . $stream, Request2::METHOD_POST, $payLoad);
        return ($response->getStatus() == 201);
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
}
