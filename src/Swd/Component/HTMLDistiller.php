<?php

namespace Swd\Component;

use DOMDocument,DOMXPath;

/**
 * Зачистка HTML от нежедательных тегов и элементов
 *
 * @packaged default
 * @author skoryukin
 **/
class HTMLDistiller
{
    public static $common_tags = array(
        "div","p","span",
        "strong","b",
        "em", "i",
        "sup","u",
        "blockquote","br",
        "a",
        "li","ul","ol",
        "table","tbody","th","tr","td"
    );
    /**
     * теги, которые не нужно заменять тестовым содержимым
     *
     * @var array
     **/
    private $allowed_tags;

    /**
     * Теги которые не обрабатываются
     *
     * @var array
     **/
    private $ignore_tags = array(
        "form","meta",
    );

    public $doc_template = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><body>%s</body></html>";

    /**
     * Опции работы дистилятора
     *
     * @var string
     **/
    protected $options = array(
        // можно ли удалить тег без содержимого или с пробелами и переводами строк (br не обрабатывается)
        "remove_empty_tags" => true,
        //нужно ли удалять пустое пространство между тегами
        "remove_empty_space" => false,
        // нужно ли склеивать соседствующие пустоты
        "merge_empty_space" => true,
        //br обрабатывается как пустота см предыдущий пункт
        "br_is_blank_tag"=>true,
        // производить замену тега (аттрибуты копируются от родителя)
        "replace_tags" =>array(
            "b" => "strong",
            "i" => "em"
        ),
    );

    protected $common_tag_attributes = array(
        "table" => array("cellspacing","cellpadding", "border"),
        "td"=>array("colspan","rowspan"),
        "a" =>array("href","target"),
        "img" => array("src","width","height")
    );



    public function __construct($options = array())
    {
        $this->allowed_tags = self::$common_tags;
        $this->options = array_merge ($this->options,$options);
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
        $res_doc->preserveWhiteSpace = false;
        $res_xpath = new DOMXPath($res_doc);
        $dst_roots = $res_xpath->query("/html/body");
        $dst_root = ($dst_roots->item($dst_roots->length -1));

        //var_dump($roots->length);
        foreach ($root->childNodes as $node){

            if(in_array($node->nodeName,$this->ignore_tags)) continue;

            //if(in_array($))
            //var_dump($node->nodeName);
            //var_dump($node->nodeValue);
            $dnode = $this->processNode($node,$res_doc);
            if($dnode === false) continue;
            if(is_array($dnode)){
                foreach($dnode as $child){
                    $dst_root->appendChild($child);
                }
            }else{
                $dst_root->appendChild($dnode);
            }
        }
        //echo $res_doc->saveHTML();

        //getting html from body container without body tag
        $result = "";
        foreach($dst_root->childNodes as $element){
            $result .=  $res_doc->saveHTML($element);
        }
        return $result;
    }

    protected function processNode($node,$dst_doc)
    {
        //по умолчанию конвертируем ноду в текст путем передачи детей родительской ноде
        $pop_nodes = true;

        $dnode = false;
        $children = array();
        //var_dump(sprintf("inside %s (%d)",$node->nodeName, $node->hasChildNodes()));
        if(in_array($node->nodeName,$this->allowed_tags)){
            //обрабатываем  и для элемента создаем соответствующий тег
            $pop_nodes = false;
        }
        if($node->nodeType == XML_ELEMENT_NODE
            //&& !$pop_nodes
        ){
            $dnode = $dst_doc->createElement($this->getNodeName($node->nodeName));
            $this->copyAttributes($node,$dnode);

        }elseif($node->nodeType == XML_TEXT_NODE){
            $node_text = trim($node->wholeText);
            if(empty($node_text) && $this->options["remove_empty_space"]){
                return false;
            }

            $dnode = $dst_doc->createTextNode($node->wholeText);
            //var_dump(sprintf ("return text node %s", $node->wholeText));
            return $dnode;

        }else{
            //var_dump($node);
            //DOMCdataSection and others
            return false;
        }

        /*
        if($node->nodeName == "a" ){
                var_dump($node->hasChildNodes());
                var_dump($node->nodeValue);
        }
         */

        if($node->hasChildNodes()){

            foreach($node->childNodes as $child){
                //var_dump("process ".$child->getNodePath());
                if(in_array($child->nodeName,$this->ignore_tags)) continue;

                $child_result = $this->processNode($child,$dst_doc);
                if($child_result === false) continue;
                if(is_array($child_result)){
                    $children = array_merge ($children,$child_result);
                }else{
                    $children[] = $child_result;
                }
            }
        }

        // Склеивание пустоты
        if($this->options["merge_empty_space"]){
            $rm_child = array();
            $empty_prev = false;
            foreach($children as $k => $child){
                $empty = false;

                if($child->nodeType == XML_TEXT_NODE){
                    $node_text = trim($child->wholeText);
                    if(empty($node_text))
                        $empty = true;

                }elseif($child->nodeType == XML_ELEMENT_NODE){
                    if($node->nodeName == "br" && $this->options["br_is_blank_tag"]) {
                        $empty = true;
                    }
                }

                if($empty){
                    if($empty_prev)
                        $rm_child[] = $k;
                    else
                        $empty_prev = true;
                }else{
                    $empty_prev = false;
                }
            }
            foreach($rm_child as $k){
                unset($children[$k]);
            }
        }

        //var_dump(count($children)." values for ".$node->getNodePath());
        //var_dump($children);

        if(empty($children)){
            if( !$dnode->hasChildNodes()
                && $this->isEmptyProtected($dnode->nodeName)
                && $this->options["remove_empty_tags"]
              )
                $result = false;
            else
                $result = $dnode;
        }else{
            if($pop_nodes){
                $result = $children;
            }else{
                //
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

    protected function getNodeName($name)
    {
        if(array_key_exists($name,$this->options["replace_tags"])){
            return $this->options["replace_tags"][$name];
        }
        return $name;
    }

    /**
     * Проверяет можно ли удалить ноду если она пуста
     *
     * @return void
     * @author skoryukin
     **/
    protected function isEmptyProtected($name)
    {
        $protected_tags = array("br");
        return !in_array($name,$protected_tags);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author skoryukin
     **/
    protected function copyAttributes($src_node,$dst_node)
    {
        if($src_node->nodeName != $dst_node->nodeName) return false;

        if(array_key_exists($src_node->nodeName,$this->common_tag_attributes)){
            if ($src_node->attributes->length > 0){
                foreach($this->common_tag_attributes[$src_node->nodeName] as $attrib_name){
                    $attrib = $src_node->attributes->getNamedItem($attrib_name);
                    if($attrib){
                        $attr_node = $dst_node->ownerDocument->createAttribute($attrib->nodeName);
                        try{
                            @$attr_node->nodeValue = @$attrib->nodeValue;
                        }catch(\Exception $e){
                            //var_dump($dst_node);
                            //print_r($attrib);
                            throw $e;
                        }

                        $dst_node->setAttributeNode($attr_node);
                    }
                }
            }
        }

        return true;
    }
}
