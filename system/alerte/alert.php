<?php

declare(strict_types=1);

namespace npds\system\assets;


class alerte
{


    public static function () 
    {
        //==> recupérations des états des fonctions d'ALERTE ou activable et maj (faire une fonction avec cache court dev ..)
        //article à valider
        $newsubs = sql_num_rows(sql_query("SELECT qid FROM " . $NPDS_Prefix . "queue"));
        
        if ($newsubs) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newsubs . "',fretour_h='" . adm_translate("Articles en attente de validation !") . "' WHERE fid='38'");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='38'");
        
    }

    public static function () 
    {
        //news auto
        $newauto = sql_num_rows(sql_query("SELECT anid FROM " . $NPDS_Prefix . "autonews"));
        if ($newauto) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newauto . "',fretour_h='" . adm_translate("Articles programmés pour la publication.") . "' WHERE fid=37");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0',fretour_h='' WHERE fid=37");
        
    }

    public static function () 
    {
        //etat filemanager
        if (Config::get('filemanager.manager')) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1' WHERE fid='27'");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0' WHERE fid='27'");
        
    }

    public static function () 
    {
        //utilisateur à valider
        $newsuti = sql_num_rows(sql_query("SELECT uid FROM " . $NPDS_Prefix . "users_status WHERE uid!='1' AND open='0'"));
        if ($newsuti) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newsuti . "',fretour_h='" . adm_translate("Utilisateur en attente de validation !") . "' WHERE fid='44'");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='44'");
    
    }

    public static function () 
    {
        //référants à gérer
        if (Config::get('npds.httpref') == 1) {
            $result = sql_fetch_assoc(sql_query("SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "referer"));
            if ($result['total'] >= Config::get('npds.httprefmax')) 
                sql_query("UPDATE " . $NPDS_Prefix . "fonctions set fetat='1', fretour='!!!' WHERE fid='39'");
            else 
                sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0' WHERE fid='39'");
        }
    }

    public static function () 
    {
        //critique en attente
        $critsubs = sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "reviews_add"));
        if ($critsubs) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $critsubs . "', fretour_h='" . adm_translate("Critique en attente de validation.") . "' WHERE fid='35'");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='35'");
    
    }

    public static function () 
    {
        //nouveau lien à valider
        $newlink = sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "links_newlink"));
        if ($newlink) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newlink . "', fretour_h='" . adm_translate("Liens à valider.") . "' WHERE fid='41'");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='41'");
    
    }

    public static function () 
    {
        //lien rompu à valider
        $brokenlink = sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "links_modrequest where brokenlink='1'"));
        if ($brokenlink) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $brokenlink . "', fretour_h='" . adm_translate("Liens rompus à valider.") . "' WHERE fid='42'");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='42'");
    
    }

    public static function () 
    {
        //nouvelle publication
        $newpubli = $Q['radminsuper'] == 1 ?
            sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "seccont_tempo")) :
            sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "seccont_tempo WHERE author='$aid'"));
        
        if ($newpubli) 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newpubli . "', fretour_h='" . adm_translate("Publication(s) en attente de validation") . "' WHERE fid='50'");
        else 
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='50'");
    
    }

    public static function () 
    {
        //utilisateur(s) en attente de groupe
        $directory = "storage/users_private/groupe";
        $iterator = new DirectoryIterator($directory);

        $j = 0;

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() and strpos($fileinfo->getFilename(), 'ask4group') !== false)
                $j++;
        }

        if ($j > 0)
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $j . "',fretour_h='" . adm_translate("Utilisateur en attente de groupe !") . "' WHERE fid='46'");
        else
            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='46'");

    }

    public static function () 
    {
        //==> Pour les modules installés produisant des notifications
        $alert_modules = sql_query("SELECT * FROM " . $NPDS_Prefix . "fonctions f LEFT JOIN " . $NPDS_Prefix . "modules m ON m.mnom = f.fnom WHERE m.minstall=1 AND fcategorie=9");
        
        if ($alert_modules) {
            while ($am = sql_fetch_array($alert_modules)) {
                include("modules/" . $am['fnom'] . "/admin/adm_alertes.php");
                
                $nr = count($reqalertes);
                $i = 0;
                
                while ($i < $nr) {
                    $ibid = sql_num_rows(sql_query($reqalertes[$i][0]));
                    
                    if ($ibid) {
                        $fr = $reqalertes[$i][1] != 1 ? $reqalertes[$i][1] : $ibid;
                        sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $fr . "', fretour_h='" . $reqalertes[$i][2] . "' WHERE fid=" . $am['fid'] . "");
                    } else
                        sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='' WHERE fid=" . $am['fid'] . "");
                    
                    $i++;
                }
            }
        }
        //<== Pour les modules installés produisant des notifications
    }

    public static function () 
    {

    }

    public static function () 
    {

    }

    public static function () 
    {

    }

    public static function () 
    {

    }

    public static function () 
    {

    }
}