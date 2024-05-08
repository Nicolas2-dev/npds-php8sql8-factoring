<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
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
declare(strict_types=1);

use npds\support\assets\js;
use npds\support\logs\logs;
use npds\support\assets\css;
use npds\support\str;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'topicsmanager';
$f_titre = __d('two_news', 'Gestion des sujets');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit



switch ($op) {
    case 'topicsmanager':
        topicsmanager();
        break;

    case 'topicedit':
        topicedit($topicid);
        break;

    case 'topicmake':
        topicmake($topicname, $topicimage, $topictext, $topicadmin);
        break;

    case 'topicdelete':
        topicdelete($topicid, $ok);
        break;

    case 'topicchange':
        topicchange($topicid, $topicname, $topicimage, $topictext, $topicadmin, $name, $url);
        break;

    case 'relatedsave':
        relatedsave($tid, $rid, $name, $url);
        break;

    case 'relatededit':
        relatededit($tid, $rid);
        break;
        
    case 'relateddelete':
        relateddelete($tid, $rid);
        break;
}
