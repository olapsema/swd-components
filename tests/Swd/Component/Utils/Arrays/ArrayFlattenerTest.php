<?php

namespace Swd\Component\Utils\Arrays;

class ArrayFlattenerTest extends \PHPUnit_Framework_TestCase {


    /**
     * @dataProvider getItems
     *
     * @param $sample
     * @param $origin
     * @param $delimeter
     */
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


    /**
     * @dataProvider getUnwrapItems
     *
     * @param $sample
     * @param $origin
     * @param $level
     */
    public function testUnwrap($sample,$origin,$level)
    {
        $result = ArrayFlattener::unwrap($sample,$level);
        $this->assertEquals($origin,$result);

    }

    public function getUnwrapItems()
    {
        $result = array();

        $result [] = array(
            //sample
            array(//level0
                array (
                     //level1
                     'id'=>12,
                     'alfs'=>array(
                             //level2
                             'a' => array(
                                 'info'=>'x',
                                 'num'=>array(
                                    //level3
                                     1 => array(
                                         'cx'=>1,
                                     ),
                                     2 => array(
                                         'dx'=>2,
                                         'ex'=>2,
                                     )
                                 )
                             )
                     )
                 )
             ),
            //origin
             array(
                 array(
                     'id'=>12,
                     'info'=>'x',
                     'alfs'=>'a',
                     'num' => 1,
                     'cx'=>1
                 ),
                 array(
                     'id'=>12,
                     'info'=>'x',
                     'alfs'=>'a',
                     'num' => 2,
                     'dx'=>2,
                     'ex'=>2,
                 ),
                 //array(
                     //'id'=>12,
                     //'info'=>'x',
                     //'alfs'=>'a',
                     //'num' => 2,
                     //'ex'=>2,
                 //)
             ),
             //level
             false
         );


        $result[] = array(
            //sample


            array(//level0
                array(//level1
                    "program_id"=> 123,
                    "date"=> array(
                      "2014-02-02" => array (//level2
                        "events"=> array("hit"=>1)
                       ),
                      "2014-02-03"=>  array(
                        "events"=>  array(
                          "hit"=> 1,
                          "activation"=> 1,
                        )
                      )
                    )
                ),
                array(
                    "program_id"=> 124,
                    "date"=>  array(
                      "2014-02-03"=>  array(
                        "events"=>  array(
                          "hit"=> 1,
                          "activation"=> 1
                        )
                      )
                    )
                ),
            ),
            //origin
            array(
                array(
                    "program_id"=> 123,
                    "date"=>'2014-02-02',
                    "events" =>array("hit"=>1),
                ),
                array(
                    "program_id"=> 123,
                    "date"=>'2014-02-03',
                    "events"=>  array(
                      "hit"=> 1,
                      "activation"=> 1,
                    )
                ),
                array(
                    "program_id"=> 124,
                    "date"=>'2014-02-03',
                    "events"=>  array(
                      "hit"=> 1,
                      "activation"=> 1
                    )
                ),

            ),
            //level
           3
        );



        //max level
        $result [] = array(
            array(
                array(
                    "program_id"=> 123,
                    "date"=> array(
                          "2014-02-03"=>  array(
                            "events"=>  array(     "hit"=> 1,   "activation"=> 1,        )
                           )
                    )
                )
            ),
            array(
                array(
                    "program_id"=> 123,
                    "date" => "2014-02-03",
                    "events"=>  array(     "hit"=> 1,   "activation"=> 1,        )
                ),
            ),
            false //  same as 2
        );


        //no changes
        $result [] = array(
            array(
                array(
                    "program_id"=> 123,
                    "date"=> array(
                          "2014-02-03"=>  array(
                            "events"=>  array(     "hit"=> 1,   "activation"=> 1,        )
                           )
                    )
                )
            ),
            array(
                array(
                    "program_id"=> 123,
                    "date"=> array(
                          "2014-02-03"=>  array(
                            "events"=>  array(     "hit"=> 1,   "activation"=> 1,        )
                           )
                    )
                ),
            ),
            1
        );

        //context saved on 2 level
        $result [] = array(
            array(
                array(//level1
                    "program_id"=> 123,
                    "date"=> array(
                            //level
                          "2014-02-03"=>  array(
                            "events"=>  array(     "hit"=> 1,   "activation"=> 1,        )
                           )
                    )
                )
            ),
            array(
                array(
                    "program_id"=> 123,
                    "date" =>"2014-02-03",
                    "events"=>  array(     "hit"=> 1,   "activation"=> 1,        )
                ),
            ),
            2
        );


        $result [] = array(
            array(
                array(
                    'a'=>1
                )
            ),
            array(
                array(
                    'a'=>1
                )
            ),
            false
        );

        return $result;
    }

}

