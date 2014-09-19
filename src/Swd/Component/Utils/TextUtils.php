<?php
namespace Swd\Component\Utils;

class TextUtils {


    /**
     * Транслитерация русского текста
     *
     * @return string
     **/
    static public  function transliterate($string)
    {

        $replace = array ("," => "", "." => "",
                "а" => "a",
                "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh",
                "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m",
                "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t",
                "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh",
                "щ" => "sch", "ъ" => "'", "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",

                "А" => "A",
                "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E", "Ж" => "ZH",
                "З" => "Z", "И" => "I", "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M",
                "Н" => "N", "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
                "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH", "Ш" => "SH",
                "Щ" => "SCH", "Ъ" => "'", "Ы" => "YI", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA"
        );
        return iconv ( "UTF-8", "UTF-8//TRANSLIT//IGNORE", strtr($string,$replace));

    }

    /**
     * Замена всех небуквенных символов на -
     *
     * @return string
     **/
    static public function cleanup($string)
    {
        $string = trim($string, ' -_');
        // replace non letter or digits by -
        $string = preg_replace('#[^\\pL\d\s]+#u', '-', $string);

        // trim
        $string = trim($string, '-');

        return $string;
    }

    /**
     * Получение slug
     *
     * @return string
     **/
    static public function slugify($string)
    {
        $string = static::cleanup($string);
        $string = static::transliterate($string);
        $string = str_replace(array(' ','—'),'-',$string);
        $string = preg_replace('/-{2,}/u','-',$string);

        // remove unwanted characters
        $string = preg_replace('#[^-\w]+#', '', $string);

        $string = mb_strtolower($string);

        return $string;
    }
}
