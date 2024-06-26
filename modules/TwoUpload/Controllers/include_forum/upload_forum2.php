<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/* Copyright Snipe 2003  base sources du forum w-agora de Marc Druilhe  */
/************************************************************************/
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\assets\css;
use npds\support\forum\forum;
use npds\system\config\Config;

if (!stristr($_SERVER['PHP_SELF'], 'modules.php')) die();

global $Titlesitename;
/*****************************************************/
/* Include et définition                             */
/*****************************************************/
$forum = $IdForum;
include_once("auth.php");

include_once("modules/upload/language/$language/language.php");
include_once("modules/upload/http/include_forum/upload.conf.forum.php");
include_once("modules/upload/http/include_forum/upload.func.forum.php");
include_once("library/file.php");

$inline_list['1'] = __d('two_upload', 'Oui');
$inline_list['0'] = __d('two_upload', 'Non');

// Security
if (!$allow_upload_forum) Header("Location: die.php");
if (!forum::autorize()) Header("Location: die.php");

/*****************************************************/
/* Entete                                            */
/*****************************************************/
ob_start();
$Titlesitename = __d('two_upload', 'Télécharg.');
include("storage/meta/meta.php");
$userX = base64_decode($user);
$userdata = explode(':', $userX);
if ($userdata[9] != '') {
    if (!$file = @opendir("themes/$userdata[9]"))
        $theme = Config::get('npds.Default_Theme');
    else
        $theme = $userdata[9];
} else
    $theme = Config::get('npds.Default_Theme');

if (isset($user)) {
    global $cookie;
    $skin = '';
    if (array_key_exists(11, $cookie)) $skin = $cookie[11];
}
echo '
            <link rel="stylesheet" href="assets/shared/font-awesome/css/all.min.css" />';
if ($skin != '') {
    echo '
            <link rel="stylesheet" href="themes/_skins/' . $skin . '/bootstrap.min.css" />
            <link rel="stylesheet" href="themes/_skins/' . $skin . '/extra.css" />';
} else
    echo ' 
            <link rel="stylesheet" href="assets/shared/bootstrap/dist/css/bootstrap.min.css" />';
echo '
            <link rel="stylesheet" href="assets/shared/bootstrap-table/dist/bootstrap-table.min.css" />'; //hardcoded lol
echo css::import_css($theme, Config::get('npds.language'), '', '', '');
echo '
        </head>
    <body>';

// Moderator
global $NPDS_Prefix;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$sql = "SELECT forum_moderator FROM " . $NPDS_Prefix . "forums WHERE forum_id = '$forum'";
if (!$result = sql_query($sql))
    forum::forumerror('0001');
$myrow = sql_fetch_assoc($result);
$moderator = forum::get_moderator($myrow['forum_moderator']);
$moderator = explode(' ', $moderator);
$Mmod = false;
for ($i = 0; $i < count($moderator); $i++) {
    if (($userdata[1] == $moderator[$i])) {
        $Mmod = true;
        break;
    }
}
$thanks_msg = '';
//settype($thanks_msg,'string');
settype($actiontype, 'string');
settype($visible_att, 'array');
if ($actiontype) {
    switch ($actiontype) {
        case 'delete':
            delete($del_att);
            break;
        case 'upload':
            $thanks_msg = forum_upload();
            break;
        case 'update':
            update_inline($inline_att);
            break;
        case 'visible':
            if ($Mmod) {
                update_visibilite($visible_att, $visible_list);
            }
            break;
    }
}

include("modules/upload/include/minigf.php");

/*****************************************************/
/* Upload du fichier                                 */
/*****************************************************/
function forum_upload()
{
    global $apli, $IdPost, $IdForum, $IdTopic, $pcfile, $pcfile_size, $pcfile_name, $pcfile_type, $att_count, $att_size, $total_att_count, $total_att_size;
    global $MAX_FILE_SIZE, $MAX_FILE_SIZE_TOTAL, $mimetypes, $mimetype_default, $upload_table, $rep_upload_forum; // mine......

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    list($sum) = sql_fetch_row(sql_query("SELECT SUM(att_size ) FROM $upload_table WHERE apli = '$apli' AND post_id = '$IdPost'"));

    // gestion du quota de place d'un post
    if (($MAX_FILE_SIZE_TOTAL - $sum) < $MAX_FILE_SIZE)
        $MAX_FILE_SIZE = $MAX_FILE_SIZE_TOTAL - $sum;
    include "modules/upload/include/fileupload.php";
    settype($thanks_msg, 'string');

    // Récupération des valeurs de PCFILE
    global $HTTP_POST_FILES, $_FILES;
    if (!empty($HTTP_POST_FILES))
        $fic = $HTTP_POST_FILES;
    else
        $fic = $_FILES;
    $pcfile_name = $fic['pcfile']['name'];
    $pcfile_type = $fic['pcfile']['type'];
    $pcfile_size = $fic['pcfile']['size'];
    $pcfile = $fic['pcfile']['tmp_name'];

    $fu = new FileUpload;
    $fu->init($rep_upload_forum, $IdForum, $apli);

    $att_count = 0;
    $att_size = 0;
    $total_att_count = 0;
    $total_att_size = 0;

    $attachments = $fu->getUploadedFiles($IdPost, $IdTopic);
    if (is_array($attachments)) {
        $att_count = $attachments['att_count'];
        $att_size = $attachments['att_size'];
        if (is_array($pcfile_name)) {
            reset($pcfile_name);
            $names = implode(', ', $pcfile_name);
            $pcfile_name = $names;
        }
        $pcfile_size = $att_size;
        $thanks_msg .= '<div class="alert alert-success alert-dismissible fade show" role="alert"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' . str_replace('{NAME}', '<strong>' . $pcfile_name . '</strong>', str_replace('{SIZE}', $pcfile_size, __d('two_upload', 'Fichier {NAME} bien reçu ({SIZE} octets transférés)'))) . '</div>';
        $total_att_count += $att_count;
        $total_att_size += $att_size;
    }
    return ($thanks_msg);
}
