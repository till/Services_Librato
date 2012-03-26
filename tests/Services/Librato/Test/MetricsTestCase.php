<?php
namespace Services\Librato\Test;

require_once 'HTTP/Request2.php';

use \Services\Librato\Metrics;

class MetricsTestCase extends \PHPUnit_Framework_TestCase
{
    public function testAccept()
    {
        $metrics = new Metrics('foo', 'bar');
        $this->assertSame($metrics, $metrics->accept(new \HTTP_Request2));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAccept()
    {
        $metrics = new Metrics('foo', 'bar');
        $metrics->accept('hello world');
    }

    public function testHttpClient()
    {
        $request = new \HTTP_Request2('http://example.org');

        $metrics = new Metrics('foo', 'bar');
        $metrics->accept($request);

        $this->assertSame($request, $metrics->getRequest('foo', 'bar'));
    }

    public function testNewHttpClient()
    {
        $metrics = new Metrics('foo', 'bar');
        $request = $metrics->getRequest('foo', 'bar');

        $this->assertInstanceOf('\HTTP_Request2', $request);
    }
}
