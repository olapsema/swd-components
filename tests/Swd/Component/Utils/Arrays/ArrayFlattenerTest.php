<?php

namespace Swd\Component\Utils\Arrays;

class ArrayFlattenerTest extends \PHPUnit_Framework_TestCase {



    /**
     * @dataProvider getItems
     *
     **/
    public function testFlattenItem($sample,$origin,$delimeter)
    {

        $result = ArrayFlattener::flattenItem($sample,$delimeter);
        $this->assertEquals($origin,$result);

    }

    public function getItems()
    {
        //this samples tests result order
        $result = array();

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2)
            ),
            array('name'=>'abc','hit'=>1,'duck'=>2),
            false
        );

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2),
                  'text' => 'blabla'

            ),
            array('name'=>'abc','text'=>'blabla','hit'=>1,'duck'=>2),
            false
        );

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2),
                  'text' => 'blabla'

            ),
            array('name'=>'abc','text'=>'blabla','events.hit'=>1,'events.duck'=>2),
            '.'
        );

        // sub->sub array

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2,'sub-duck'=>array('xyz'=>123,'yad'=>456)),
                  'text' => 'blabla'

            ),
            array(
                'name'=>'abc',
                'text'=>'blabla',
                'events.hit'=>1,
                'events.duck'=>2,
                'events.sub-duck.xyz'=>123,
                'events.sub-duck.yad'=>456,
            ),
            '.'
        );

        //order test

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2),
                  'inner-value' => 'sss',
                  'none-events'=>array('xyz'=>123,'yad'=>456),
                  'text' => 'blabla',

            ),
            array(
                'name'=>'abc',
                'text'=>'blabla',
                'events.hit'=>1,
                'events.duck'=>2,
                'inner-value' => 'sss',
                'none-events.xyz'=>123,
                'none-events.yad'=>456,
            ),
            '.'
        );

        // another delimeter

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2),
                  'inner-value' => 'sss',
                  'none-events'=>array('xyz'=>123,'yad'=>456),
                  'text' => 'blabla',

            ),
            array(
                'name'=>'abc',
                'text'=>'blabla',
                'events__hit'=>1,
                'events__duck'=>2,
                'inner-value' => 'sss',
                'none-events__xyz'=>123,
                'none-events__yad'=>456,
            ),
            '__'
        );

        // value replace

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2),
                  'text' => 'blabla',
                  'hit'=> 'this value will be replaced',

            ),
            array(
                'name'=>'abc',
                'text'=>'blabla',
                'hit'=>1,
                'duck'=>2
            ),
            false
        );

        $result[] = array(
            array('name' =>'abc',
                  'events' => array('hit'=>1,'duck'=>2,'sub'=>array('hit'=>'locked')),
                  'text' => 'blabla',
                  'hit'=> 'this value will be replaced',

            ),
            array(
                'name'=>'abc',
                'text'=>'blabla',
                'hit'=>'locked',
                'duck'=>2
            ),
            false
        );

        return $result;
    }


    public function testFlatten()
    {
        $sample = array(
            array(
                'name'=>'test',
                'events'=> array('hit'=>1),
            ),
            array(
                'name'=>'test2',
                'events'=> array('duck'=>1,'blood'=>'rain'),
            )
        );

        $origin = array(
            array(
                'name'=>'test',
                'hit'=>1
            ),
            array(
                'name'=>'test2',
                'duck'=>1,
                'blood'=>'rain'
            )
        );

        $result = ArrayFlattener::flatten($sample,false);
        $this->assertEquals($origin,$result);


    }

}

