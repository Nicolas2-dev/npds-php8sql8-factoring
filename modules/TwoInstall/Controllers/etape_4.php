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

function etape_4()
{
    global $langue, $stage, $qi, $stopngo, $stopphp, $dbhost, $dbuname, $dbname, $adminmail;
    $stage = 4;
    settype($dbpass, 'string');
    settype($NPDS_Prefix, 'string');

    include_once('config/config.php');
    global $system, $mysql_p, $system_md5;
    echo '
                <h3 class="mb-2">' . __d('two_install', 'Paramètres de connexion') . '</h3>
                <div class="col-sm-12">
                    <form id="parameters" name="parameters" method="post" action="install.php">
                        <div class="d-flex justify-content-start w-100 small text-help py-1"><div> ' . __d('two_install', 'Exemples :') . ' ==> sql.domaine.com ==> localhost</div><div class="ms-auto" id="countcar_new_dbhost"></div></div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_dbhost" id="new_dbhost" maxlength="80" value="' . $dbhost . '" required="required" />
                            <label for="new_dbhost">' . __d('two_install', 'Nom d\'hôte du serveur mySQL') . '</label>
                        </div>
                        <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_dbuname"></div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_dbuname" id="new_dbuname" maxlength="80" value="' . $dbuname . '" required="required" />
                            <label for="new_dbuname">' . __d('two_install', 'Nom d\'utilisateur (identifiant)') . '</label>
                        </div>
                        <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_dbpass"></div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="password" name="new_dbpass" id="new_dbpass" maxlength="80" required="required" />
                            <label for="new_dbpass">' . __d('two_install', 'Mot de passe') . '</label>
                        </div>
                        <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_dbname"></div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_dbname" id="new_dbname" maxlength="80" required="required" />
                            <label for="new_dbname">' . __d('two_install', 'Nom de la base de données') . '</label>
                        </div>
                        <div class="d-flex justify-content-start w-100 small text-help py-1"><div> ' . __d('two_install', 'Pour éviter les conflits de nom de table sql...') . '</div><div class="ms-auto" id="countcar_new_NPDS_Prefix"></div></div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="new_NPDS_Prefix" id="new_NPDS_Prefix" maxlength="10" value="' . $NPDS_Prefix . '" />
                            <label for="new_NPDS_Prefix">' . __d('two_install', 'Préfixe des tables sql') . '</label>
                        </div>';
    $sel1 = '';
    $sel2 = '';
    if ($mysql_p == 0) {
        $sel1 = 'selected="selected"';
        $sel2 = '';
    } else {
        $sel1 = '';
        $sel2 = 'selected="selected"';
    }
    echo '
                        <div class="form-floating mb-3">
                            <select class="form-select" name="new_mysql_p">
                            <option value="0" ' . $sel1 . '>' . __d('two_install', 'Non permanente') . '</option>
                            <option value="1" ' . $sel2 . '>' . __d('two_install', 'Permanente') . '</option>
                            </select>
                            <label for="new_mysql_p">' . __d('two_install', 'Type de connexion au serveur mySQL') . '</label>
                        </div>
                        <div class="d-flex justify-content-end w-100 small text-help py-1" id="countcar_new_adminmail"></div>
                        <div class="form-floating mb-3">
                            <input class="form-control" type="email" name="new_adminmail" id="new_adminmail"  maxlength="60" value="' . $adminmail . '" required="required" />
                            <label for="new_adminmail">' . __d('two_install', 'Adresse e-mail de l\'administrateur') . '</label>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" name="langue" value="' . $langue . '" />
                            <input type="hidden" name="stage" value="' . $stage . '" />
                            <input type="hidden" name="op" value="write_parameters" />
                            <input type="hidden" name="qi" value="' . $qi . '" />
                            <button type="submit" class="btn btn-success">' . __d('two_install', 'Modifier') . '</button>
                        </div>
                    </form>
                </div>
                </div>';
    $arg1 = '
    var formulid = ["parameters"]
    inpandfieldlen("new_dbhost",80);
    inpandfieldlen("new_dbuname",80);
    inpandfieldlen("new_dbpass",80);
    inpandfieldlen("new_dbname",80);
    inpandfieldlen("new_NPDS_Prefix",10);
    inpandfieldlen("new_adminmail",60);';
    formval('fv', '', $arg1, '1');
}
