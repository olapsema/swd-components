<?php
namespace Swd\Component\Utils\Arrays;

use  Swd\Component\Utils\Inflector;
use Traversable,ArrayAccess;

class  ArrayMolder {

    /**
     * Переименовывает ключи в массиве
     * 
     * @param array $arr
     * @param array $replaceWith
     * @return array
     */
    public static function replaceKeys(array $arr, array $replaceWith)
    {
        foreach ($replaceWith as $key => $sub){
            if(isset($arr[$key])){
                $arr[$sub] = $arr[$key];
                unset($arr[$key]);
            }
        }
        
        return $arr;
    }
    
    /**
     *  Возвращает ассоциативный массив вида ключ =>  ряд
     * @param $array_val
     * @param string $field
     * @param bool $multiple
     * @return array
     */
    static public function indexArray($array_val,$field = "id",$multiple = false)
    {
        if(!is_array($array_val) && !($array_val instanceof ArrayAccess))
            return array();

        $result = array();
        if(strlen($field)>0 && is_array($array_val)){
            foreach($array_val as $row){
                if($multiple){
                    $result[$row[$field]][] = $row;
                }else{
                    $result[$row[$field]] = $row;
                }
            }
        }
        return $result;
    }

    /**
     *  Возвращает плоский массив вида ключ => значение
     *
     * @param $array_val
     * @param string $key_field
     * @param string $field
     * @return array
     */
    static public function flattenArray(&$array_val,$key_field = "id",$field = "name" )
    {
        if(!is_array($array_val) && !($array_val instanceof ArrayAccess))
            return array();

        $result = array();
        if(strlen($field)>0 && strlen($key_field)>0){
            foreach($array_val as $row){
                $result[$row[$key_field]] = $row[$field];
            }
        }
        return $result;
    }

    /**
     * Возвращает массив значений $key
     *
     * @param $array_val
     * @param string $key
     * @return array
     */
    static public function collectArrayValue($array_val,$key='id')
    {
        if(!is_array($array_val) && !($array_val instanceof ArrayAccess))
            return array();

        $result = array();

        foreach($array_val as $val){
            if(isset($val[$key])){
                array_push($result,$val[$key]);
            }
        }

        return $result;
    }

    /**
     *  Возвращает плоский массив вида ключ => array(значение1,...)
     *  работает с объектами
     *
     * @param $array_obj
     * @param string $key_field
     * @param string $field
     * @param bool $suppress_error
     * @return array
     * @throws \Exception
     */
    static public function flattenMultiple($array_obj,$key_field = "id",$field = "name" , $suppress_error = true)
    {
        if(!is_array($array_obj) && !($array_obj instanceof Traversable))
            return array();

        $method = Inflector::camelize("get_".$field);
        $key_method = Inflector::camelize("get_".$key_field);

        $result = array();
        foreach($array_obj as $row){

            if(is_object($row)){
                if(!method_exists($row,$method) && !$suppress_error){
                    throw new \Exception(sprintf("Method %s::%s not exists",get_class($row),$method));
                }
                if(!method_exists($row,$key_method) && !$suppress_error){
                    throw new \Exception(sprintf("Method %s::%s not exists",get_class($row),$key_method));
                }
                $key = $row->$key_method();
                $value =  $row->$method();

            }else{
                $key = $row[$key_field];
                $value =  $row[$field];
            }
            if(!array_key_exists($key,$result)){
                $result[$key] = array();
            }
            $result[$key][]= $value;
        }
        return $result;
    }

    /**
     *  Возвращает плоский массив вида ключ => значение
     *  работает с объектами
     *
     * @param $array_obj
     * @param string $key_field
     * @param string $field
     * @param bool $suppress_error
     * @return array
     * @throws \Exception
     */
    static public function flatten($array_obj,$key_field = "id",$field = "name" , $suppress_error = true)
    {
        if(!is_array($array_obj) && !($array_obj instanceof Traversable))
            return array();

        $method = Inflector::camelize("get_".$field);
        $key_method = Inflector::camelize("get_".$key_field);

        $result = array();
        foreach($array_obj as $row){
            if(is_object($row)){
                if(!method_exists($row,$method) && !$suppress_error){
                    throw new \Exception(sprintf("Method %s::%s not exists",get_class($row),$method));
                }
                if(!method_exists($row,$key_method) && !$suppress_error){
                    throw new \Exception(sprintf("Method %s::%s not exists",get_class($row),$key_method));
                }
                $result[$row->$key_method()] = $row->$method();
            }else{
                $result[$row[$key_field]] = $row[$field];
            }
        }
        return $result;
    }

    /**
     * Собирает поле с подмассивов или с объектов массива
     *
     * @param array | Traversable $array_obj
     * @param string $field
     * @param bool $suppress_error
     * @return array
     * @throws \Exception
     */
    static public function collect($array_obj,$field = "id", $suppress_error = true)
    {
        if(!is_array($array_obj) && !($array_obj instanceof Traversable))
            return array();

        $method = Inflector::camelize("get_".$field);
        $result = array();
        foreach($array_obj as $row){
            if(is_object($row)){
                if(!method_exists($row,$method) && !$suppress_error){
                    throw new \Exception(sprintf("Method %s::%s not exists",get_class($row),$method));
                }

                $result[] = $row->$method();

            }else{
                if(isset($row[$field])){
                    $result[] = $row[$field];
                }elseif(!$suppress_error){
                    throw new \Exception(sprintf("No key %s in array with keys %s",$field,implode(", ",array_keys($row))));
                }
            }
        }
        return $result;
    }

    /**
     * Пересобирает массив используя в качестве ключа значение элеменита массива
     *
     * @param $array_obj
     * @param string $field
     * @param bool $multiple
     * @param bool $suppress_error
     * @return array
     * @throws \Exception
     */
    static public function index($array_obj , $field = "id",$multiple = false ,$suppress_error = true)
    {
        if(!is_array($array_obj) && !($array_obj instanceof Traversable))
            return array();

        $method = Inflector::camelize("get_".$field);
        $result = array();
        foreach($array_obj as $row){
            if(is_object($row)){
                if(!method_exists($row,$method) && !$suppress_error){
                    throw new \Exception(sprintf("Method %s::%s not exists",get_class($row),$method));
                }
                $key = $row->$method();

            }else{
                if(!isset($row[$field])){
                    if(!$suppress_error)
                        throw new \Exception(sprintf("No key %s in array with keys %s",$field,implode(", ",array_keys($row))));
                }
                $key = $row[$field];
            }

            if($multiple){
                $result[$key][] = $row;
            }else{

                $result[$key] = $row;
            }
        }
        return $result;
    }

}

