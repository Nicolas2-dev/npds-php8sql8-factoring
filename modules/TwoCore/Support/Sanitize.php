<?php

declare(strict_types=1);

namespace Modules\TwoCore\Support;


class Sanitize
{

    /**
     * convertie \r \n  BR ... en br XHTML
     *
     * @param   string  $txt  [$txt description]
     *
     * @return  string
     */
    public static function conv2br(string $txt): string
    {
        return str_replace(
            ['\r\n', '\r', '\n', '<BR />', '<BR>'],
            ['<br />', '<br />', '<br />', '<br />', '<br />'],
            $txt
        );
    }

    /**
     * Les 8 premiers caractères sont convertis en UNE valeur Hexa unique 
     *
     * @param   string  $txt  [$txt description]
     *
     * @return  int
     */
    public static function hexfromchr(string $txt): int
    {
        $surlignage = substr(md5($txt), 0, 8);
        $tmp = 0;

        for ($ix = 0; $ix <= 5; $ix++) {
            $tmp += hexdec($surlignage[$ix]) + 1;
        }

        return ($tmp %= 16);
    }

    /**
     * Formate une chaine numérique avec un espace tous les 3 chiffres / cheekybilly 2005
     *
     * @param   string|int     $ibid  [$ibid description]
     *
     * @return  string
     */
    public static function wrh(string|int $ibid): string
    {
        $tmp = number_format( (float) $ibid, 0, ',', ' ');
        $tmp = str_replace(' ', '&nbsp;', $tmp);

        return $tmp;
    }

    /**
     * Découpe la chaine en morceau de $slpit longueur si celle-ci ne contient pas d'espace / Snipe 2004
     *
     * @param   string  $msg    [$msg description]
     * @param   int     $split  [$split description]
     *
     * @return  string
     */
    public static function split_string_without_space(string $msg, int $split): string
    {
        $Xmsg = explode(' ', $msg);
        array_walk($Xmsg, [Sanitize::class, 'wrapper_f'], $split);
        $Xmsg = implode(' ', $Xmsg);

        return $Xmsg;
    }

    /**
     * Fonction Wrapper pour split_string_without_space / Snipe 2004
     *
     * @param   string  $string  [$string description]
     * @param   string  $key     [$key description]
     * @param   int     $cols    [$cols description]
     *
     * @return  void
     */
    public static function wrapper_f(string &$string, string $key, int $cols): void
    {
        // if (!(stristr($string,'IMG src=') 
        // or stristr($string,'A href=') 
        // or stristr($string,'HTTP:') 
        // or stristr($string,'HTTPS:') 
        // or stristr($string,'MAILTO:') 
        // or stristr($string,'[CODE]'))) {
        $outlines = '';

        if (strlen($string) > $cols) {
            while (strlen($string) > $cols) {

                $cur_pos = 0;
                for ($num = 0; $num < $cols - 1; $num++) {
                    $outlines .= $string[$num];
                    $cur_pos++;

                    if ($string[$num] == "\n") {
                        $string = substr($string, $cur_pos, (strlen($string) - $cur_pos));
                        $cur_pos = 0;
                        $num = 0;
                    }
                }

                $outlines .= '<i class="fa fa-cut fa-lg"> </i>';
                $string = substr($string, $cur_pos, (strlen($string) - $cur_pos));
            }

            $string = $outlines . $string;
        }

        // }
    }

    /**
     * [changetoamp description]
     *
     * @param   array   $r  [$r description]
     *
     * @return  string
     */
    public static function changetoamp(array $r): string
    {
        return str_replace('&', '&amp;', $r[0]);
    }

    /**
     * [changetoampadm description]
     *
     * @param   array   $r  [$r description]
     *
     * @return  string
     */
    public static function changetoampadm(array $r): string
    {
        return static::changetoamp($r[0]);
    }

    /**
     * Encode une chaine UF8 au format javascript - JPB 2005
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  string
     */
    public static function utf8_java(string $ibid): string
    {
        // UTF8 = &#x4EB4;&#x6B63;&#7578; 
        // javascript = \u4EB4\u6B63\u.dechex(7578)
        $tmp = explode('&#', $ibid);

        foreach ($tmp as $bidon) {
            if ($bidon) {

                $bidon = substr($bidon, 0, (int) strpos($bidon, ";"));
                $hex = strpos($bidon, 'x');

                $ibid = ($hex === false)
                    ? str_replace('&#' . $bidon . ';', '\\u' . dechex((int) $bidon), $ibid)
                    : str_replace('&#' . $bidon . ';', '\\u' . substr((string) $bidon, 1), $ibid);
            }
        }

        return $ibid;
    }

    /**
     * Quote une chaîne contenant des '
     *
     * @param   string  $what  [$what description]
     *
     * @return  array|string|null
     */
    public static function FixQuotes(?string $what = ''): array|string|null
    {
        $what = str_replace("&#39;", "'", $what);
        $what = str_replace("'", "''", $what);

        while (preg_match("#\\\\'#", $what)) {
            $what = preg_replace("#\\\\'#", "'", $what);
        }

        return $what;
    }

    /**
     * [addslashes_GPC description]
     *
     * @param   string  $arr  [$arr description]
     *
     * @return  void
     */
    public static function addslashes_GPC(string &$arr): void
    {
        $arr = addslashes($arr);
    }

    /**
     * 
     *
     * @param   string  $input  [$input description]
     *
     * @return  string
     */
    public static function undo_htmlspecialchars(string $input): string 
    {
        $input = preg_replace("/&gt;/i", ">", $input);
        $input = preg_replace("/&lt;/i", "<", $input);
        $input = preg_replace("/&quot;/i", "\"", $input);
        $input = preg_replace("/&amp;/i", "&", $input);
    
        return $input;
    }

}
