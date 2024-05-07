<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;


if (! function_exists('ephemblock'))
{
    /**
     * Bloc ephemerid
     * syntaxe : function#ephemblock
     *
     * @return  void    [return description]
     */
    function ephemblock(): void
    {
        $cnt = 0;
        $eday = date("d", time() + ((int) Config::get('two_core::config.gmt') * 3600));
        $emonth = date("m", time() + ((int) Config::get('two_core::config.gmt') * 3600));

        $result = DB::table('ephem')
                    ->select('yid', 'content')
                    ->where('did', $eday)
                    ->where('mid', $emonth)
                    ->orderBy('yid', 'asc')
                    ->get();
        
        $boxstuff = '<div>' . translate("En ce jour...") . '</div>';

        foreach ($result as $ephem) {
            if ($cnt == 1) {
                $boxstuff .= "\n<br />\n";
            }

            $boxstuff .= "<b>". $ephem['yid'] ."</b>\n<br />\n";
            $boxstuff .= Language::aff_langue($ephem['content']);
            $cnt = 1;
        }

        $boxstuff .= "<br />\n";

        global $block_title;
        $title = $block_title == '' ? translate("Ephémérides") : $block_title;

        Theme::themesidebox($title, $boxstuff);
    }
}
