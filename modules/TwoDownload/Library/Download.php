<?php

declare(strict_types=1);

namespace Modules\TwoDownload\Library;


use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Sanitize;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;


class Download
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
        $long_chain = Theme::getConfig('config.long_chain');

        if (!$long_chain) {
            $long_chain = 13;
        }
 
        $result = DB::table('downloads')
                    ->select('did', 'dcounter', 'dfilename', 'dcategory', 'ddate', 'perms')
                    ->orderBy($ordre, 'desc')
                    ->limit(Config::get('two_core::config.top'))
                    ->offset(0)
                    ->get();
        
        $lugar = 1;
        $ibid = '';

        foreach ($result as $download) {    
            if ($download->dcounter > 0) {
                $okfile = User::autorisation($download->perms);
                
                if ($ordre == 'dcounter') {
                    $dd = Sanitize::wrh($download->dcounter);
                }
                
                if ($ordre == 'ddate') {
                    $dd = translate("dateinternal");

                    $day = substr($download->ddate, 8, 2);
                    $month = substr($download->ddate, 5, 2);
                    $year = substr($download->ddate, 0, 4);

                    $dd = str_replace('d', $day, $dd);
                    $dd = str_replace('m', $month, $dd);
                    $dd = str_replace('Y', $year, $dd);
                    $dd = str_replace("H:i", "", $dd);
                }

                $ori_dfilename = $download->dfilename;
                
                if (strlen($download->dfilename) > $long_chain) {
                    $download->dfilename = (substr($download->dfilename, 0, $long_chain)) ." ...";
                }

                if ($form == 'short') {
                    if ($okfile) {
                        $ibid .= '<li class="list-group-item list-group-item-action d-flex justify-content-start p-2 flex-wrap">'. $lugar .' 
                            <a class="ms-2" href="'. site_url('download.php?op=geninfo&amp;did='. $download->did .'&amp;out_template=1') .'" title="'. $ori_dfilename .' '. $dd .'" >
                                '. $download->dfilename .'
                            </a>
                            <span class="badge bg-secondary ms-auto align-self-center">'. $dd .'</span>
                        </li>';
                    }
                } else {
                    if ($okfile) {
                        $ibid .= '<li class="ms-4 my-1">
                            <a href="'. site_url('download.php?op=mydown&amp;did='. $download->did) .'" >
                                '. $download->dfilename .'
                            </a> ('. translate("Catégorie") .' :'. Language::aff_langue(stripslashes($download->dcategory)) .')&nbsp;
                            <span class="badge bg-secondary float-end align-self-center">'. Sanitize::wrh($download->dcounter) .'</span>
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
