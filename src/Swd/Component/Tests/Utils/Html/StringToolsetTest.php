<?php

namespace Swd\Component\Tests\Utils\Html;

use Swd\Component\Utils\Html\StringToolset;

class StringToolsetTest extends \PHPUnit_Framework_TestCase
{

    public function testSubstringBlock()
    {

        $html = "<div><p>First, I've identified two limitations of var_export verus serialize.</p><p class='x'>However, I could deal with both of those so I created a benchmark.  I used a single array containing from 10 to 150 indexes.  I've generate the elements' values randomly using booleans, nulls, integers, floats, and some nested arrays (the nested arrays are smaller averaging 5 elements but created similarly).</p><p>The largest percentage of elements are short strings around 10-15 characters.  While there is a small number of long strings (around 500 characters).</p></div>";

        $tool = new StringToolset();

        $result1 = $tool->substringBlock($html,0,200);

        echo ($result1);
        $pos = $tool->strlen($result1);
        echo "\n\n";
        $result2 = $tool->substringBlock($html,$pos);
        echo ($result2);


        echo "\n\n";
    }
}
