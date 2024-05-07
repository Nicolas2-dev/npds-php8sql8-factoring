<?php

use Two\Support\Facades\DB;
use Modules\TwoCore\Support\Sanitize;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;


if (! function_exists('mainblock'))
{
    /**
     * Bloc principal
     * syntaxe : function#mainblock
     *
     * @return  void    [return description]
     */
    function mainblock(): void
    {
        $block = DB::table('block')
                    ->select('title', 'content')
                    ->where('id', 1)
                    ->first();

        global $block_title;
        if ($block->title == '') {
            $block->title = $block_title;
        }

        //must work from php 4 to 7 !..?..
        Theme::themesidebox(
            Language::aff_langue($block->title), 
            Language::aff_langue(preg_replace_callback('#<a href=[^>]*(&)[^>]*>#', 
            [Sanitize::class, 'changetoamp'], $block->content))
        );
    }
}
