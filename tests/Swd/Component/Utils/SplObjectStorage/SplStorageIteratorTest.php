<?php

namespace Swd\Component\Utils;

class SplStorageIteratorTest extends \PHPUnit_Framework_TestCase
{


    public function testGetDataIterator()
    {
        $a = new \stdClass();
        $a->id =1;
        $b = new \stdClass();
        $b->id =2;

        $storage = new SplObjectStorage();
        $storage->attach($a,'first');
        $storage->attach($b,'second');

        $this->assertEquals(['first','second'],iterator_to_array($storage->getDataIterator()));
    }

    public function testGetObjectsIterator()
    {
        $a = new \stdClass();
        $a->id =1;
        $b = new \stdClass();
        $b->id =2;

        $storage = new SplObjectStorage();
        $storage->attach($a,'first');
        $storage->attach($b,'second');

        $this->assertEquals([$a,$b],iterator_to_array($storage->getObjectsIterator()));
    }
    
}
