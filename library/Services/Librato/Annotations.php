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
        if ( (empty($user)) or (empty($apiKey)) ) {
            throw new \InvalidArgumentException('Cannot be empty.');
        }

        $this->user   = $user;
        $this->apiKey = $apiKey;
    }

    /**
     * Create an annotation event.
     *
     * @param string $stream      the annotation stream (doesn't need to exist)
     * @param string $title       the annotation title
     * @param string $source      the annotation source
     * @param string $description the annotation description
     * @param int    $start_time  Unix timestamp for the start of the event
     * @param int    $end_time    Unix timestamp for the end of the event
     * @param string $rel         the annotation link relationship
     * @param string $href        the annotation link URL
     * @param string $label       the annotation link label
     *
     * @todo the annotation link support doesn't work, don't know why
     *
     * @return boolean
     */
    public function create($stream, $title, $source = null, $description = null,
                           $start_time = null, $end_time = null, $rel = null,
                           $href = null, $label = null)
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
        if ($rel !== null) {
            $payLoad['rel'] = $rel;
        }
        if ($href !== null) {
            $payLoad['href'] = $href;
        }
        if ($label !== null) {
            $payLoad['label'] = $label;
        }

        $response = $this->makeRequest('/annotations/' . $stream, Request2::METHOD_POST, $payLoad);
        return ($response->getStatus() == 201);
    }

    /**
     * Return an annotation stream - returns all annotation streams if no parameter is set.
     *
     * @param mixed $name
     *
     * @return stdClass
     */
    public function getStream($name = null)
    {
        $uri = '/annotations';
        if (!empty($name)) {
            $uri .= '/' . $name . '?count=99999';
        }
        $response = $this->makeRequest($uri);
        return $this->parseResponse($response, true);
    }

    /**
     * Return an annotation.
     *
     * @param string $name
     * @param int    $id
     *
     * @return stdClass
     */
    public function get($name, $id)
    {
        $uri = "/annotations/$name/$id";
        $response = $this->makeRequest($uri);
        return $this->parseResponse($response, true);
    }

    /**
     * Delete an annotation stream.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function deleteStream($name)
    {
        $uri = "/annotations/$name";
        $response = $this->makeRequest($uri, Request2::METHOD_DELETE);
        return ($response->getStatus() == 204);
    }
}
