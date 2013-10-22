<?php

namespace Swd\Component\Utils\Html;

/**
 * Зачищает документ от тегов, оставляя только текст
 *
 * @package default
 * @author skoryukin
**/
class Stripper extends Distiller
{
   public function  __construct()
   {
       $options = array(
       );

       parent::__construct($options);

       $this->setAllowedTags(array());
   }
}
