<?php

use Two\Support\Facades\Config;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;


if (! function_exists('bloc_langue'))
{
    /**
     * Bloc langue 
     * syntaxe : function#bloc_langue
     *
     * @return  void
     */
    function bloc_langue(): void
    {
        global $block_title;

        if (Config::get('npds.multi_langue')) {
            $title = $block_title == '' ? translate("Choisir une langue") : $block_title;
            Theme::themesidebox($title, Language::aff_local_langue(site_url('index.php'), "choice_user_language", ''));
        }
    }
}
