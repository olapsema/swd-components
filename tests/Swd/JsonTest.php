<?php

namespace Swd;

use Swd\Json\JsonException;


class JsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Swd\Json\JsonException
     * @dataProvider getWrongJson
     *
     * @param $json
     */
    public function testDecodeException($json)
    {
        Json::decode($json,true);
    }

    public function getWrongJson()
    {
        $result = array();

        $result[] = array('asd asd a');
        $result[] = array('[asd');
        $result[] = array('{{sds}}');

        return $result;
    }

    /**
     * @expectedException \Swd\Json\JsonException
     * @dataProvider getWrongData
     *
     * @param $val
     */
    public function testEncodeException($val)
    {
        Json::encode($val);
    }

    public function getWrongData()
    {
        $result = array();
        $result[] = array(
            ['a'=>mb_convert_encoding('тестовая строка','cp1251')],
        );
        return $result;
    }

    /**
     * @dataProvider getData
     *
     * @param $val
     * @param $orig
     */
    public function testEncode($val,$orig)
    {
        $data =  Json::encode($val);
        $this->assertEquals($orig,$data);
    }

    public function getData()
    {
        $result = array();
        $result[] = array(
            array('a'=>1,'b'=>'test'),
            '{"a":1,"b":"test"}',
        );
        $result[] = array(
            '',
            '""'
        );
        $result[] = array(
            null,
            'null'
        );
        $result[] = array(
            true,
            'true'
        );
        return $result;
    }

    /**
     * @dataProvider getJson
     *
     * @param $json
     * @param $orig
     */
    public function testDecode($json,$orig)
    {
        $data =  Json::decode($json,true);
        $this->assertEquals($orig,$data);
    }

    public function getJson()
    {
        $result = array();
        $result[] = array(
            '{"a":1,"b":"test"}',
            array('a'=>1,'b'=>'test'),
        );
        $result[] = array(
            '""',
            '',
        );
        $result[] = array(
            'null',
            null,
        );
        $result[] = array(
            'true',
            true,
        );
        return $result;
    }
}
