<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Cluster Paradise - Manage Data-Cluster  / Mod by Tribal-Dolphin      */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\utility\crypt;


function FindPartners_secur_cluster()
{
    if (file_exists("modules/cluster-paradise/config/data-cluster-M.php")) {
        include("modules/cluster-paradise/config/data-cluster-M.php");
        $cpt = 1;
        $part_cpt = 0;
        //while (each($part)) {
        foreach ($part as $client) {    
            if (strtoupper($part[$cpt]["OP"]) == "EXPORT") {
                $Xpart[$part_cpt]["WWW"] = $client[$cpt]["WWW"];
                $Xpart[$part_cpt]["SUBSCRIBE"] = $client[$cpt]["SUBSCRIBE"];
                $Xpart[$part_cpt]["OP"] = $client[$cpt]["OP"];
                $Xpart[$part_cpt]["FROMTOPICID"] = $client[$cpt]["FROMTOPICID"];
                $Xpart[$part_cpt]["TOTOPIC"] = $client[$cpt]["TOTOPIC"];
                $Xpart[$part_cpt]["FROMCATID"] = $client[$cpt]["FROMCATID"];
                $Xpart[$part_cpt]["TOCATEG"] = $client[$cpt]["TOCATEG"];
                $Xpart[$part_cpt]["AUTHOR"] = $client[$cpt]["AUTHOR"];
                $Xpart[$part_cpt]["MEMBER"] = $client[$cpt]["MEMBER"];
                $part_cpt = $part_cpt + 1;
            }
            $cpt = $cpt + 1;
        }
        return ($Xpart);
    }
}

function key_secur_cluster()
{
    if (file_exists("modules/cluster-paradise/config/data-cluster-M.php")) {
        include("modules/cluster-paradise/config/data-cluster-M.php");
        return (md5($part[0]["WWW"] . $part[0]["KEY"]));
    }
}

function L_encrypt($txt)
{
    if (file_exists("modules/cluster-paradise/config/data-cluster-M.php")) {
        include("modules/cluster-paradise/config/data-cluster-M.php");
        $key = $part[0]["KEY"];
    }
    return (crypt::encryptK($txt, $key));
}

if ($cluster_activate) {
    global $language;
    $local_key = key_secur_cluster();
    $tmp = FindPartners_secur_cluster();
    if (is_array($tmp)) {
        $cpt = 0;
        //while (each($tmp)) {
        foreach ($tmp as $key) {   
            if ((empty($key[$cpt]["FROMTOPICID"]) && empty($key[$cpt]["FROMCATID"])) || ($key[$cpt]["FROMTOPICID"] == $topic || $key[$cpt]["FROMCATID"] == $catid)) {
                echo "<script type=\"text/javascript\">\n//<![CDATA[\nvar cluster$cpt=window.open('', 'cluster$cpt', 'width=300, height=60, resizable=yes');\n//]]>\n</script>";
                $Zibid = "<html><head><title>NPDS - Cluster Paradise</title>";
                include("modules/upload/config/upload.conf.php");
                if ($url_upload_css) {
                    $url_upload_cssX = str_replace("style.css", "$language-style.css", $url_upload_css);
                    if (is_readable($url_upload . $url_upload_cssX))
                        $url_upload_css = $url_upload_cssX;
                    $Zibid .= "<link href=\"" . $url_upload . $url_upload_css . "\" title=\"default\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />";
                }
                $Zibid .= "</head><body topmargin=\"1\" leftmargin=\"1\">";
                $Zibid .= "<form action=\"http://" . $key[$cpt]["WWW"] . "/modules.php\" method=\"post\">";
                $Zibid .= "<input type=\"hidden\" name=\"ModPath\" value=\"cluster-paradise\" />";
                $Zibid .= "<input type=\"hidden\" name=\"ModStart\" value=\"cluster-E\" />";
                $Zibid .= "<input type=\"hidden\" name=\"Xop\" value=\"" . $key[$cpt]["SUBSCRIBE"] . "\" />";
                $Zibid .= "<input type=\"hidden\" name=\"key\" value=\"" . L_encrypt($local_key) . "\" />";
                if ((strtoupper($key[$cpt]["SUBSCRIBE"]) == "NEWS") and (strtoupper($key[$cpt]["OP"]) == "EXPORT")) {
                    if (isset($key[$cpt]["TOCATEG"])) {
                        $Xcatid = $key[$cpt]["TOCATEG"];
                    } else {
                        list($Xcatid) = sql_fetch_row(sql_query("select title from " . $NPDS_Prefix . "stories_cat where catid='$catid'"));
                    }
                    $Zibid .= "<input type=\"hidden\" name=\"Xcatid\" value=\"" . L_encrypt($Xcatid) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xaid\" value=\"" . L_encrypt($key[$cpt]["AUTHOR"]) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xsubject\" value=\"" . L_encrypt($subject) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xhometext\" value=\"" . L_encrypt($hometext) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xbodytext\" value=\"" . L_encrypt($bodytext) . "\" />";
                    if (isset($key[$cpt]["TOTOPIC"])) {
                        $Xtopic = $key[$cpt]["TOTOPIC"];
                    } else {
                        list($Xtopic) = sql_fetch_row(sql_query("select topictext from " . $NPDS_Prefix . "topics where topicid='$topic'"));
                    }
                    $Zibid .= "<input type=\"hidden\" name=\"Xtopic\" value=\"" . L_encrypt($Xtopic) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xauthor\" value=\"" . L_encrypt($key[$cpt]["MEMBER"]) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xnotes\" value=\"" . L_encrypt($notes) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xihome\" value=\"" . L_encrypt($ihome) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xdate_debval\" value=\"" . L_encrypt($date_debval) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xdate_finval\" value=\"" . L_encrypt($date_finval) . "\" />";
                    $Zibid .= "<input type=\"hidden\" name=\"Xepur\" value=\"" . L_encrypt($epur) . "\" />";
                }
                $Zibid .= "<input type=\"hidden\" name=\"Xurl_back\" value=\"cluster$cpt\" />";
                $Zibid .= "<br /><p align=\"center\"><span class=\"noir\" style=\"font-size: 12px;\"><b>" . __d('two_cluster_paradise', 'Mise à jour') . " : " . $key[$cpt]["WWW"] . "</b></span><br /><br />";
                $Zibid .= "<input type=\"submit\" class=\"bouton_standard\" value=\"" . __d('two_cluster_paradise', 'Valider') . "\" />&nbsp;&nbsp;";
                $Zibid .= "<input type=\"button\" class=\"bouton_standard\" value=\"" . __d('two_cluster_paradise', 'Annuler') . "\" onclick=\"window.close()\" /><br />";

                $Zibid .= "</p></form></body></html>";
                echo "<script type=\"text/javascript\">
                    //<![CDATA[
                    cluster$cpt.document.write('$Zibid');
                    //]]>
                    </script>";
                $cpt = $cpt + 1;
            }
        }
    }
}
