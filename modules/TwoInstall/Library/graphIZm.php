<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/* IZ-Xinstall version : 1.2                                            */
/*                                                                      */
/* Auteurs : v.0.1.0 EBH (plan.net@free.fr)                             */
/*         : v.1.1.1 jpb, phr                                           */
/*         : v.1.1.2 jpb, phr, dev, boris                               */
/*         : v.1.1.3 dev - 2013                                         */
/*         : v.1.2 phr, jpb - 2017                                      */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

function entete()
{
    global $langue, $cms_logo, $cms_name, $stage, $Version_Num, $Version_Id, $Version_Sub;
    echo '<html>
    <head>
        <meta charset="utf-8">
        <title>NPDS IZ-Xinstall - Installation &amp; Configuration</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="identifier-url" content="" />
        <meta name="author" content="Developpeur, EBH, jpb, phr" />
        <meta name="owner" content="npds.org" />
        <meta name="reply-to" content="developpeur@npds.org" />
        <meta name="language" content="fr" />
        <meta http-equiv="content-language" content="fr, fr-be, fr-ca, fr-lu, fr-ch" />
        <meta name="description" content="NPDS IZ-Xinstall" />
        <meta name="keywords" content="NPDS, Installateur automatique" />
        <meta name="rating" content="general" />
        <meta name="distribution" content="global" />
        <meta name="copyright" content="npds.org 2001-2016" />
        <meta name="revisit-after" content="15 days" />
        <meta name="resource-type" content="document" />
        <meta name="robots" content="none" />
        <meta name="generator" content="NPDS IZ-Xinstall" />
        <link rel="stylesheet" href="assets/shared/font-awesome/css/all.min.css" />
        <link rel="stylesheet" href="assets/shared/bootstrap/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="assets/shared/formvalidation/dist/css/formValidation.min.css">
        <link rel="stylesheet" href="themes/npds-boost_sk/style/style.css">
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="my-2">
                <div class="row">
                <div class="col-sm-2 d-none d-sm-inline-block"><img class="img-fluid" src="install/images/header.png" alt="NPDS logo" /></div>
                <div id="logo_header" class="col-sm-10">
                    <h1 class="display-4">NPDS<br /><small class="text-muted"><em>installation automatique</em></small></h1>
                </div>
                </div>
                <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6 text-sm-end">' . $Version_Sub . ' ' . $Version_Num . '</div>
                </div>
            </div>
            <hr class="lead" />';
}
function pied_depage()
{
    global $stage;
    echo '
            <div class="col text-center">
                <hr class="lead" /><a href="http://www.npds.org" target="_blank">NPDS</a> IZ-Xinstall version : 1.2 <i class="fa fa-spinner fa-spin fa-lg fa-fw text-success"></i><span class="visually-hidden">On work...</span>
            </div>
        </div>
    </body>
    </html>';
    exit();
}
function page_message($chaine)
{
    entete();
    echo '
    <h2>' . $chaine . '</h2>';
    pied_depage();
}
function menu()
{
    global $menu, $langue, $colorst1, $colorst2, $colorst3, $colorst4, $colorst5, $colorst6, $colorst7, $colorst8, $colorst9, $colorst10, $phpver;
    $menu = '';
    $menu .= '
            <div class="row">
                <div class="col-md-3">
                <ul class="list-group mb-3">
                    <li class="list-group-item list-group-item' . $colorst1 . '">' . __d('two_install', 'Langue') . '</li>
                    <li class="list-group-item list-group-item' . $colorst2 . '">' . __d('two_install', 'Bienvenue') . '</li>
                    <li class="list-group-item list-group-item' . $colorst3 . '">' . __d('two_install', 'Licence') . '</li>
                    <li class="list-group-item list-group-item' . $colorst4 . '">' . __d('two_install', 'Vérification des fichiers') . '</li>
                    <li class="list-group-item list-group-item' . $colorst5 . '">' . __d('two_install', 'Paramètres de connexion') . '</li>
                    <li class="list-group-item list-group-item' . $colorst6 . '">' . __d('two_install', 'Autres paramètres') . '</li>
                    <li class="list-group-item list-group-item' . $colorst7 . '">' . __d('two_install', 'Base de données') . '</li>
                    <li class="list-group-item list-group-item' . $colorst8 . '">' . __d('two_install', 'Compte Admin') . '</li>
                    <li class="list-group-item list-group-item' . $colorst9 . '">' . __d('two_install', 'Module UPload') . '</li>
                    <li class="list-group-item list-group-item' . $colorst10 . '">' . __d('two_install', 'Fin') . '</li>
                    <li class="list-group-item list-group-item-light"><code class="small">Version Php ' . $phpver . '</code></li>
                </ul>
                </div>
                <div class="col-md-9">';
    return $menu;
}
