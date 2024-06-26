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
if (!stristr($_SERVER['PHP_SELF'], 'install.php')) die();

function etape_8()
{
    global $langue, $stage, $qi;
    $stage = 8;
    if (file_exists('modules/upload/config/upload.conf.php')) include_once('modules/upload/config/upload.conf.php');
    if ($racine != '') {
        $begin = strlen($racine);
        $end1 = strlen($rep_upload);
        $end2 = strlen($rep_cache);
        $end3 = strlen($rep_log);
        $rep_upload = substr($rep_upload, $begin, $end1);
        $rep_cache = substr($rep_cache, $begin, $end2);
        $rep_log = substr($rep_log, $begin, $end3);
    }
    echo '
                <h3 class="mb-3">' . __d('two_install', 'Configuration du module UPload') . '</h3>
                <div class="col-sm-12">
                    <form id="upload_module" name="upload_module" method="post" action="install.php">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="number" name="new_max_size" id="new_max_size" maxlength="20" value="' . $max_size . '" />
                            <label for="new_max_size">' . __d('two_install', 'Taille maxi des fichiers en octets') . '</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_DOCUMENTROOT" id="new_DOCUMENTROOT" maxlength="60" value="' . $DOCUMENTROOT . '" />
                            <label for="new_DOCUMENTROOT">' . __d('two_install', 'Chemin physique absolu d\'accès depuis la racine de votre site') . '</label>
                            <small class="mt-1">' . __d('two_install', 'Exemple par défaut ou SI vous ne savez pas') . ' : ==><br />' . __d('two_install', 'Exemples SI redirection') . ' : ==> /data/www/monsite OU c:\web\monsite</small>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="new_autorise_upload_p" name="new_autorise_upload_p">';
    if ($autorise_upload_p == "true") {
        $sel1 = 'selected="selected"';
        $sel2 = '';
    } else {
        $sel1 = '';
        $sel2 = 'selected="selected"';
    }
    echo '
                            <option value="true" ' . $sel1 . '>' . __d('two_install', 'Oui') . '</option>
                            <option value="false" ' . $sel2 . '>' . __d('two_install', 'Non') . '</option>
                            </select>
                            <label for="new_autorise_upload_p">' . __d('two_install', 'Autoriser l\'upload dans le répertoire personnel') . '</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_racine" id="new_racine" maxlength="60" value="' . $racine . '" />
                            <label for="new_racine">' . __d('two_install', 'Répertoire de votre site') . '</label>
                            <small class="mt-1">' . __d('two_install', 'Exemples :') . '<br />www.monsite.com/ ==> <br />www.monsite.com/npds/ ==> <span class="text-success">/npds</span><br />www.monsite.com/npds/npds/ ==> <span class="text-success">/npds/npds</span></small>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_rep_upload" id="new_rep_upload" maxlength="60" value="' . $rep_upload . '" />
                            <label for="new_rep_upload">' . __d('two_install', 'Répertoire de téléchargement') . '</label>
                            <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_rep_upload"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_rep_cache" id="new_rep_cache" maxlength="60" value="' . $rep_cache . '" />
                            <label for="new_rep_cache">' . __d('two_install', 'Répertoire des fichiers temporaires') . '</label>
                            <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_rep_cache"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_rep_log" id="new_rep_log" maxlength="60" value="' . $rep_log . '" />
                            <label for="new_rep_log">' . __d('two_install', 'Fichier journal de sécurité') . '</label>
                            <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_rep_log"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="url" name="new_url_upload" id="new_url_upload" maxlength="60" value="' . $url_upload . '" data-fv-uri___allow-local="true" />
                            <label for="new_url_upload">' . __d('two_install', 'URL HTTP de votre site') . '</label>
                            <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_url_upload"></div>
                            <span class="d-block text-help small w-100">' . __d('two_install', 'SI installation locale') . ' ==> http://127.0.0.1/</span>
                        </div>
                        <input type="hidden" name="langue" value="' . $langue . '" />
                        <input type="hidden" name="stage" value="' . $stage . '" />
                        <input type="hidden" name="qi" value="' . $qi . '" />
                        <input type="hidden" name="op" value="write_upload" />
                        <button type="submit" class="btn btn-success mb-3">' . __d('two_install', 'Valider') . '</button>
                    </form>
                </div>
                </div>';
    $arg1 = '
    var formulid = ["upload_module"]
    inpandfieldlen("new_racine",60);
    inpandfieldlen("new_rep_upload",60);
    inpandfieldlen("new_rep_cache",60);
    inpandfieldlen("new_rep_log",60);
    inpandfieldlen("new_url_upload",60);';
    formval('fv', '', $arg1, '1');
}
