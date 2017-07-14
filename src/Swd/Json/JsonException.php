<?php

namespace Swd\Json;

use RuntimeException;

class JsonException extends RuntimeException
{
    protected $data;

    static $jsonErrors = array(
        JSON_ERROR_NONE           => 'JSON_ERROR_NONE',
        JSON_ERROR_DEPTH          => 'JSON_ERROR_DEPTH',
        JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH',
        JSON_ERROR_CTRL_CHAR      => 'JSON_ERROR_CTRL_CHAR',
        JSON_ERROR_SYNTAX         => 'JSON_ERROR_SYNTAX',
        JSON_ERROR_UTF8           => 'JSON_ERROR_UTF8',
    );

    static function create($data = null, $message = null,$errorCode = null)
    {
        $message  = is_null($message)?json_last_error_msg():$message;
        $errorCode = is_null($errorCode)?json_last_error():$errorCode;

        $exception = new static($message,$errorCode);

        $exception->setData($data);

        return $exception;
    }


    public function getJsonError()
    {
        if(isset(self::$jsonErrors[$this->getCode()])){
            return self::$jsonErrors[$this->getCode()];
        }
        return null;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
