<?php
namespace Services;

require_once 'HTTP/Request2.php';
use \HTTP_Request2 as Request2;

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
        throw new \InvalidArgumentException("Unknown value.");
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
}
