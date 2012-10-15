<?php

namespace Swd\Component\Utils\Arrays;

/**
 * Простой адаптер для получения данных из массива
 * с проверками на найденные значения, подразумевается что
 * источник содержит все необходимые данные
 *
 * @package default
 * @author skoryukin
**/
class ArraySource  implements \IteratorAggregate
{

    protected $source;
    public function __construct($source = array())
    {
        $this->load($source);
    }

    public function getItems($keys)
    {
        if(!is_array($keys))
            throw new \InvalidArgumentException(sprintf("Array expected '%s' given",gettype($items)));

        if(empty($keys))
                throw new \LogicException("Method argument must not be empty");

        $result = array_intersect_key($this->source,array_flip($keys));

        if(count($result) != count($keys)){
            $ntfk = array_diff($keys,array_keys($result));
            throw new \Exception( sprintf("Some keys (%s) not found, may be wrong load ",implode(',',$ntfk)));
        }

        return $result;
    }


    public function getItem($key)
    {
        if(!isset($this->source[$key]))
            throw new \Exception("Key '$key' not found, may be wrong load");

        return    $this->source[$key];
    }

    public function load($items)
    {
        if(!is_array($items))
            throw new \InvalidArgumentException(sprintf("Array expected '%s' given",gettype($items)));

        $this->source = $items;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->source);
    }
}
