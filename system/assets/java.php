<?php

declare(strict_types=1);

namespace npds\system\assets;

class java
{
 
    /**
     * 
     *
     * @param   string  $F  [$F description]
     * @param   string  $T  [$T description]
     * @param   int     $W  [$W description]
     * @param   int     $H  [$H description]
     *
     * @return  string      [return description]
     */
    public static function JavaPopUp(string $F, string $T, int $W, int $H): string 
    {
        // 01.feb.2002 by GaWax
        if ($T == "") {
            $T = "@ ".time()." ";
        }

        $PopUp = "'$F', '$T', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,height=$H,width=$W,toolbar=no,scrollbars=yes,resizable=yes'";

        return $PopUp;
    }

}