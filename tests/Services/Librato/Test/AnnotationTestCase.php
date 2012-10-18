<?php
namespace Services\Librato\Test;

use \Services\Librato\Annotations\Annotation;

class AnnotationTestCase extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $annotation = new Annotation('title', 'stream');
        $annotation->start_time = '2012-12-12 00:00:00';

        $payLoad = $annotation->toArray();
        $this->assertInternalType('array', $payLoad);
        $this->assertSame('title', $payLoad['title']);
        $this->assertSame('stream', $annotation->getStream());
        $this->assertSame(array('title' => 'title', 'start_time' => '2012-12-12 00:00:00'), $payLoad);
    }
}
