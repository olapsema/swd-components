<?php

namespace Swd\Component\Utils;
use DOMDocument;
use DOMXPath;

class DomTool
{


    public static function removeNodesBySelector(DOMXPath $xpath, $selector)
    {
        $items = $xpath->query($selector);
        if($items->length <= 0){
            return;
        }

        for($i = 0;$i<$items->length;$i++){
            $node = $items->item($i);
            $parent = $node->parentNode;
            if($node->nodeType == XML_ATTRIBUTE_NODE){
                $parent->removeAttributeNode($node);
            }else{
                $parent->removeChild($node);
            }
        }
    }

    public static function expandLinksWithBaseTag(DOMDocument $dom)
    {

        $xpath = new DOMXPath($dom);
        $items = $xpath->query('//base');
        if($items->length <= 0){
            return ;
        }

        $baseUrl = null;
        $nodeBase = $items->item(0);
        $hrefAttr = $nodeBase->attributes->getNamedItem('href');
        if($hrefAttr){
            $baseUrl = $hrefAttr->nodeValue;
        }

        for($i = 0;$i<$items->length;$i++){
            $node = $items->item($i);
            $parent = $node->parentNode;
            $parent->removeChild($node);

        }

        $items = $xpath->query('//a/@href');
        if($items->length <= 0){
            return ;
        }
        for($i = 0;$i<$items->length;$i++){
            $node = $items->item($i);
            $value = self::expandUrl($node->nodeValue,$baseUrl);
            $node->nodeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        }

        $items = $xpath->query('//*/@src');
        if($items->length <= 0){
            return ;
        }
        for($i = 0;$i<$items->length;$i++){
            $node = $items->item($i);
            $value = self::expandUrl($node->nodeValue,$baseUrl);
            $node->nodeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * Добавлет базовый урл к относительным  ссылкам
     *
     * @param $url
     * @param $base
     * @return string
     */
    public static function expandUrl($url,$base)
    {
        $url = trim($url);

        //Если урл с протоколом или хостом, то игнорим
        if(mb_strpos($url,'http') ===0 || mb_strpos($url,'//')!== false){
            return $url;
        }

        if(mb_strpos($url,'/')===0){
            return  $base.mb_substr($url,1);
        }

        return $base.$url;
    }
}

