<?php

namespace Swd\Component\Utils\Arrays;

class ArrayMolderTest extends \PHPUnit_Framework_TestCase {


    /**
     * @dataProvider getReplaceItems
     *
     * @param $sample
     * @param $origin
     * @param $delimeter
     */
    public function testReplaceKeys($sample,$keys,$origin)
    {

        $result = ArrayMolder::replaceKeys($sample,$keys);
        $this->assertEquals($origin,$result);

    }

    public function getReplaceItems()
    {
        //this samples tests result order
        $result = array();

        $result[] = array(
            array('name' =>'abc',
                  'events' => 12
            ),
            array('name'=>'test_name'),

            array('test_name' =>'abc',
                  'events' => 12
            ),
        );


        return $result;
    }

    public  function testCollectIterator()
    {
        $d = new \ArrayIterator([['id'=>1],['id'=>17]]);
        
        $result = ArrayMolder::collect($d);
        
        $this->assertEquals([1,17],$result);
    }
}

