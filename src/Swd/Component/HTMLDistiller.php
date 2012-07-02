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
    private $allowed_tags;
    private $special_tags = array(
        "#text",
    );

    private $ignore_tags = array(
        "form","meta",
    );

    public $doc_template = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><body>%s</body></html>";

    public function __construct()
    {
    }

    public function setAllowedTags($tags)
    {
        $this->allowed_tags = array_merge($tags,$this->special_tags);

    }

    public function processHTML($html)
    {
        $html = sprintf($this->doc_template,$html);
        $doc =  new DOMDocument("1.0","UTF-8");
        $doc->preserveWhiteSpace = false;
        $res_doc =  new DOMDocument("1.0","UTF-8");

        @$doc->loadHTML($html);
        $doc->normalizeDocument();
        $xpath = new DOMXPath($doc);
        $roots = $xpath->query("/html/body");

        //var_dump($roots->length);
        $root = ($roots->item($roots->length -1));
        foreach ($root->childNodes as $node){

            var_dump($node->nodeName);
            var_dump($node->nodeValue);
            $dnode = $this->processNode($node,$res_doc);
            //if()
        }
    }

    protected function processNode($node,$dstDoc)
    {
        if($srcNode)

    }

}
