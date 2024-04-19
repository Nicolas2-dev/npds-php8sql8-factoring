<?php

declare(strict_types=1);

namespace npds\support;

use npds\support\auth\users;
use npds\support\str;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\DB;

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
        if (!Config::get('npds.theme.long_chain')) {
            Config::set('npds.theme.long_chain', 13);
        }
 
        $result = DB::table('downloads')
                    ->select('did', 'dcounter', 'dfilename', 'dcategory', 'ddate', 'perms')
                    ->orderBy($ordre, 'desc')
                    ->limit(Config::get('npds.top'))
                    ->offset(0)
                    ->get();
        
        $lugar = 1;
        $ibid = '';

        foreach ($result as $download) {    
            if ($download['dcounter'] > 0) {
                $okfile = users::autorisation($download['perms']);
                
                if ($ordre == 'dcounter') {
                    $dd = str::wrh($download['dcounter']);
                }
                
                if ($ordre == 'ddate') {
                    $dd = translate("dateinternal");

                    $day = substr($download['ddate'], 8, 2);
                    $month = substr($download['ddate'], 5, 2);
                    $year = substr($download['ddate'], 0, 4);

                    $dd = str_replace('d', $day, $dd);
                    $dd = str_replace('m', $month, $dd);
                    $dd = str_replace('Y', $year, $dd);
                    $dd = str_replace("H:i", "", $dd);
                }

                $ori_dfilename = $download['dfilename'];
                
                // not used !!!!
                // if (strlen($download['dfilename']) > Config::get('npds.theme.long_chain')) {
                //     $dfilename = (substr($download['dfilename'], 0, Config::get('npds.theme.long_chain'))) ." ...";
                // }

                if ($form == 'short') {
                    if ($okfile) {
                        $ibid .= '<li class="list-group-item list-group-item-action d-flex justify-content-start p-2 flex-wrap">'. $lugar .' 
                            <a class="ms-2" href="'. site_url('download.php?op=geninfo&amp;did='. $download['did'] .'&amp;out_template=1') .'" title="'. $ori_dfilename .' '. $dd .'" >
                                '. $download['dfilename'] .'
                            </a>
                            <span class="badge bg-secondary ms-auto align-self-center">'. $dd .'</span>
                        </li>';
                    }
                } else {
                    if ($okfile) {
                        $ibid .= '<li class="ms-4 my-1">
                            <a href="'. site_url('download.php?op=mydown&amp;did='. $download['did']) .'" >
                                '. $download['dfilename'] .'
                            </a> ('. translate("Catégorie") .' :'. language::aff_langue(stripslashes($download['dcategory'])) .')&nbsp;
                            <span class="badge bg-secondary float-end align-self-center">'. str::wrh($download['dcounter']) .'</span>
                        </li>';
                    }
                }

                if ($okfile) {
                    $lugar++;
                }
            }
        }

        return $ibid;
    }
}
