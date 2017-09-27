<?php

namespace Swd\Component\Utils\Arrays;

use  Swd\Component\Utils\Inflector;
use Traversable, ArrayAccess;

class  ArrayMolder
{

    /**
     * Переименовывает ключи в массиве
     *
     * @param array $arr
     * @param array $replaceWith
     * @return array
     */
    public static function replaceKeys(array $arr, array $replaceWith)
    {
        foreach ($replaceWith as $key => $sub) {
            if (isset($arr[$key])) {
                $arr[$sub] = $arr[$key];
                unset($arr[$key]);
            }
        }

        return $arr;
    }

    /**
     *  Возвращает ассоциативный массив вида ключ =>  ряд
     * @param $arrayValue
     * @param string $field
     * @param bool $multiple
     * @return array
     */
    static public function indexArray($arrayValue, $field = "id", $multiple = false)
    {
        if (!is_array($arrayValue) && !($arrayValue instanceof ArrayAccess)) {
            return [];
        }

        $result = [];

        if (strlen($field) > 0 && is_array($arrayValue)) {
            foreach ($arrayValue as $row) {
                if ($multiple) {
                    $result[$row[$field]][] = $row;
                } else {
                    $result[$row[$field]] = $row;
                }
            }
        }

        return $result;
    }

    /**
     *  Возвращает плоский массив вида ключ => значение
     *
     * @param $arrayValue
     * @param string $keyField
     * @param string $field
     * @return array
     */
    static public function flattenArray(&$arrayValue, $keyField = "id", $field = "name")
    {
        if (!is_array($arrayValue) && !($arrayValue instanceof ArrayAccess)) {
            return [];
        }

        $result = [];
        if (strlen($field) > 0 && strlen($keyField) > 0) {
            foreach ($arrayValue as $row) {
                $result[$row[$keyField]] = $row[$field];
            }
        }

        return $result;
    }

    /**
     * Возвращает массив значений $key
     *
     * @param $arrayValue
     * @param string $key
     * @return array
     */
    static public function collectArrayValue($arrayValue, $key = 'id')
    {
        if (!is_array($arrayValue) && !($arrayValue instanceof ArrayAccess)){
            return [];
        }

        $result = [];

        foreach ($arrayValue as $val) {
            if (isset($val[$key])) {
                array_push($result, $val[$key]);
            }
        }

        return $result;
    }

    /**
     *  Возвращает плоский массив вида ключ => array(значение1,...)
     *  работает с объектами
     *
     * @param $arrayObj
     * @param string $keyField
     * @param string $field
     * @param bool $suppressError
     * @return array
     * @throws \Exception
     */
    static public function flattenMultiple($arrayObj, $keyField = "id", $field = "name", $suppressError = true)
    {
        if (!is_array($arrayObj) && !($arrayObj instanceof Traversable))
            return array();

        $method = Inflector::camelize("get_" . $field);
        $keyMethod = Inflector::camelize("get_" . $keyField);

        $result = array();
        foreach ($arrayObj as $row) {

            if (is_object($row)) {
                if (!method_exists($row, $method) && !$suppressError) {
                    throw new \Exception(sprintf("Method %s::%s not exists", get_class($row), $method));
                }
                if (!method_exists($row, $keyMethod) && !$suppressError) {
                    throw new \Exception(sprintf("Method %s::%s not exists", get_class($row), $keyMethod));
                }
                $key = $row->$keyMethod();
                $value = $row->$method();

            } else {
                $key = $row[$keyField];
                $value = $row[$field];
            }
            if (!array_key_exists($key, $result)) {
                $result[$key] = array();
            }
            $result[$key][] = $value;
        }

        return $result;
    }

    /**
     *  Возвращает плоский массив вида ключ => значение
     *  работает с объектами
     *
     * @param $arrayObj
     * @param string $keyField
     * @param string $field
     * @param bool $suppressError
     * @return array
     * @throws \Exception
     */
    static public function flatten($arrayObj, $keyField = "id", $field = "name", $suppressError = true)
    {
        if (!is_array($arrayObj) && !($arrayObj instanceof Traversable)){
            return [];
        }

        $method = Inflector::camelize("get_" . $field);
        $keyMethod = Inflector::camelize("get_" . $keyField);

        $result = [];
        foreach ($arrayObj as $row) {
            if (is_object($row)) {
                if (!method_exists($row, $method) && !$suppressError) {
                    throw new \Exception(sprintf("Method %s::%s not exists", get_class($row), $method));
                }
                if (!method_exists($row, $keyMethod) && !$suppressError) {
                    throw new \Exception(sprintf("Method %s::%s not exists", get_class($row), $keyMethod));
                }
                $result[$row->$keyMethod()] = $row->$method();
            } else {
                $result[$row[$keyField]] = $row[$field];
            }
        }

        return $result;
    }

    /**
     * Собирает поле с подмассивов или с объектов массива
     *
     * @param array | Traversable $arrayObj
     * @param string $field
     * @param bool $suppressError
     * @return array
     * @throws \Exception
     */
    static public function collect($arrayObj, $field = "id", $suppressError = true)
    {
        if (!is_array($arrayObj) && !($arrayObj instanceof Traversable)) {
            return [];
        }

        $method = Inflector::camelize("get_" . $field);
        $result = [];

        foreach ($arrayObj as $row) {
            if (is_object($row)) {
                if (!method_exists($row, $method) && !$suppressError) {
                    throw new \Exception(sprintf("Method %s::%s not exists", get_class($row), $method));
                }

                $result[] = $row->$method();

            } else {
                if (isset($row[$field])) {
                    $result[] = $row[$field];
                } elseif (!$suppressError) {
                    throw new \Exception(sprintf("No key %s in array with keys %s", $field, implode(", ", array_keys($row))));
                }
            }
        }

        return $result;
    }

    /**
     * Пересобирает массив используя в качестве ключа значение элеменита массива
     *
     * @param $arrayObj
     * @param string $field
     * @param bool $multiple
     * @param bool $suppressError
     * @return array
     * @throws \Exception
     */
    static public function index($arrayObj, $field = "id", $multiple = false, $suppressError = true)
    {
        if (!is_array($arrayObj) && !($arrayObj instanceof Traversable)) {
            return [];
        }

        $method = Inflector::camelize("get_" . $field);
        $result = [];

        foreach ($arrayObj as $row) {
            if (is_object($row)) {
                if (!method_exists($row, $method) && !$suppressError) {
                    throw new \Exception(sprintf("Method %s::%s not exists", get_class($row), $method));
                }
                $key = $row->$method();

            } else {
                if (!isset($row[$field])) {
                    if (!$suppressError) {
                        throw new \Exception(sprintf("No key %s in array with keys %s", $field, implode(", ", array_keys($row))));
                    }
                }
                $key = $row[$field];
            }

            if (is_object($key)) {
                if(!method_exists($key , '__toString')){
                    throw  new \Exception(sprintf('Can\'t convert object to string. Class "%s" has no __toString method',get_class($key)));
                }
                $key = (string)$key;
            }

            if ($multiple) {
                $result[$key][] = $row;
            } else {

                $result[$key] = $row;
            }
        }

        return $result;
    }

}

