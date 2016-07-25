<?php

namespace Swd\Component\Utils\Html;



/**
 * Конвертация UTF-8 в сущности (в основном те, что имеют имена)
 *
 * @return void
 * @author skoryukin
 **/
class EntitiesConverter
{
    static $ent_table;
    static $default_replace = array("common","math");


    /**
     * Превращает UTF-8 символы  в html сущности
     *
     * @param $text
     * @param bool $tables
     * @return mixed
     */
    static public function encode($text,$tables = false)
    {
        self::init();
        if($tables == false){
            $tables = self::$default_replace;
        }

        $from = $to = array();
        foreach($tables as $tab){
            $from = array_merge($from ,self::$ent_table["utf8_codes"][$tab]);
            $to = array_merge($to ,self::$ent_table["ent"][$tab]);
        }

        return str_replace($from,$to,$text);
    }

    static protected function init()
    {
        static $inited;

        if($inited) return;

        $html = array(
            "&quot;",
            "&amp;",
            "&apos;",
            "&lt;",
            "&gt;",
        );
        $common = array(
            "&nbsp;",
            "&iexcl;",
            "&cent;",
            "&pound;",
            "&curren;",
            "&yen;",
            "&brvbar;",
            "&sect;",
            "&uml;",
            "&copy;",
            "&ordf;",
            "&laquo;",
            "&not;",
            "&shy;",
            "&reg;",
            "&macr;",
            "&deg;",
            "&plusmn;",
            "&sup2;",
            "&sup3;",
            "&acute;",
            "&micro;",
            "&para;",
            "&middot;",
            "&cedil;",
            "&sup1;",
            "&ordm;",
            "&raquo;",
            "&frac14;",
            "&frac12;",
            "&frac34;",

            "&times;",
            "&circ;",
            "&tilde;",
            "&trade;",

            "&ndash;",
            "&mdash;",
            "&lsquo;",
            "&rsquo;",
            "&sbquo;",
            "&ldquo;",
            "&rdquo;",
            "&bdquo;",
            "&bull;",
            "&hellip;",
            "&permil;",
            "&prime;",
            "&Prime;",
            "&lsaquo;",
            "&rsaquo;",
            "&oline;",
            "&frasl;",
            "&euro;",
            "&yen;",
        );

        $latin = array(
            "&iquest;",
            "&Agrave;",
            "&Aacute;",
            "&Acirc;",
            "&Atilde;",
            "&Auml;",
            "&Aring;",
            "&AElig;",
            "&Ccedil;",
            "&Egrave;",
            "&Eacute;",
            "&Ecirc;",
            "&Euml;",
            "&Igrave;",
            "&Iacute;",
            "&Icirc;",
            "&Iuml;",
            "&ETH;",
            "&Ntilde;",
            "&Ograve;",
            "&Oacute;",
            "&Ocirc;",
            "&Otilde;",
            "&Ouml;",
            "&Oslash;",
            "&Ugrave;",
            "&Uacute;",
            "&Ucirc;",
            "&Uuml;",
            "&Yacute;",
            "&THORN;",
            "&szlig;",
            "&agrave;",
            "&aacute;",
            "&acirc;",
            "&atilde;",
            "&auml;",
            "&aring;",
            "&aelig;",
            "&ccedil;",
            "&egrave;",
            "&eacute;",
            "&ecirc;",
            "&euml;",
            "&igrave;",
            "&iacute;",
            "&icirc;",
            "&iuml;",
            "&eth;",
            "&ntilde;",
            "&ograve;",
            "&oacute;",
            "&ocirc;",
            "&otilde;",
            "&ouml;",
            "&divide;",
            "&oslash;",
            "&ugrave;",
            "&uacute;",
            "&ucirc;",
            "&uuml;",
            "&yacute;",
            "&thorn;",
            "&yuml;",
            "&OElig;",
            "&oelig;",
            "&Scaron;",
            "&scaron;",
            "&Yuml;",
            "&fnof;",
        );

        $greek = array(
            "&Alpha;",
            "&Beta;",
            "&Gamma;",
            "&Delta;",
            "&Epsilon;",
            "&Zeta;",
            "&Eta;",
            "&Theta;",
            "&Iota;",
            "&Kappa;",
            "&Lambda;",
            "&Mu;",
            "&Nu;",
            "&Xi;",
            "&Omicron;",
            "&Pi;",
            "&Rho;",
            "&Sigma;",
            "&Tau;",
            "&Upsilon;",
            "&Phi;",
            "&Chi;",
            "&Psi;",
            "&Omega;",
            "&alpha;",
            "&beta;",
            "&gamma;",
            "&delta;",
            "&epsilon;",
            "&zeta;",
            "&eta;",
            "&theta;",
            "&iota;",
            "&kappa;",
            "&lambda;",
            "&mu;",
            "&nu;",
            "&xi;",
            "&omicron;",
            "&pi;",
            "&rho;",
            "&sigmaf;",
            "&sigma;",
            "&tau;",
            "&upsilon;",
            "&phi;",
            "&chi;",
            "&psi;",
            "&omega;",
            "&thetasym;",
            "&upsih;",
        );

        $pseudo = array(

            "&larr;",
            "&uarr;",
            "&rarr;",
            "&darr;",
            "&harr;",
            "&crarr;",
            "&lArr;",
            "&uArr;",
            "&rArr;",
            "&dArr;",
            "&hArr;",
            "&spades;",
            "&clubs;",
            "&hearts;",
            "&diams;",
            "&dagger;",
            "&Dagger;",
        );

        $math = array(

            "&forall;",
            "&part;",
            "&exist;",
            "&empty;",
            "&nabla;",
            "&isin;",
            "&notin;",
            "&ni;",
            "&prod;",
            "&sum;",
            "&minus;",
            "&lowast;",
            "&radic;",
            "&prop;",
            "&infin;",
            "&ang;",
            "&and;",
            "&or;",
            "&cap;",
            "&cup;",
            "&int;",
            "&there4;",
            "&sim;",
            "&cong;",
            "&asymp;",
            "&ne;",
            "&equiv;",
            "&le;",
            "&ge;",
            "&sub;",
            "&sup;",
            "&nsub;",
            "&sube;",
            "&supe;",
            "&oplus;",
            "&otimes;",
            "&perp;",
            "&sdot;",
            "&lceil;",
            "&rceil;",
            "&lfloor;",
            "&rfloor;",
            "&lang;",
            "&rang;",
            "&loz;",
        );

        $entities = array(
            "html"  => $html,
            "common"=> $common,
            "greek" => $greek,
            "latin" => $latin,
            "math"  => $math,
        );

        $utf8_codes = array();
        foreach($entities as $ent_tab => $ent_list){
            foreach($ent_list as $symbol){
                $utf8_codes[$ent_tab][] = mb_convert_encoding($symbol,"UTF-8","HTML-ENTITIES");
            }
        }
        self::$ent_table = array(
            "ent"=> $entities,
            "utf8_codes" => $utf8_codes,
        );
        $inited = true;
    }
}
