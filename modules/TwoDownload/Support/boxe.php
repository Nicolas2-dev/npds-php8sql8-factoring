<?php

use Modules\TwoDownload\Library\Download;
use Modules\TwoThemes\Support\Facades\Theme;



if (! function_exists('topdownload'))
{
    /**
     * Bloc topdownload
     * syntaxe : function#topdownload
     *
     * @return  void    [return description]
     */
    function topdownload(): void
    {
        global $block_title;

        $title = $block_title == '' ? __d('two_download', 'Les plus téléchargés') : $block_title;

        $boxstuff = '<ul>';
        $boxstuff .= Download::topdownload_data('short', 'dcounter');
        $boxstuff .= '</ul>';

        if ($boxstuff == '<ul></ul>') {
            $boxstuff = '';
        }

        Theme::themesidebox($title, $boxstuff);
    }
}

if (! function_exists('lastdownload'))
{
    /**
     * Bloc lastdownload
     * syntaxe : function#lastdownload
     *
     * @return  void    [return description]
     */
    function lastdownload(): void
    {
        global $block_title;

        $title = $block_title == '' ? __d('two_download', 'Fichiers les + récents') : $block_title;

        $boxstuff = '<ul>';
        $boxstuff .= Download::topdownload_data('short', 'ddate');
        $boxstuff .= '</ul>';

        if ($boxstuff == '<ul></ul>') {
            $boxstuff = '';
        }

        Theme::themesidebox($title, $boxstuff);
    }
}
