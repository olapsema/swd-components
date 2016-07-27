<?php

namespace Swd\Component\Utils\Html;

use DOMDocument,DOMXPath,DOMNode;

/**
 * Зачистка HTML до состояния теста
 *
 * @packaged default
 * @author skoryukin
 **/
class Html2PlainText
{
    protected $template = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><body>%s</body></html>";

    const BLOCK_BEGIN = 1;
    const BLOCK_END = 2;
    /**
     * Теги которые не обрабатываются
     *
     * @var array
     **/
    protected $ignoreTags = array(
        "form","meta",
    );

    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'merge_whitespace' => false,
            'ignore_newline' => false,

            'block_tags' => ['br','div','p','ol','ul','h1','h2','h3','h4','h5']
        ],$options);
    }

    public function process($html)
    {
        $result = '';
        $html = sprintf($this->template,$html);
        $doc =  new DOMDocument("1.0","UTF-8");
        $doc->preserveWhiteSpace = false;

        @$doc->loadHTML($html);
        $doc->normalizeDocument();
        $xpath = new DOMXPath($doc);
        $roots = $xpath->query("/html/body");
        $root = ($roots->item($roots->length -1));


        $nodes = array();
        $this->extractNodes($root,$nodes);
        $lastText = false;
        while($node = array_shift($nodes)){
            //var_dump('lasttext '.intval($lastText));
            if(!is_object($node)){
                if($node == self::BLOCK_BEGIN){
                    //var_dump('begin');
                    if($lastText){
                        $result .= "\n";
                    }
                }

                if($node == self::BLOCK_END){
                    //var_dump('end');
                    if($lastText){
                        $result .= "\n";
                    }
                }
                $lastText= false;
                continue;
            }

            if($node->nodeType == XML_TEXT_NODE){
                $node->normalize();
                $text = $node->nodeValue;
                $textTest = trim($text);
                if(empty($textTest)){
                    //var_dump('skip');
                    continue;
                }

                if($this->options['merge_whitespace']){
                    $text = $this->mergeWhiteSpace($text);
                }
                if($this->options['ignore_newline']){
                    $text = str_replace("\n",' ',$text);
                }
                if(!$lastText){
                    $text = ltrim($text);
                }
                //var_dump($text);
                $lastText = true;
                $result .=  $text;
                continue;
            }

            if($node->nodeName == 'br'){
                //var_dump('br');
                $result .="\n";
                $lastText = false;
                continue;
            }

            $blockTag = in_array($node->nodeName,$this->options['block_tags']);

            if($blockTag){
                array_unshift($nodes, self::BLOCK_END);//для переноса строки после элемента
            }

            if($node->hasChildNodes() ){
                $this->extractNodes($node,$nodes,true);
            }
            if($blockTag){
                array_unshift($nodes, self::BLOCK_BEGIN);//для переноса строки перед элемента
            }
        }

        return rtrim($result);
    }

    public function extractNodes(DOMNode $root,array &$ar,$inBeginning = false)
    {
        if($root->childNodes->length <= 0 ){
            return;
        }

        if($inBeginning){
            $len = $root->childNodes->length;
            //add in reverse order
            for ($i = ($len-1); $i >= 0;$i--){
                $node = $root->childNodes->item($i);
                array_unshift($ar,$node);
                //var_dump('added2 '.$node->nodeName.'#'.$node->nodeType);
            }
        }else{
            foreach ($root->childNodes as $node){
                //var_dump('added1 '.$node->nodeName.'#'.$node->nodeType);
                array_push($ar,$node);
            }
        }
    }

    protected function mergeWhiteSpace($text)
    {
        return preg_replace("/[[:space:]][[:space:]]{1,}/mu",' ',$text);
    }
}
