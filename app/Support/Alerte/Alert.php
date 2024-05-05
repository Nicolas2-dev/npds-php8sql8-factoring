<?php

declare(strict_types=1);

namespace App\Support\Alerte;


class Alerte
{


    public static function () 
    {
        //==> recupérations des états des fonctions d'ALERTE ou activable et maj (faire une fonction avec cache court dev ..)
        //article à valider

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $newsubs = sql_num_rows(sql_query("SELECT qid FROM " . $NPDS_Prefix . "queue"));
        
        if ($newsubs) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newsubs . "',fretour_h='" . adm_translate("Articles en attente de validation !") . "' WHERE fid='38'");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='38'");
        
    }

    public static function () 
    {
        //news auto

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $newauto = sql_num_rows(sql_query("SELECT anid FROM " . $NPDS_Prefix . "autonews"));
        if ($newauto) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newauto . "',fretour_h='" . adm_translate("Articles programmés pour la publication.") . "' WHERE fid=37");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0',fretour_h='' WHERE fid=37");
        
    }

    public static function () 
    {
        //etat filemanager
        if (Config::get('filemanager.manager')) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1' WHERE fid='27'");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0' WHERE fid='27'");
        
    }

    public static function () 
    {
        //utilisateur à valider

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $newsuti = sql_num_rows(sql_query("SELECT uid FROM " . $NPDS_Prefix . "users_status WHERE uid!='1' AND open='0'"));
        if ($newsuti) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newsuti . "',fretour_h='" . adm_translate("Utilisateur en attente de validation !") . "' WHERE fid='44'");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='44'");
    
    }

    public static function () 
    {
        //référants à gérer
        if (Config::get('npds.httpref') == 1) {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_fetch_assoc(sql_query("SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "referer"));
            if ($result['total'] >= Config::get('npds.httprefmax')) 

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                sql_query("UPDATE " . $NPDS_Prefix . "fonctions set fetat='1', fretour='!!!' WHERE fid='39'");
            else 

                //DB::table('')->where('', )->update(array(
                //    ''       => ,
                //));

                sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0' WHERE fid='39'");
        }
    }

    public static function () 
    {
        //critique en attente

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $critsubs = sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "reviews_add"));
        if ($critsubs) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $critsubs . "', fretour_h='" . adm_translate("Critique en attente de validation.") . "' WHERE fid='35'");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='35'");
    
    }

    public static function () 
    {
        //nouveau lien à valider

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $newlink = sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "links_newlink"));
        if ($newlink) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newlink . "', fretour_h='" . adm_translate("Liens à valider.") . "' WHERE fid='41'");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='41'");
    
    }

    public static function () 
    {
        //lien rompu à valider

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $brokenlink = sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "links_modrequest where brokenlink='1'"));
        if ($brokenlink) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $brokenlink . "', fretour_h='" . adm_translate("Liens rompus à valider.") . "' WHERE fid='42'");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='42'");
    
    }

    public static function () 
    {
        //nouvelle publication
        $newpubli = $Q['radminsuper'] == 1 ?

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "seccont_tempo")) :

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "seccont_tempo WHERE author='$aid'"));
        
        if ($newpubli) 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $newpubli . "', fretour_h='" . adm_translate("Publication(s) en attente de validation") . "' WHERE fid='50'");
        else 

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

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

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $j . "',fretour_h='" . adm_translate("Utilisateur en attente de groupe !") . "' WHERE fid='46'");
        else

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='0' WHERE fid='46'");

    }

    public static function () 
    {
        //==> Pour les modules installés produisant des notifications

        // = DB::table('')->select()->where('', )->orderBy('')->get();

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

                        //DB::table('')->where('', )->update(array(
                        //    ''       => ,
                        //));

                        sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1',fretour='" . $fr . "', fretour_h='" . $reqalertes[$i][2] . "' WHERE fid=" . $am['fid'] . "");
                    } else

                        //DB::table('')->where('', )->update(array(
                        //    ''       => ,
                        //));

                        sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='0',fretour='' WHERE fid=" . $am['fid'] . "");
                    
                    $i++;
                }
            }
        }
        //<== Pour les modules installés produisant des notifications
    }

    public static function () 
    {
        //==> recuperation traitement des messages de NPDS

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $QM = sql_query("SELECT * FROM " . $NPDS_Prefix . "fonctions WHERE fnom REGEXP'mes_npds_[[:digit:]]'");
        
        settype($f_mes, 'array');
        
        while ($SQM = sql_fetch_assoc($QM)) {
            $f_mes[] = $SQM['fretour_h'];
        }

        //==> recuperation
        $messagerie_npds = file_get_contents('https://raw.githubusercontent.com/npds/npds_dune/master/versus.txt');
        $messages_npds = explode("\n", $messagerie_npds);

        array_pop($messages_npds);

        // traitement specifique car message permanent versus
        $versus_info = explode('|', $messages_npds[0]);

        if ($versus_info[1] == Config::get('versioning.Version_Sub') and $versus_info[2] == Config::get('versioning.Version_Num'))

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1', fretour='', fretour_h='Version NPDS " . Config::get('versioning.Version_Sub') . " " . Config::get('versioning.Version_Num') . "', furlscript='' WHERE fid='36'");
        else

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1', fretour='N', furlscript='data-bs-toggle=\"modal\" data-bs-target=\"#versusModal\"', fretour_h='Une nouvelle version NPDS est disponible !<br />" . $versus_info[1] . " " . $versus_info[2] . "<br />Cliquez pour télécharger.' WHERE fid='36'");

        $mess = array_slice($messages_npds, 1);

        if (empty($mess)) {
            //si pas de message on nettoie la base
            DB::table('fonctions')->where('fnom', 'REGEXP', 'mes_npds_[[:digit:]]')->delete();

            //DB::statement();

            sql_query("ALTER TABLE " . $NPDS_Prefix . "fonctions AUTO_INCREMENT = (SELECT MAX(fid)+1 FROM " . $NPDS_Prefix . "fonctions)");
        } else {
            $fico = '';
            $o = 0;

            foreach ($mess as $v) {
                $ibid = explode('|', $v);
                $fico = $ibid[0] != 'Note' ? 'message_npds_a' : 'message_npds_i';

                // = DB::table('')->select()->where('', )->orderBy('')->get();

                $QM = sql_num_rows(sql_query("SELECT * FROM " . $NPDS_Prefix . "fonctions WHERE fnom='mes_npds_" . $o . "'"));
                
                if ($QM === false)

                    //DB::table('')->insert(array(
                    //    ''       => ,
                    //));

                    sql_query("INSERT INTO " . $NPDS_Prefix . "fonctions (fnom,fretour_h,fcategorie,fcategorie_nom,ficone,fetat,finterface,fnom_affich,furlscript) VALUES ('mes_npds_" . $o . "','" . addslashes($ibid[1]) . "','9','Alerte','" . $fico . "','1','1','" . addslashes($ibid[2]) . "','data-bs-toggle=\"modal\" data-bs-target=\"#messageModal\");\n");
                
                $o++;
            }
        }

        // si message on compare avec la base
        if ($mess) {
            $fico = '';
            
            for ($i = 0; $i < count($mess); $i++) {
                $ibid = explode('|', $mess[$i]);
                $fico = $ibid[0] != 'Note' ? 'message_a' : 'message_i';
                
                //si on trouve le contenu du fichier dans la requete
                if (in_array($ibid[1], $f_mes, true)) {
                    $k = (array_search($ibid[1], $f_mes));
                    unset($f_mes[$k]);
                    
                    // = DB::table('')->select()->where('', )->orderBy('')->get();

                    $result = sql_query("SELECT fnom_affich FROM " . $NPDS_Prefix . "fonctions WHERE fnom='mes_npds_$i'");
                    
                    if (sql_num_rows($result) == 1) {
                        $alertinfo = sql_fetch_assoc($result);
                        
                        if ($alertinfo['fnom_affich'] != $ibid[2])

                            //DB::table('')->where('', )->update(array(
                            //    ''       => ,
                            //));

                            sql_query('UPDATE ' . $NPDS_Prefix . 'fonctions SET fdroits1_descr="", fnom_affich="' . addslashes($ibid[2]) . '" WHERE fnom="mes_npds_' . $i . '"');
                    }
                } else

                    DB::statement();

                    sql_query('REPLACE ' . $NPDS_Prefix . 'fonctions SET fnom="mes_npds_' . $i . '",fretour_h="' . $ibid[1] . '",fcategorie="9", fcategorie_nom="Alerte", ficone="' . $fico . '",fetat="1", finterface="1", fnom_affich="' . addslashes($ibid[2]) . '", furlscript="data-bs-toggle=\"modal\" data-bs-target=\"#messageModal\"",fdroits1_descr=""');
            }

            if (count($f_mes) !== 0) {
                foreach ($f_mes as $v) {
                    DB::table('fonctions')->where('fretour_h', $v)->where('fcategorie', 9)->delete();
                }
            }
        }
        //<== recuperation traitement des messages de NPDS
    }

    public static function () 
    {
        $adm_ent .= '
        <div id="adm_men_dial" class="border rounded px-2 py-2" >
            <div id="adm_men_alert" >
                <div id="alertes">
                ' . language::aff_langue($bloc_foncts_A) . '
                </div>
            </div>
        </div>
        <div id ="mes_perm" class="contenair-fluid text-muted" >
            <span class="car">' . Config::get('versioning.Version_Sub') . ' ' . Config::get('versioning.Version_Num') . ' ' . $aid . ' </span><span id="tempsconnection" class="car"></span>
        </div>
            <div class="modal fade" id="versusModal" tabindex="-1" aria-labelledby="versusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="versusModalLabel"><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" />' . adm_translate("Version") . ' NPDS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <p>Vous utilisez NPDS ' . Config::get('versioning.Version_Sub') . ' ' . Config::get('versioning.Version_Num') . '</p>';
            
        if (($versus_info[2] > Config::get('versioning.Version_Num'))) {          
        $adm_ent .= '<p>' . adm_translate("Une nouvelle version de NPDS est disponible !") . '</p>
                <p class="lead mt-3">' . $versus_info[1] . ' ' . $versus_info[2] . '</p>
                <p class="my-3">
                    <a class="me-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.zip" target="_blank" title="" data-bs-toggle="tooltip" data-bs-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.zip</a>
                    <a class="mx-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.tar.gz" target="_blank" title="" data-bs-toggle="tooltip" data-bs-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.tar.gz</a>
                </p></div>';

        }
    }

    public static function () 
    {
                $adm_ent .= '                
                
                <div class="modal-footer">
                </div>
            </div>
        </div>
        </div>
        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id=""><span id="messageModalIcon" class="me-2"></span><span id="messageModalLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <p id="messageModalContent"></p>
                <form class="mt-3" id="messageModalForm" action="" method="POST">
                    <input type="hidden" name="id" id="messageModalId" value="0" />
                    <button type="submit" class="btn btn btn-primary btn-sm">' . adm_translate("Confirmer la lecture") . '</button>
                </form>
                </div>
                <div class="modal-footer">
                <span class="small text-muted">Information de npds.org</span><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" />
                </div>
            </div>
        </div>
        </div>';
    }

}
