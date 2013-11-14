<?php

namespace Swd\Component\Menu;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use LogicException, ArrayIterator;

class FlatMenuHelper extends ArrayIterator {

    protected $router;

    public function __construct($array,$flags = 0,UrlGeneratorInterface $router = null)
    {
        $this->router  = $router;

        $defaults = array('selected'=>false);

        foreach($array as $k => $item){
            $array[$k] = (object)array_merge($defaults,$item);
        }
        parent::__construct($array,$flags);
    }

    public function selectPath($path)
    {
        $paths = array();
        foreach($this as $k => $item ){
            $paths[$k] =  parse_url($item->url,PHP_URL_PATH);
        }

        arsort($paths,SORT_STRING);
        //var_dump($paths,$path);

        foreach($paths as $k => $item_path){
            if(strpos($path,$item_path) === 0){
                $this[$k]->selected = true;
                break;
            }
        }
    }

    public function addRoute($title,$route_name,$route_params = array(),$abs = false)
    {

        $url = $this->getRouter()->generate($route_name,$route_params,$abs);
        $item = array(
            'name'=>$title,
            'route'=>array('name'=>$route_name,'params'=>$route_params,'absolute'=>$abs),
            'url'=>$url,
            'selected'=>false
        );
        $item = (object)$item;
        parent::append($item);

        return $item;
    }

    public function addUrl($title,$url)
    {
        $item = array('name'=>$title,'url'=>$url,'selected' => false);

        $item = (object)$item;
        parent::append($item);
        return $item;
    }

    public function setRouter($router){
        $this->router  = $router;
    }


    protected function getRouter()
    {
        if(is_null($this->router)){
            throw new LogicException('No UrlGenerator set');
        }
        return $this->router;
    }
}


