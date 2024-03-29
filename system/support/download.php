<?php

declare(strict_types=1);

namespace npds\system\support;

use npds\system\auth\users;
use npds\system\support\str;
use npds\system\language\language;

class download
{

    /**
     * Bloc topdownload et lastdownload
     *
     * @param   string  $form   [$form description]
     * @param   string  $ordre  [$ordre description]
     *
     * @return  string          [return description]
     */
    public static function topdownload_data(string $form, string $ordre): string
    {
        global $NPDS_Prefix, $top, $long_chain;

        if (!$long_chain) {
            $long_chain = 13;
        }

        settype($top, 'integer');

        $result = sql_query("SELECT did, dcounter, dfilename, dcategory, ddate, perms FROM " . $NPDS_Prefix . "downloads ORDER BY $ordre DESC LIMIT 0, $top");
        $lugar = 1;
        $ibid = '';

        while (list($did, $dcounter, $dfilename, $dcategory, $ddate, $dperm) = sql_fetch_row($result)) {
            if ($dcounter > 0) {
                $okfile = users::autorisation($dperm);
                
                if ($ordre == 'dcounter') {
                    $dd = str::wrh($dcounter);
                }
                
                if ($ordre == 'ddate') {
                    $dd = translate("dateinternal");

                    $day = substr($ddate, 8, 2);
                    $month = substr($ddate, 5, 2);
                    $year = substr($ddate, 0, 4);

                    $dd = str_replace('d', $day, $dd);
                    $dd = str_replace('m', $month, $dd);
                    $dd = str_replace('Y', $year, $dd);
                    $dd = str_replace("H:i", "", $dd);
                }

                $ori_dfilename = $dfilename;
                
                if (strlen($dfilename) > $long_chain) {
                    $dfilename = (substr($dfilename, 0, $long_chain)) . " ...";
                }

                if ($form == 'short') {
                    if ($okfile) {
                        $ibid .= '<li class="list-group-item list-group-item-action d-flex justify-content-start p-2 flex-wrap">' . $lugar . ' <a class="ms-2" href="download.php?op=geninfo&amp;did=' . $did . '&amp;out_template=1" title="' . $ori_dfilename . ' ' . $dd . '" >' . $dfilename . '</a><span class="badge bg-secondary ms-auto align-self-center">' . $dd . '</span></li>';
                    }
                } else {
                    if ($okfile) {
                        $ibid .= '<li class="ms-4 my-1"><a href="download.php?op=mydown&amp;did=' . $did . '" >' . $dfilename . '</a> (' . translate("Catégorie") . ' : ' . language::aff_langue(stripslashes($dcategory)) . ')&nbsp;<span class="badge bg-secondary float-end align-self-center">' . str::wrh($dcounter) . '</span></li>';
                    }
                }

                if ($okfile) {
                    $lugar++;
                }
            }
        }

        sql_free_result($result);

        return $ibid;
    }
}
