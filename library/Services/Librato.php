<?php
namespace Services;

require_once 'HTTP/Request2.php';
use \HTTP_Request2 as Request2;
use \HTTP_Request2_Response as HttpResponse;
use \HTTP_Request2_Exception as HttpException;

/**
 * @author Till Klampaeckel <till@php.net>
 */
class Librato
{
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
