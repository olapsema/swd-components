<?php
namespace Swd\Component\Utils\Arrays;

use Traversable,ArrayAccess;

class ArrayFlattener {


    /**
     * Разворачивает все подмассивы в основной для каждого элемента
     *
     * @return array
     **/
    public static function flatten($data,$delimeter = false)
    {
        foreach($data as $k => $item){
            $data[$k] =  static::flattenItem($item,$delimeter);
        }
        return $data;
    }

    public static function flattenItem($item,$delimeter = false){
        $result = array();
        $arrays = array();
        $use_prefix = true;

        if(is_null($delimeter) || $delimeter === false) {
            $delimeter = '_';
            $use_prefix = false;
        }

        //cleanup item
        foreach($item as $key => $element){

            if(is_array($element)){
                $arrays[] = array('item'=>$element,'prefix'=>$key);
                unset($item[$key]);
            }
        }
        $result = $item;

        if(empty($arrays))
            return $result;



        while ($element = array_pop($arrays)){
            foreach($element['item'] as $key =>$item){
                $item_pref = $element['prefix'].$delimeter.$key;

                if(is_array($item)){
                    array_push($arrays,
                        array(
                        'item'  => $item,
                        'prefix'=> $item_pref,
                        )
                    );
                    continue;
                }

                if($use_prefix){
                    $result[$item_pref] = $item;
                }else{
                    $result[$key] = $item;
                }
            }
        }

        return $result;
    }

}

