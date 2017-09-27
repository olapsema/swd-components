<?php

namespace Swd;

use Swd\Json\JsonException;

class Json
{

    static function encode($value,$options = 0, $depth = 512)
    {
        $result = json_encode($value,$options,$depth);

        if($result === false && $value !== false){
            throw JsonException::create($value);
        }

        return $result;
    }

    static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        $result = json_decode($json, $assoc, $depth, $options);

        if($result === null && json_last_error() != JSON_ERROR_NONE){
            throw JsonException::create($json);
        }

        return $result;
    }
}
