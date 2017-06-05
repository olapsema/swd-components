<?php

namespace Swd\Component\Utils;
use Swd\Component\Utils\SplObjectStorage\Iterator;

class SplObjectStorage extends  \SplObjectStorage
{
    /**
     * Forms new SplObjectStorage from object items in array, keys ignored
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data){
        $spl = new static();
        foreach ($data as $item) {
            if(!is_object($item)){
                throw new \InvalidArgumentException(sprintf("Wrong array item at index '%s', object expected, '%s' given ",key($data), gettype($item)));
            }
            $spl->attach($item);
        }
        return $spl;

    }

    /**
     * @return \Iterator
     */
    public function getObjectsIterator()
    {
        return new Iterator($this,Iterator::ITERATE_OBJECTS);
    }

    /**
     * @return  \Iterator
     */
    public function getDataIterator()
    {
        return new Iterator($this,Iterator::ITERATE_DATA);
    }
}
