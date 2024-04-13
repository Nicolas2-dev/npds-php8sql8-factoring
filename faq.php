<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\cache\cache;
use npds\system\config\Config;
use npds\system\security\hack;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

/**
 *  
 *
 * @param   int     $id_cat      [$id_cat description]
 * @param   string  $categories  [$categories description]
 *
 * @return  void
 */
function ShowFaq(): void 
{
    $categories = Request::query('categories');

    echo '
    <h2 class="mb-4">' . translate("FAQ - Questions fréquentes") . '</h2>
    <hr />
    <h3 class="mb-3">' . translate("Catégorie") . ' <span class="text-muted"># ' . StripSlashes($categories) . '</span></h3>
    <p class="lead">
        <a href="'. site_url('faq.php') .'" title="' . translate("Retour à l'index FAQ") . '" data-bs-toggle="tooltip">Index</a>&nbsp;&raquo;&raquo;&nbsp;' . StripSlashes($categories) . '
    </p>';

    $id_cat = Request::query('id_cat');

    //cette requette ne sert a rien !!!
    foreach (DB::table('faqanswer')
                ->select('id', 'id_categorie', 'question', 'answer')
                ->where('id', $id_cat)
                ->get() as $faqanswer) 
    {
    }
}

/**
 * [ShowFaqAll description]
 *
 * @param   int   $id_cat  [$id_cat description]
 *
 * @return  void
 */
function ShowFaqAll(): void
{
    foreach (DB::table('faqanswer')
            ->select('id', 'id_categorie', 'question', 'answer')
            ->where('id_categorie', Request::query('id_cat'))
            ->get() as $faqanswer) 
    {
        echo '
        <div class="card mb-3" id="accordion_' . $faqanswer['id'] . '" role="tablist" aria-multiselectable="true">
            <div class="card-body">
                <h4 class="card-title">
                <a data-bs-toggle="collapse" data-parent="#accordion_' . $faqanswer['id'] . '" href="#faq_' . $faqanswer['id'] . '" aria-expanded="true" aria-controls="' . $faqanswer['id'] . '"><i class="fa fa-caret-down toggle-icon"></i></a>&nbsp;' . language::aff_langue($faqanswer['question']) . '
                </h4>
                <div class="collapse" id="faq_' . $faqanswer['id'] . '" >
                <div class="card-text">
                ' . metalang::meta_lang(language::aff_langue($faqanswer['answer'])) . '
                </div>
                </div>
            </div>
        </div>';
    }
}

if (!Request::query('myfaq')) {
    Config::set('npds.Titlesitename', "Faqs");
    include("themes/default/header.php");

    // start Caching page
    if (cache::cacheManagerStart2()) {

        $result = DB::table('faqcategories')
            ->select('id', 'categories')
            ->orderBy('id', 'asc')
            ->get();

        echo '
        <h2 class="mb-4">' . translate("FAQ - Questions fréquentes") . '</h2>
        <hr />
        <h3 class="mb-3">' . translate("Catégories") . '<span class="badge bg-secondary float-end">' . count($result) . '</span></h3>
        <div class="list-group">';
    
        foreach ($result as $categ) 
        {
            $catname = urlencode(language::aff_langue($categ['categories']));
            echo '<a class="list-group-item list-group-item-action" href="'. site_url('faq.php?id_cat=' . $categ['id'] . '&amp;myfaq=yes&amp;categories=' . $catname) .'">
                <h4 class="list-group-item-heading">' . language::aff_langue($categ['categories']) . '</h4>
            </a>';
        }

        echo '</div>';
    }

    // end Caching page
    cache::cacheManagerEnd();

    include("themes/default/footer.php");;
} else {
    Config::set('npds.Titlesitename', "Faqs : " . hack::removeHack(StripSlashes(Request::query('categories'))));
    
    include("themes/default/header.php");

    // start Caching page
    if (cache::cacheManagerStart2()) {

        ShowFaq();
        ShowFaqAll();
    }

    // end Caching page
    cache::cacheManagerEnd();
    
    include("themes/default/footer.php");
}
