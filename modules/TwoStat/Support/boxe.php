<?php

use Two\Support\Facades\Config;
use Modules\TwoStat\Support\Stats;
use Modules\TwoCore\Support\Sanitize;
use Modules\TwoThemes\Support\Facades\Theme;


if (! function_exists('Site_Activ'))
{
    /**
     * Bloc activité du site
     * syntaxe : function#Site_Activ
     *
     * @return  void    [return description]
     */
    function Site_Activ(): void
    {
        list($membres, $totala, $totalb, $totalc, $totald, $totalz) = Stats::req_stat();

        $aff = '
        <p class="text-center">'. __d('two_stat', 'Pages vues depuis') .' '. Config::get('two_core::config.startdate') .' : <span class="fw-semibold">'. Sanitize::wrh($totalz) .'</span></p>
        <ul class="list-group mb-3" id="site_active">
        <li class="my-1">'. __d('two_stat', 'Nb. de membres') .' <span class="badge rounded-pill bg-secondary float-end">'. Sanitize::wrh(($membres)) .'</span></li>
        <li class="my-1">'. __d('two_stat', 'Nb. d\'articles') .' <span class="badge rounded-pill bg-secondary float-end">'. Sanitize::wrh($totala) .'</span></li>
        <li class="my-1">'. __d('two_stat', 'Nb. de forums') .' <span class="badge rounded-pill bg-secondary float-end">'. Sanitize::wrh($totalc) .'</span></li>
        <li class="my-1">'. __d('two_stat', 'Nb. de sujets') .' <span class="badge rounded-pill bg-secondary float-end">'. Sanitize::wrh($totald) .'</span></li>
        <li class="my-1">'. __d('two_stat', 'Nb. de critiques') .' <span class="badge rounded-pill bg-secondary float-end">'. Sanitize::wrh($totalb) .'</span></li>
        </ul>';

        if ($ibid = Theme::theme_image("box/top.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = false;
        } // no need

        if ($imgtmp) {
            $aff .= '<p class="text-center">
                <a href="'. site_url('top.php') .'">
                    <img src="'. $imgtmp .'" alt="'. __d('two_stat', 'Top') .' '. Config::get('two_core::config.top') .'" />
                </a>&nbsp;&nbsp;';

            if ($ibid = Theme::theme_image("box/stat.gif")) {
                $imgtmp = $ibid;
            } else {
                $imgtmp = false;
            } // no need

            $aff .= '<a href="'. site_url('stats.php') .'">
                    <img src="'. $imgtmp .'" alt="'. __d('two_stat', 'Statistiques') .'" />
                </a></p>';
        } else {
            $aff .= '<p class="text-center">
                <a href="'. site_url('top.php') .'">
                    '. __d('two_stat', 'Top') .' '. Config::get('two_core::config.top') .'
                </a>
                &nbsp;&nbsp;
                    <a href="'. site_url('stats.php') .'">'. __d('two_stat', 'Statistiques') .'
                </a>
            </p>';
        }

        global $block_title;
        $title = $block_title == '' ? __d('two_stat', 'Activité du site') : $block_title;

        Theme::themesidebox($title, $aff);
    }
}