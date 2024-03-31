<?php

declare(strict_types=1);

namespace npds\system\date;

use npds\system\language\language;

class date
{
 
    /**
     * Pour obtenir Nuit ou Jour ... Un grand Merci à P.PECHARD pour cette fonction
     *
     * @return  string
     */
    public static function NightDay(): string 
    {
        global $lever, $coucher;

        $Maintenant = strtotime("now");
        $Jour = strtotime($lever);
        $Nuit = strtotime($coucher);

        if ($Maintenant - $Jour < 0 xor $Maintenant - $Nuit > 0) {
            return "Nuit";
        } else {
            return "Jour";
        }
    }
 
    /**
     * Formate un timestamp en fonction de la valeur de $locale (config.php)
     * si "nogmt" est concaténé devant la valeur de $time, le décalage gmt n'est pas appliqué
     *
     * @param   string     $time  [$time description]
     *
     * @return  string
     */
    public static function formatTimestamp(string $time): string
    {
        $locale = language::getLocale();

        return ucfirst(htmlentities(\PHP81_BC\strftime(translate("datestring"), $time, $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));
    }

    /**
     * [convertdateTOtimestamp description]
     *
     * @param   string  $myrow  [$myrow description]
     *
     * @return  int
     */
    public static function convertdateTOtimestamp(string $myrow): int
    {
        if (substr($myrow, 2, 1) == "-") {
            $day = substr($myrow, 0, 2);
            $month = substr($myrow, 3, 2);
            $year = substr($myrow, 6, 4);
        } else {
            $day = substr($myrow, 8, 2);
            $month = substr($myrow, 5, 2);
            $year = substr($myrow, 0, 4);
        }

        $hour = substr($myrow, 11, 2);
        $mns = substr($myrow, 14, 2);
        $sec = substr($myrow, 17, 2);

        return mktime((int) $hour, (int) $mns, (int) $sec, (int) $month, (int) $day, (int) $year);
    }

    /**
     * [post_convertdate description]
     *
     * @param   int     $tmst  [$tmst description]
     *
     * @return  string
     */
    public static function post_convertdate(int $tmst): string 
    {
        $val = $tmst > 0 ? date(translate("dateinternal"), $tmst) : '';
    
        return $val;
    }
    
    /**
     * [convertdate description]
     *
     * @param   string  $myrow  [$myrow description]
     *
     * @return  string
     */
    public static function convertdate(string $myrow): string
    {
        $tmst = static::convertdateTOtimestamp($myrow);
        $val = static::post_convertdate($tmst);
    
        return $val;
    }

}
