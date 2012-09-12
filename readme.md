# Using HTMLDistiller


        use Swd\Component\Utils\Html\Distiller;
        $distiller =  new Distiller();
        // you can remove tags that you not need or add yours
        $distiller->setAllowedTags(Distiller::$common_tags);
        $clean_html =  $distiller->process($html);


