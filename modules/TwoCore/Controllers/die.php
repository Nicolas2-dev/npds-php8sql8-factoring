<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\system\config\Config;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

function accessAdmin()
{
    global $pdst;

    $pdst = -1;

    Config::set('npds.Titlesitename', 'NPDS - admin erreur !');
    include("themes/default/header.php");

    echo '
        <link id="bsth" rel="stylesheet" href="/assets/shared/bootstrap/dist/css/bootstrap.min.css" />
        </head>
        <body>
        <div class="contenair-fluid mt-5">
            <div class= "card mx-auto p-3" style="width:380px; text-align:center">
                <span style="font-size: 72px;">🚫</span>
                <span class="text-danger h3 mb-3" style="">
                    Acc&egrave;s refus&eacute; ! <br />
                    Access denied ! <br />
                    Zugriff verweigert ! <br />
                    Acceso denegado ! <br />
                    &#x901A;&#x5165;&#x88AB;&#x5426;&#x8BA4; ! <br />
                </span>
                <hr />
                <div>
                    <span class="text-muted">NPDS - Portal System</span>
                    <img width="48px" class="adm_img ms-2" src="assets/images/admin/message_npds.png" alt="icon_npds">
                </div>
            </div>
        </div>
        </body>
        </html>';

    include("themes/default/footer.php");
}

function accessModule()
{
    global $pdst;

    $pdst = 0;

    Config::set('npds.Titlesitename', 'NPDS - Modules erreur !');
    include("themes/default/header.php");

    echo '
        <link id="bsth" rel="stylesheet" href="/assets/shared/bootstrap/dist/css/bootstrap.min.css" />
        </head>
        <body>
        <div class="contenair-fluid mt-5">
            <div class= "card mx-auto p-3" style="width:380px; text-align:center">
                <span style="font-size: 72px;">🚫</span>
                <span class="text-danger h3 mb-3" style="">
                    Acc&egrave;s refus&eacute; ! <br />
                    Access denied ! <br />
                    Zugriff verweigert ! <br />
                    Acceso denegado ! <br />
                    &#x901A;&#x5165;&#x88AB;&#x5426;&#x8BA4; ! <br />
                </span>
                <hr />
                <div>
                    <span class="text-muted">NPDS - Portal System</span>
                    <img width="48px" class="adm_img ms-2" src="assets/images/admin/message_npds.png" alt="icon_npds">
                </div>
            </div>
        </div>
        </body>
        </html>';

    include("themes/default/footer.php");
}

function accessNotModule()
{
    global $pdst;

    $pdst = 0;

    Config::set('npds.Titlesitename', 'NPDS - Modules erreur !');
    include("themes/default/header.php");

    echo '
        <link id="bsth" rel="stylesheet" href="/assets/shared/bootstrap/dist/css/bootstrap.min.css" />
        </head>
        <body>
        <div class="contenair-fluid mt-5">
            <div class= "card mx-auto p-3" style="width:380px; text-align:center">
                <span style="font-size: 72px;">🚫</span>
                <span class="text-danger h3 mb-3" style="">
                    Ce module n\'existe pas !!!<br />
                </span>
                <hr />
                <div>
                    <span class="text-muted">NPDS - Portal System</span>
                    <img width="48px" class="adm_img ms-2" src="assets/images/admin/message_npds.png" alt="icon_npds">
                </div>
            </div>
        </div>
        </body>
        </html>';

    include("themes/default/footer.php");
}

function accessError()
{
    Config::set('npds.Titlesitename', 'NPDS - Access erreur !');
    
    if (file_exists("storage/meta/meta.php")) {
        include("storage/meta/meta.php");
    }

    echo '
        <link id="bsth" rel="stylesheet" href="/assets/shared/bootstrap/dist/css/bootstrap.min.css" />
        </head>
        <body>
        <div class="contenair-fluid mt-5">
            <div class= "card mx-auto p-3" style="width:380px; text-align:center">
                <span style="font-size: 72px;">🚫</span>
                <span class="text-danger h3 mb-3" style="">
                    Acc&egrave;s refus&eacute; ! <br />
                    Access denied ! <br />
                    Zugriff verweigert ! <br />
                    Acceso denegado ! <br />
                    &#x901A;&#x5165;&#x88AB;&#x5426;&#x8BA4; ! <br />
                </span>
                <hr />
                <div>
                    <span class="text-muted">NPDS - Portal System</span>
                    <img width="48px" class="adm_img ms-2" src="assets/images/admin/message_npds.png" alt="icon_npds">
                </div>
            </div>
        </div>
        </body>
        </html>';

    die();
}

switch (Request::query('op')) {

    case 'admin':
        accessAdmin();
        break;

    case 'module':
        accessModule();
        break;

    case 'module-exist':
        accessNotModule();
        break;

    default:
        accessError();
        break;
}
