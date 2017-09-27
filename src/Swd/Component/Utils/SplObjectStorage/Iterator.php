<?php

namespace Swd\Component\Utils\SplObjectStorage;

/**
 * Class Iterator
 * @package Swd\Component\Utils\SplObjectStorage
 */
class Iterator implements  \Iterator
{
    const ITERATE_OBJECTS = 1;
    const ITERATE_DATA = 2;

    protected $storage;
    protected $pos;
    protected $type;

    public function __construct(\SplObjectStorage $storage,$type = self::ITERATE_OBJECTS )
    {
       $this->storage = $storage;
       $this->pos = 0;
       $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return  ($this->type === self::ITERATE_OBJECTS)
            ?$this->storage->current()
            :$this->storage->getInfo()
            ;
        
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->storage->next();
        $this->pos++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->pos;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->storage->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->pos = 0;
        $this->storage->rewind();
    }

}
