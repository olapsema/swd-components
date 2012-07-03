<?php
namespace Swd\Component;

use DOMDocument,DOMXPath;

/**
 * Конвертация HTML в разметку Textile
 *
 * @package default
 * @author skoryukin
**/
class  HTML2Textile
{
    private  $doc_template = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><body>%s</body></html>";
    public function __construct()
    {
    }

    public function transform($html)
    {
        $doc = $this->createDom($html);
        $root = $this->getDomRoot($doc);

        //TODO start from here
    }

    protected function createDom($html)
    {
        $html = sprintf($this->doc_template,$html);
        $doc =  new DOMDocument("1.0","UTF-8");
        $doc->preserveWhiteSpace = false;

        @$doc->loadHTML($html);
        $doc->normalizeDocument();
        return $doc;
    }

    protected function getDomRoot($doc)
    {
        $xpath = new DOMXPath($doc);
        $roots = $xpath->query("/html/body[1]");

        $root = ($roots->item($roots->length -1));

        return $root;
    }
}
