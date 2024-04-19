<?php

declare(strict_types=1);

namespace npds\system\assets;


class alertcore
{




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
