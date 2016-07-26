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

    protected $newLineTags = array(
    );

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
        $lastBlock = true;
        //var_dump('initial '.count($nodes));
        while($node = array_shift($nodes)){
            if(!is_object($node)){
                $lastBlock = true;
                $result .= $node;
                continue;
            }
            //var_dump($node->nodeName.'#'.$node->nodeType);

            if($node->nodeType == XML_TEXT_NODE){
                //var_dump(get_class($node),$node);
                //var_dump($node->wholeText);
                $node->normalize();
                $lastText = true;
                $text = $node->nodeValue;

                if($this->options['merge_whitespace']){
                    $text = $this->mergeWhiteSpace($text);
                }
                if($this->options['ignore_newline']){
                    $text = str_replace("\n",' ',$text);
                }
                if($lastBlock){
                    $text = ltrim($text);
                }
                $lastBlock = false;

                $result .=  $text;
                continue;
            }
            if($node->nodeName == 'br'){
                $result .="\n";
                $lastBlock = true;
                continue;
            }

            //$result .= trim($node->nodeValue);
            $blockTag = in_array($node->nodeName,$this->options['block_tags']);

            $lastBlock = $blockTag;
            //var_dump($node->nodeName.'adds '.$node->childNodes->length);
            if($blockTag){
                array_unshift($nodes, "\n");//перенос строки после элемента
            }

            if($node->hasChildNodes() ){
                $this->extractNodes($node,$nodes,true);
                //var_dump($nodes);
            }
            if($lastText && $blockTag){
                array_unshift($nodes, "\n");//перенос строки перед элемента
            }
            $lastText = false;
            //var_dump('count: '.count($nodes));
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
