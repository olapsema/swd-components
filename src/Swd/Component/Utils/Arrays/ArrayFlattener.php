<?php
namespace Swd\Component\Utils\Arrays;

use Traversable,ArrayAccess;

class ArrayFlattener {

    /**
     * Раскрытие вложенной структуры в плоский вид
     * (Операция обратная группировке) (использует 1 найденный массив)
     *
     * @param $data - массив входных данных
     * @param $item_level - на какой глубине находятся элемeнты
     *
     * @return array
     **/
    public static function unwrap($data,$item_level = false)
    {
        $result = array();

        //this is level 0
        foreach($data as $item){
            $res = static::unwrapContext(array(),$item,1,$item_level);

            //var_dump($res);
            $result = array_merge($result,$res);
        }

        //var_dump('result',$result);

        return $result;
    }

    protected static function unwrapContext($context,$item,$level,$stop_level = false)
    {
        //var_dump('level: '.$level,$context,$item);
        if($stop_level !== false && $level >= $stop_level ){
            //var_dump('xxx');
            return array(array_merge($item));
        }

        $path = null;

        $result = array();



        foreach($item as $key => $element){
            if(is_null($path) && is_array($element)){
                $path = $key;
                continue;
            }
            $context[$key] = $element;
        }

        if(is_null($path)){
            return array($context);
        }

        //var_dump($path);

        $sub_paths= false;
        foreach($item[$path] as $value => $subitem)
        {
            if(is_array($subitem)){
                $sub_paths = true;
                $item_context = array_merge($context,array($path=>$value));
                $res = static::unwrapContext($item_context,$subitem,$level+1,$stop_level);
                $result = array_merge($result,$res);
            }
            /*
            else{
                $result[] = array(array_merge($context,array($value=>$subitem)));

            }
             */


        }

        if(!$sub_paths){
            $result = array(array_merge($context,$item));
        }


        return $result;
    }


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

    public static function flattenItem($item,$delimeter = false)
    {
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

