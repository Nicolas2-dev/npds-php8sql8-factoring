<?php

if (! function_exists('searchbox'))
{
    /**
     * Bloc Search-engine
     * syntaxe : function#searchbox
     *
     * @return  void    [return description]
     */
    function searchbox(): void
    {
        global $block_title;

        $title = $block_title == '' ? __d('two_search', 'Recherche') : $block_title;

        $content = '
        <form id="searchblock" action="'. site_url('search.php') .'" method="get">
        <input class="form-control" type="text" name="query" />
        </form>';

        themesidebox($title, $content);
    }
}
