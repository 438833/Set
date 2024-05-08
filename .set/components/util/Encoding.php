<?php

namespace set\components\util;

class Encoding
{

    static public function strToUtf8($string)
    {
        if(mb_detect_encoding($string, 'UTF-8', true) === false)
        {
            $string = utf8_encode($string);
		}
        return $string;
	}

    static public function strCp1251ToUtf($string)
    {
        return iconv('cp1251', 'utf-8', $string);
    }

    static public function strUtfToCp866($string)
    {
        return iconv('utf-8', 'cp866', $string);
    }

}

?>