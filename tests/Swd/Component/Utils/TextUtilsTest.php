<?php

namespace Swd\Component\Utils;

class TextUtilsTest extends \PHPUnit_Framework_TestCase
{

    public function test_ww2()
    {
        $text=<<<EOT
123 123 123456789 789xxxxxxxxxxxxx
123
123 123
EOT;
        $check=<<<EOT
123 123
1234567
89
789xxxx
xxxxxxx
xx
123
123 123
EOT;
        $res = TextUtils::wordwrap($text,7,"\n",true);

        $this->assertEquals($check,$res);
        //$res = wordwrap($text,10,"\n",true);
        //var_dump($res);
    }

    public function test_ww3()
    {
        $text=<<<EOT
Launching this week, we are excited to announce a new feature designed to help you earn even more revenue from your site. Simply called “Promoted Content Display Ads”, we designed this app to dynamically source and deliver the right sized ad for your site's layout, served up just beneath the pre-existing Promoted Content module that you are already running.

If you are already running the Promoted Content app, then the Promoted Content Display Ads app will be automatically rolled out to you.

EOT;
        $check=<<<EOT
Launching this week, we are excited to announce a new feature designed to help you earn even more revenue from your site. Simply called
“Promoted Content Display Ads”, we designed this app to dynamically source and deliver the right sized ad for your site's layout, served up
just beneath the pre-existing Promoted Content module that you are already running.

If you are already running the Promoted Content app, then the Promoted Content Display Ads app will be automatically rolled out to you.
EOT;
        $res = TextUtils::wordwrap(trim($text),140,"\n",true);

        //var_dump($res);
        $this->assertEquals(trim($check),$res);
        //$res = wordwrap($text,10,"\n",true);
    }
}


