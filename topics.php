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
if (!function_exists("Mysql_Connexion"))
    include('boot/bootstrap.php');

settype($op, 'string');
if ($op != "maj_subscribe") {
    include("themes/default/header.php");
    $inclusion = false;
    if (file_exists("themes/$theme/view/topics.html"))
        $inclusion = "themes/$theme/view/topics.html";
    elseif (file_exists("themes/default/view/topics.html"))
        $inclusion = "themes/default/view/topics.html";
    else
        echo 'html/topics.html / not find !<br />';
    if ($inclusion) {
        ob_start();
        include($inclusion);
        $Xcontent = ob_get_contents();
        ob_end_clean();
        echo meta_lang(aff_langue($Xcontent));
    }
    include("themes/default/footer.php");
} else {
    if ($subscribe) {
        if ($user) {
            $result = sql_query("DELETE FROM " . $NPDS_Prefix . "subscribe WHERE uid='$cookie[0]' AND topicid!=NULL");
            $selection = sql_query("SELECT topicid FROM " . $NPDS_Prefix . "topics ORDER BY topicid");
            while (list($topicid) = sql_fetch_row($selection)) {
                if (isset($Subtopicid)) {
                    if (array_key_exists($topicid, $Subtopicid)) {
                        if ($Subtopicid[$topicid] == "on") {
                            $resultX = sql_query("INSERT INTO " . $NPDS_Prefix . "subscribe (topicid, uid) VALUES ('$topicid','$cookie[0]')");
                        }
                    }
                }
            }
            redirect_url("topics.php");
        }
    }
}
