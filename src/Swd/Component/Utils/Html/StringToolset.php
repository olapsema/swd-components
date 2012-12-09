<?php

namespace Swd\Component\Utils\Html;

use DOMDocument,DOMXPath;

class StringToolset
{

    const SUBST_NOMOD = 0;
    const SUBST_START = 1;
    const SUBST_END   = 2;
    const SUBST_PRESERVE = 4;
    const SUBST_REMOVE = 8;

    protected $substr_ignore_tags = array("img");

    public function __construct()
    {
        $this->substring_pos = 0;
        $this->callbacks = array_combine(array("substring"),array(false));
    }

    public function setCallback($cat,$callbacks)
    {
        $this->callbacks[$cat] = $callbacks;
    }

    public function shrink($html,$length)
    {
        return $this->substring($html,0,$length);
    }

    public function substring($html,$from,$length=false)
    {
        $doc = $this->getDocument($html);
        $root = $this->getRootNode($doc);

        $this->substNode($root,$from,$length,$this->callbacks["substring"]);
        return $this->getDocumentHTML($doc);
    }

    /**
     *  Calculate text length in html document
    **/
    public function strlen($html)
    {
        $doc = $this->getDocument($html);
        $root = $this->getRootNode($doc);

        $length = $this->strlenNodeTree($root);

        return $length;
    }

    protected function strlenNodeTree($node)
    {
        $length = $this->strlenNode($node);
        if($node->hasChildNodes()){
            foreach($node->childNodes as $child){
                $length+= $this->strlenNodeTree($child);
            }
        }

        return $length;
    }

    /**
     * substring without block cutting at the end
     *
    **/
    public function substringBlock($html,$from,$length)
    {
        $doc = $this->getDocument($html);
        $root = $this->getRootNode($doc);

        $callbacks = array("end"=>function(&$node_text,$whole_text,$cur_pos,$end){
            $node_text = $whole_text;
        });
        $this->substNode($root,$from,$length,$callbacks);
        return $this->getDocumentHTML($doc);
    }

    /*
    public function strlength($html)
    {

    }
    */

    private $substring_pos;

    protected function substNode($node,$start,$end,$callbacks = false)
    {
        $this->substring_pos = 0;
        return $this->substringNode($node,$start,$end,$callbacks);
    }

    /**
     * Не может использоваться самостоятельно только в substNode
     *
     * @return void
     * @author skoryukin
     **/
    private function substringNode($node,$start,$end,$callbacks = false)
    {
        if(in_array($node->nodeName,$this->substr_ignore_tags)){
            //обрабатываем  и для элемента создаем соответствующий тег
            return self::SUBST_PRESERVE;
        }


        $result = self::SUBST_NOMOD;

        $length = $this->strlenNode($node);

        //$cur_pos = $pos;
        $cur_pos = $this->substring_pos;
        if( $start >= $cur_pos  //not started
            && $start <= ($cur_pos + $length ) //have start point
        ){
            $result = $result |  self::SUBST_START;
            if($start > $cur_pos){
                //cut top of text
                if($node->nodeType == XML_TEXT_NODE){
                    $whole_text = $this->trimHTML($node->wholeText);
                    if(!empty($whole_text)){
                        $node_text = mb_substr($whole_text,($start-$cur_pos));
                        if(!empty($callbacks) && isset($callbacks["start"]) && is_callable($callbacks["start"])){
                            call_user_func_array($callbacks["start"],array(&$node_text,$whole_text,$cur_pos,$start));
                        }
                        $node->replaceData(0,$node->length,$node_text);
                        if(empty($node_text) && !$node->hasChildNodes())
                        {
                            $result = $result | self::SUBST_REMOVE;
                        }
                    }
                }
            }
        }

        if($end !== false){
            if($end >= $cur_pos //not ended
                && $end <= $cur_pos+$length
            ){
                //cut end
                $result = $result |  self::SUBST_END;

                if($end < $cur_pos+$length){

                    if($node->nodeType == XML_TEXT_NODE){
                        $whole_text = $this->trimHTML($node->wholeText);
                        if(!empty($whole_text)){
                            $node_text = mb_substr($whole_text,0,($end - $cur_pos));
                            //var_dump($node_text);
                            if(!empty($callbacks) && isset($callbacks["end"]) && is_callable($callbacks["end"])){
                                $cb_result = call_user_func_array($callbacks["end"],array(&$node_text,$whole_text,$cur_pos,$end));
                                if(!is_null($cb_result)){
                                    $result = $result | $cb_result;
                                }
                            }
                            $node->replaceData(0,$node->length,$node_text);
                            //if just in the start of the node && all text was captured in previos node this node will preserved,
                            //so fix this situation
                            if(empty($node_text) && !$node->hasChildNodes())
                            {
                                $result = $result | self::SUBST_REMOVE;
                            }
                        }
                    }
                }
                //stop traversing
                return $result;
            }
        }

        $cur_pos += $length;
        $this->substring_pos = $cur_pos;

        $remove = array();
        if($node->hasChildNodes()){
            $delete_all = false;
            foreach($node->childNodes as $child){
                //var_dump("process ".$child->getNodePath());
                if($delete_all){
                    $remove[] = $child;
                    continue;
                }

                $child_result = $this->substringNode($child,$start,$end,$callbacks);
                $outside_zone  = !($this->substring_pos > $start && $this->substring_pos < $end );

                if(($child_result & self::SUBST_REMOVE)){
                    // удаляем дочернюю ноду, как ненужную
                    $remove[] = $child;
                    continue;
                }
                if($child_result & self::SUBST_PRESERVE){
                    //ноду придется оставить и себя, как родителя
                    $result = $result | self::SUBST_PRESERVE;
                    continue;
                }

                if(($child_result == self::SUBST_NOMOD) && $outside_zone){
                    // удаляем дочернюю ноду, как ненужную
                    $remove[] = $child;
                    continue;
                }
                if($child_result & self::SUBST_START){
                    $result = $result |  self::SUBST_START;
                }

                if($child_result & self::SUBST_END){
                    $result = $result |  self::SUBST_END;
                    $delete_all = true;
                }
            }
        }

        foreach($remove as $child){
            $node->removeChild($child);
        }

        return $result;
    }

    /**
     * Просчитывает длину текста ноды (не рекурсивно)
     *
     * @return void
     * @author skoryukin
     **/
    protected function strlenNode($node)
    {
        $result = 0;

        if($node->nodeType == XML_ELEMENT_NODE ){
            //$result = mb_strlen($node->nodeValue);
            // count child nodes
        }elseif($node->nodeType == XML_TEXT_NODE){
            //may be use isWhitespaceInElementContent
            //var_dump($node->wholeText);
            //$node_text = trim($node->wholeText);
            $node_text = $this->trimHTML($node->wholeText);
            if(!empty($node_text)){
                $result = mb_strlen($node_text);
            }
            //var_dump($node_text,$result);
        }else{
            //var_dump(get_class($node));
            //var_dump($node->nodeType);
        }


        //var_dump("len",$result);
        //DOMCdataSection and others are not counted
        return $result;


    }

    public function trimHtml($text)
    {
        //blank near brackets
        //$result = preg_replace("@(?:(?=\>)*[\s]{2,})|(?:[\s]{2,}(?=\<)*)@im"," ",$text);
        $result = preg_replace("@(?<=\>)*[\s]{2,}(?=\<)*@im"," ",$text);
        $result = preg_replace("@(?<=\>)*[\n]*(?=\<)*@im","",$result);//newline
        //between to symbols
        //$result = preg_replace("@(?<=[\w])(?:[\s]|[^\>\<\"]){2,}(?=[\w]+)@im"," ",$result);
        //var_dump($result);
        return $result;

    }

    public $doc_template = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><body>%s</body></html>";

    protected function getDocument($html)
    {
        $html = sprintf($this->doc_template,$html);

        $doc =  new DOMDocument("1.0","UTF-8");
        $doc->preserveWhiteSpace = false;
        $doc->substituteEntities = true;
        $doc->resolveExternals = false;

        @$doc->loadHTML($html);
        $doc->normalizeDocument();

        return $doc;
    }

    protected function getRootNode($doc)
    {
        $xpath = new DOMXPath($doc);
        $roots = $xpath->query("/html/body");
        $root = ($roots->item($roots->length -1));
        return $root;
    }

    protected function getDocumentHTML($doc)
    {
        $result = "";
        if(version_compare(PHP_VERSION, '5.3.6', '>=')){
            $root =  $this->getRootNode($doc);
            foreach($root->childNodes as $element){
                $result .=  $doc->saveHTML($element);
            }
        }else{
            $result = $doc->saveHTML();
            if(preg_match("@(?:.*)<body>(.*)</body>(?:.*)@ims",$result,$match))
            {
                $result = $match[1];
            }

        }
        return $result;
    }
}
