<?php

namespace Swd\Component;

use DOMDocument,DOMXPath;

/**
 * Зачистка HTML от
 *
 * @packaged default
 * @author skoryukin
 **/
class HTMLDistiller
{
    /**
     * теги, которые не нужно заменять тестовым содержимым
     *
     * @var array
     **/
    private $allowed_tags;
    private $special_tags = array(
        "#text",
    );

    /**
     * Теги которые не обрабатываются
     *
     * @var array
     **/
    private $ignore_tags = array(
        "form","meta",
    );

    public $doc_template = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><body>%s</body></html>";

    public function __construct()
    {
    }

    public function setAllowedTags($tags)
    {
        $this->allowed_tags = $tags;

    }

    public function processHTML($html)
    {
        //echo "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><body>";
        $html = sprintf($this->doc_template,$html);
        $doc =  new DOMDocument("1.0","UTF-8");
        $doc->preserveWhiteSpace = false;

        @$doc->loadHTML($html);
        $doc->normalizeDocument();
        $xpath = new DOMXPath($doc);
        $roots = $xpath->query("/html/body");
        $root = ($roots->item($roots->length -1));

        $res_doc =  new DOMDocument("1.0","UTF-8");
        $res_doc->loadHTML( sprintf($this->doc_template,""));
        $res_xpath = new DOMXPath($res_doc);
        $dst_roots = $res_xpath->query("/html/body");
        $dst_root = ($dst_roots->item($dst_roots->length -1));

        //var_dump($roots->length);
        foreach ($root->childNodes as $node){

            if(in_array($node->nodeName,$this->ignore_tags)) continue;

            //if(in_array($))
            //var_dump($node->nodeName);
            //var_dump($node->nodeValue);
            $dnode = $this->processNode($node,$xpath,$res_doc);
            if(is_array($dnode)){
                foreach($dnode as $child){
                    $dst_root->appendChild($child);
                }
            }else{
                $dst_root->appendChild($dnode);
            }
        }
        echo $res_doc->saveHTML();
//        echo $res_doc->saveHTML($dst_root);
    }

    protected function processNode($node,$src_xpath,$dst_doc)
    {
        //по умолчанию конвертируем ноду в текст путем передачи детей родительской ноде
        $pop_nodes = true;

        $children = array();
        //var_dump(sprintf("inside %s (%d)",$node->nodeName, $node->hasChildNodes()));
        if(in_array($node->nodeName,$this->allowed_tags)){
            //обрабатываем  и для элемента создаем соответствующий тег
            $pop_nodes = false;
        }
        if($node->nodeType == XML_ELEMENT_NODE){
                $dnode = $dst_doc->createElement($node->nodeName);

        }elseif($node->nodeType == XML_TEXT_NODE){
            $dnode = $dst_doc->createTextNode($node->wholeText );
            //var_dump(sprintf ("return text node %s", $node->wholeText));
            return $dnode;
        }else{
            $dnode = false;
        }


        if(false && $node->nodeName == "br" ){
                var_dump($node->hasChildNodes());
                var_dump($node->nodeValue);
        }

        if($node->hasChildNodes()){

            foreach($node->childNodes as $child){
                //var_dump("process ".$child->getNodePath());
                if(in_array($child->nodeName,$this->ignore_tags)) continue;

                $child_result = $this->processNode($child,$src_xpath,$dst_doc);
                if(is_array($child_result)){
                    $children = array_merge ($children,$child_result);
                }else{
                    $children[] = $child_result;
                }
            }
        }

        //var_dump(count($children)." values for ".$node->getNodePath());
        //var_dump($children);

        if(empty($children)){
            $result = $dnode;
        }else{
            if($pop_nodes){
                $result = $children;
            }else{
                //var_dump($dnode->childNodes->item(0)->wholeText);
                foreach($children as $child) {
                    $dnode->appendChild($child);
                }
                //var_dump($dnode->childNodes->item(1));
                $result = $dnode;
            }
        }

        /*
        var_dump(sprintf( "inside %s return %s with (%d)",$node->nodeName,
            (is_array($result)?"array":"node" ),
            (is_array($result)?count($result):($dnode->hasChildNodes()?$dnode->childNodes->length:0)))
        );
         */
        return $result;
    }

}
