<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
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

include('config/config.php');
$pre_tab = '';
if ($NPDS_Prefix != '')
    $pre_tab = __d('two_install', ' Tables préfixées avec : ') . '<code class="code">' . $NPDS_Prefix . ' </code>.';

function etape_6()
{
    global $list_tab, $langue, $stage, $qi, $dbhost, $dbname, $dbuname, $dbpass, $NPDS_Prefix, $pre_tab;
    $stage = 6;
    echo '
                <h3 class="mb-3">' . __d('two_install', 'Base de données') . '</h3>
                    <p id="mess_bd">' . __d('two_install', 'Nous allons maintenant procéder à la création des tables de la base de données ') . ' (&nbsp;<code class="code">' . $dbname . '</code>&nbsp;) ' . __d('two_install', 'sur le serveur d\'hébergement') . ' (&nbsp;<code class="code">' . $dbhost . '</code>&nbsp;). ' . $pre_tab . '<br />' . __d('two_install', 'Si votre base de données comporte déjà des tables, veuillez en faire une sauvegarde avant de poursuivre !') . '<br /></p>
                    <form name="database" method="post" action="install.php">
                        <input type="hidden" name="langue" value="' . $langue . '" />
                        <input type="hidden" name="stage" value="' . $stage . '" />
                        <input type="hidden" name="op" value="write_database" />
                        <input type="hidden" name="qi" value="' . $qi . '" />
                        <button type="submit" class="btn btn-success">' . __d('two_install', 'Créer') . '</button>
                    </form>
                </div>';
}
