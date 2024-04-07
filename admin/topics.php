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

use npds\system\assets\js;
use npds\system\logs\logs;
use npds\system\assets\css;
use npds\system\support\str;
use npds\system\config\Config;
use npds\system\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'topicsmanager';
$f_titre = adm_translate("Gestion des sujets");

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [topicsmanager description]
 *
 * @return  void
 */
function topicsmanager(): void 
{
    global $f_meta_nom, $f_titre, $nook;

    include("themes/default/header.php");

    GraphicAdmin(manuel('topics'));
    adminhead($f_meta_nom, $f_titre);

    $result = DB::table('topics')->select('topicid', 'topicname', 'topicimage', 'topictext')->orderBy('topicname')->get();

    settype($topicadmin, 'string');

    if ($result > 0) {
        echo '
        <hr />
        <h3 class="my-3">' . adm_translate("Sujets actifs") . '<span class="badge bg-secondary float-end">' . sql_num_rows($result) . '</span></h3>';
        
        foreach($result as $topic) {
            echo '
            <div class="card card-body mb-2" id="top_' . $topic['topicid'] . '">
                <div class=" topi">
                    <div class="">';

            if (($topic['topicimage']) or ($topic['topicimage'] != '')) {
                echo '<a href="admin.php?op=topicedit&amp;topicid=' . $topic['topicid'] . '"><img class="img-thumbnail" style="height:80px;  max-width:120px" src="' . Config::get('npds.tipath') . $topicimage . '" data-bs-toggle="tooltip" title="ID : ' . $topicid . '" alt="' . $topic['topicname'] . '" /></a>';
            } else {
                echo '<a href="admin.php?op=topicedit&amp;topicid=' . $topic['topicid'] . '"><img class="img-thumbnail" style="height:80px;  max-width:120px" src="' . Config::get('npds.tipath') . 'topics.png" data-bs-toggle="tooltip" title="ID : ' . $topicid . '" alt="' . $topic['topicname'] . '" /></a>';
            }

            echo '
                    </div>
                    <div class="">
                        <h4 class="my-3"><a href="admin.php?op=topicedit&amp;topicid=' . $topic['topicid'] . '" ><i class="fa fa-edit me-1 align-middle"></i>' . language::aff_langue($topic['topicname']) . '</a></h4>
                        <p>' . language::aff_langue($topic['topictext']) . '</p>
                        <div id="shortcut-tools_' . $topic['topicid'] . '" class="n-shortcut-tools" style="display:none;"><a class="text-danger btn" href="admin.php?op=topicdelete&amp;topicid=' . $topicid . '&amp;ok=0" ><i class="fas fa-trash fa-2x"></i></a></div>
                    </div>
                </div>
            </div>';
        }
    }

    echo '
    <hr />
    <a name="addtopic"></a>';

    if (isset($nook)) {
        echo '<div class="alert alert-danger alert-dismissible fade show">Le nom de ce sujet existe déjà ! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    echo '
    <h3 class="my-4">' . adm_translate("Ajouter un nouveau Sujet") . '</h3>
    <form action="admin.php" method="post" id="topicmake">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="topicname">' . adm_translate("Intitulé") . '</label>
            <div class="col-sm-8">
                <input id="topicname" class="form-control" type="text" name="topicname" maxlength="20" value="' . $topic['topicname'] . '" placeholder="' . adm_translate("cesiteestgénial") . '" required="required" />
                <span class="help-block">' . adm_translate("(un simple nom sans espaces)") . ' - ' . adm_translate("max caractères") . ' : <span id="countcar_topicname"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="topictext">' . adm_translate("Texte") . '</label>
            <div class="col-sm-8">
                <textarea id="topictext" class="form-control" rows="3" name="topictext" maxlength="250" placeholder="' . adm_translate("ce site est génial") . '" required="required" >' . $topictext . '</textarea>
                <span class="help-block">' . adm_translate("(description ou nom complet du sujet)") . ' - ' . adm_translate("max caractères") . ' : <span id="countcar_topictext"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="topicimage">' . adm_translate("Image") . '</label>
            <div class="col-sm-8">
                <input id="topicimage" class="form-control" type="text" name="topicimage" maxlength="20" value="' . $topic['topicimage'] . '" placeholder="genial.png" />
                <span class="help-block">' . adm_translate("(nom de l'image + extension)") . ' (' . Config::get('npds.tipath') . '). - ' . adm_translate("max caractères") . ' : <span id="countcar_topicimage"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="topicadmin">' . adm_translate("Administrateur(s)") . '</label>
            <div class="col-sm-8">
                <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user-cog fa-lg"></i></span>
                <input class="form-control" type="text" id="topicadmin" name="topicadmin" maxlength="255" value="' . $topicadmin . '" required="required" />
                </div>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <input type="hidden" name="op" value="topicmake" />
                <button class="btn btn-primary" type="submit" ><i class="fa fa-plus-square fa-lg me-2"></i>' . adm_translate("Ajouter un Sujet") . '</button>
            </div>
        </div>
    </form>';

    echo '
    <script type="text/javascript">
        //<![CDATA[
            var topid="";
            $(".topi").hover(function(){
                topid = $(this).parent().attr("id");
                topid=topid.substr (topid.search(/\d/))
                $button=$("#shortcut-tools_"+topid);
                $button.show();
            }, function(){
            $button.hide();
            });
        //]]>
    </script>';

    // le validateur pour topicadmin ne fonctionne pas ?!!
    $fv_parametres = '
    topicadmin: {
        validators: {
            callback: {
                message: "Please choose an administrator FROM the provided list.",
                callback: function(value, validator, $field) {
                diff="";
                var value = $field.val();
                            console.log(value);//

                if (value === "") {return true;}
                function split( n ) {
                return n.split( /,\s*/ );
                }
                diff = $(split(value)).not(admin).get();
                console.log(diff);
                if (diff!="") {return false;}
                return true;
                }
            }
        }
    },

    topicname: {
        validators: {
            regexp: {
                regexp: /^\w+$/i,
                message: "' . adm_translate("Doit être un mot sans espace.") . '"
            }
        }
    },

    topicimage: {
        validators: {
            regexp: {
                regexp: /^[\w]+\\.(jpg|jpeg|png|gif)$/,
                message: "' . adm_translate("Doit être un nom de fichier valide avec une de ces extensions : jpg, jpeg, png, gif.") . '"
            }
        }
    },';

    $arg1 = '
    var formulid = ["topicmake"];
    inpandfieldlen("topicname",20);
    inpandfieldlen("topictext",250);
    inpandfieldlen("topicimage",20);
    inpandfieldlen("topicadmin",255);';

    echo js::auto_complete_multi('admin', 'aid', 'authors', 'topicadmin', '');

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [topicedit description]
 *
 * @param   int   $topicid  [$topicid description]
 *
 * @return  void
 */
function topicedit(int $topicid): void 
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('topics'));
    adminhead($f_meta_nom, $f_titre);

    $topic  = DB::table('topics')->select('topicid', 'topicname', 'topicimage', 'topictext', 'topicadmin')->where('topicid', $topicid)->first();

    echo '
    <hr />
    <h3 class="mb-3">' . adm_translate("Editer le Sujet :") . ' <span class="text-muted">' . language::aff_langue($topic['topicname']) . '</span></h3>';

    if ($topic['topicimage'] != '') {
        echo '
    <div class="card card-body my-4 py-3"><img class="img-fluid mx-auto d-block" src="' . Config::get('npds.tipath') . $topic['topicimage'] . '" alt="image-sujet" /></div>';
    }

    echo '
    <form action="admin.php" method="post" id="topicchange">
        <fieldset>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topicname">' . adm_translate("Intitulé") . '</label>
                <div class="col-sm-8">
                <input id="topicname" class="form-control" type="text" name="topicname" maxlength="20" value="' . $topic['topicname'] . '" placeholder="' . adm_translate("cesiteestgénial") . '" required="required" />
                <span class="help-block">' . adm_translate("(un simple nom sans espaces)") . ' - ' . adm_translate("max caractères") . ' : <span id="countcar_topicname"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topictext">' . adm_translate("Texte") . '</label>
                <div class="col-sm-8">
                <textarea id="topictext" class="form-control" rows="3" name="topictext" maxlength="250" placeholder="' . adm_translate("ce site est génial") . '" required="required">' . $topic['topictext'] . '</textarea>
                <span class="help-block">' . adm_translate("(description ou nom complet du sujet)") . ' - ' . adm_translate("max caractères") . ' : <span id="countcar_topictext"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topicimage">' . adm_translate("Image") . '</label>
                <div class="col-sm-8">
                <input id="topicimage" class="form-control" type="text" name="topicimage" maxlength="20" value="' . $topic['topicimage'] . '" placeholder="genial.png" />
                <span class="help-block">' . adm_translate("(nom de l'image + extension)") . ' (' . Config::get('npds.tipath') . '). - ' . adm_translate("max caractères") . ' : <span id="countcar_topicimage"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topicadmin">' . adm_translate("Administrateur(s) du sujet") . '</label>
                <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user-cog fa-lg"></i></span>
                    <input class="form-control" type="text" id="topicadmin" name="topicadmin" maxlength="255" value="' . $topic['topicadmin'] . '" />
                </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
        <hr />
        <h4 class="my-3">' . adm_translate("Ajouter des Liens relatifs au Sujet") . '</h4>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="name">' . adm_translate("Nom du site") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="name" id="name" maxlength="30" />
                <span class="help-block">' . adm_translate("max caractères") . ' : <span id="countcar_name"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="url">' . adm_translate("URL") . '</label>
            <div class="col-sm-8">
                <input class="form-control" type="url" name="url" id="url" maxlength="320" placeholder="http://www.valideurl.org" />
                <span class="help-block">' . adm_translate("max caractères") . ' : <span id="countcar_url"></span></span>
            </div>
        </div>
        </fieldset>
        <div class="mb-3 row">
            <input type="hidden" name="'.$topic['topicid'].'" value="' . $topic['topicid'] . '" />
            <input type="hidden" name="op" value="topicchange" />
            <div class="col-sm-8 ms-sm-auto">
                <button class="btn btn-primary" type="submit">' . adm_translate("Sauver les modifications") . '</button>
                <button class="btn btn-secondary" onclick="javascript:document.location.href=\'admin.php?op=topicsmanager\'">' . adm_translate("Retour en arrière") . '</button>
            </div>
        </div>
    </form>';
    /*
    <form id="fad_deltop" action="admin.php" method="post">
        <input type="hidden" name="topic['topicid']" value="'.$topic['topicid'].'" />
        <input type="hidden" name="op" value="topicdelete" />
    </form>
    <button class="btn btn-danger"><i class="fas fa-trash fa-lg"></i>&nbsp;&nbsp;'.adm_translate("Effacer le Sujet !").'</button>
    */

    echo '
        <hr />
        <h3 class="my-2">' . adm_translate("Gérer les Liens Relatifs : ") . ' <span class="text-muted">' . language::aff_langue($topic['topicname']) . '</span></h3>';

    $r_related = DB::table('related')->select('rid', 'name', 'url')->where('tid', $topic['topicid'])->first();

    echo '
    <table id="tad_linkrel" data-toggle="table" data-striped="true" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <th data-sortable="true" data-halign="center">' . adm_translate('Nom') . '</th>
            <th data-sortable="true" data-halign="center">' . adm_translate('Url') . '</th>
            <th class="n-t-col-xs-2" data-halign="center" data-align="right">' . adm_translate('Fonctions') . '</th>
        </thead>
        <tbody>';

    while (list($rid, $name, $url) = sql_fetch_row($res)) {
    foreach ($r_relatad as $related)
        echo '
                <tr>
                    <td>' . $related['name'] . '</td>
                    <td><a href="' . $related['url'] . '" target="_blank">' . $related['url'] . '</a></td>
                    <td>
                    <a href="admin.php?op=relatededit&amp;tid=' . $topic['topicid'] . '&amp;rid=' . $related['rid'] . '" ><i class="fas fa-edit fa-lg" data-bs-toggle="tooltip" title="' . adm_translate("Editer") . '"></i></a>&nbsp;
                    <a href="' . $related['url'] . '" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a>&nbsp;
                    <a href="admin.php?op=relateddelete&amp;tid=' . $topic['topicid'] . '&amp;rid=' . $related['rid'] . '" ><i class="fas fa-trash fa-lg text-danger" data-bs-toggle="tooltip" title="' . adm_translate("Effacer") . '"></i></a>
                    </td>
                </tr>';
    }

    echo '
            </tbody>
        </table>';

    $fv_parametres = '
    topicadmin: {
        validators: {
            callback: {
                message: "Please choose an administrator from the provided list.",
                callback: function(value, validator, $field) {
                diff="";
                var value = $field.val();
                if (value === "") {return true;}
                function split( n ) {
                    return n.split( /,\s*/ );
                }
                diff = $(split(value)).not(admin).get();
                console.log(diff);
                if (diff!="") {return false;}
                return true;
                }
            }
        }
    },
    topicimage: {
        validators: {
            regexp: {
                regexp: /^[\w]+\\.(jpg|jpeg|png|gif)$/,
                message: "This must be a valid file name with one of this extension jpg, jpeg, png, gif."
            }
        }
    },
    topicname: {
        validators: {
            regexp: {
                regexp: /^\w+$/i,
                message: "This must be a simple word without space."
            }
        }
    },';

    $arg1 = '
    var formulid = ["topicchange"];
    inpandfieldlen("topicname",20);
    inpandfieldlen("topictext",250);
    inpandfieldlen("topicimage",20);
    inpandfieldlen("name",30);
    inpandfieldlen("url",320);
    ';

    echo js::auto_complete_multi('admin', 'aid', 'authors', 'topicadmin', '');

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [relatededit description]
 *
 * @param   int   $tid  [$tid description]
 * @param   int   $rid  [$rid description]
 *
 * @return  void
 */
function relatededit(int $tid, int $rid): void 
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('topics'));
    adminhead($f_meta_nom, $f_titre);

    $related = DB::table('related')->select('name', 'url')->where('rid', $rid)->first();

    $topic = DB::table('topics')->select('topictext', 'topicimage')->where('topicid', $tid)->first();

    echo '
    <hr />
    <h3>' . adm_translate("Sujet : ") . ' ' . $topic['topictext'] . '</h3>
    <h4>' . adm_translate("Editer les Liens Relatifs") . '</h4>';

    if ($topic['topicimage'] != "") {  
        echo '
    <div class="thumbnail">
        <img class="img-fluid " src="' . Config::get('npds.tipath') . $topic['topicimage'] . '" alt="' . $topic['topictext'] . '" />
    </div>';
    }

    echo '
    <form class="form-horizontal" action="admin.php" method="post" id="editrelatedlink">
        <fieldset>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="name">' . adm_translate("Nom du site") . '</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="name" id="name" value="' . $related['name'] . '" maxlength="30" required="required" />
                <span class="help-block text-end"><span id="countcar_name"></span></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="url">' . adm_translate("URL") . '</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-text">
                        <a href="' . $related['url'] . '" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a>
                    </span>
                    <input type="url" class="form-control" name="url" id="url" value="' . $related['url'] . '" maxlength="320" />
                </div>
                <span class="help-block text-end"><span id="countcar_url"></span></span>
                </div>
                <input type="hidden" name="op" value="relatedsave" />
                <input type="hidden" name="tid" value="' . $tid . '" />
                <input type="hidden" name="rid" value="' . $rid . '" />
            </fieldset>
        <div class="mb-3 row">
            <div class="col-sm-8 ms-sm-auto">
                <button class="btn btn-primary col-12" type="submit">' . adm_translate("Sauver les modifications") . '</button>
            </div>
        </div>
    </form>';

    $arg1 = '
        var formulid = ["editrelatedlink"];
        inpandfieldlen("name",30);
        inpandfieldlen("url",320);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [relatedsave description]
 *
 * @param   int     $tid   [$tid description]
 * @param   int     $rid   [$rid description]
 * @param   string  $name  [$name description]
 * @param   string  $url   [$url description]
 *
 * @return  void
 */
function relatedsave(int $tid, int $rid, string $name, string $url): void
{
    DB::table('related')->where('rid', $rid)->update(array(
        'name'      => $name,
        'url'       => $url,
    ));

    Header("Location: admin.php?op=topicedit&topicid=$tid");
}

/**
 * [relateddelete description]
 *
 * @param   int   $tid  [$tid description]
 * @param   int   $rid  [$rid description]
 *
 * @return  void
 */
function relateddelete(int $tid, int $rid): void 
{
    DB::table('related')->where('rid', $rid)->delete();

    Header("Location: admin.php?op=topicedit&topicid=$tid");
}

/**
 * [topicmake description]
 *
 * @param   string  $topicname   [$topicname description]
 * @param   string  $topicimage  [$topicimage description]
 * @param   string  $topictext   [$topictext description]
 * @param   string  $topicadmin  [$topicadmin description]
 *
 * @return  void
 */
function topicmake(string $topicname, string $topicimage, string $topictext, string $topicadmin): void
{
    $topicname = stripslashes(str::FixQuotes($topicname));

    $istopicname = DB::table('topics')->select('*')->where('topicname', $topicname)->first();

    if ($istopicname !== 0) {
        Header("Location: admin.php?op=topicsmanager&nook=nook#addtopic");
        die();
    }

    $topicimage = stripslashes(str::FixQuotes($topicimage));
    $topictext = stripslashes(str::FixQuotes($topictext));

    DB::table('topics')->insert(array(
        'topicname'         => $topicname,
        'topicimage'        => $topicimage,
        'topictext'         => $topictext,
        'counter'           => 0,
        'topicadmin	'       => $topicadmin,
    ));

    global $aid;
    logs::Ecr_Log("security", "topicMake ($topicname) by AID : $aid", "");

    $topicadminX = explode(",", $topicadmin);
    array_pop($topicadminX);

    for ($i = 0; $i < count($topicadminX); $i++) {
        trim($topicadminX[$i]);

        $nres = DB::table('droits')->select('*')->where('d_aut_aid', $topicadminX[$i])->where('d_droits', 11112)->get();

        if ($nres == 0) {
            DB::table('droits')->insert(array(
                'd_aut_aid'       => $topicadminX[$i],
                'd_fon_fid'       => 2,
                'd_droits'       => 11112,
            ));

        }
    }

    Header("Location: admin.php?op=topicsmanager#addtopic");
}

/**
 * [topicchange description]
 *
 * @param   int     $topicid     [$topicid description]
 * @param   string  $topicname   [$topicname description]
 * @param   string  $topicimage  [$topicimage description]
 * @param   string  $topictext   [$topictext description]
 * @param   string  $topicadmin  [$topicadmin description]
 * @param   string  $name        [$name description]
 * @param   string  $url         [$url description]
 *
 * @return  void
 */
function topicchange(int $topicid, string $topicname, string $topicimage, string $topictext, string $topicadmin, string $name, string $url): void
{
    $topicadminX = explode(',', $topicadmin);
    array_pop($topicadminX);

    $res = DB::table('droits')->select('*')->where('d_droits', 11112)->where('d_fon_fid', 2)->get();

    $d = array();
    $topad = array();

    foreach ($res as $d) {
        $topad[] = $d['d_aut_aid'];
    }

    foreach ($topicadminX as $value) {
        if (!in_array($value, $topad)) {
            DB::table('')->insert(array(
                'd_aut_aid'      => $value,
                'd_fon_fid'      => 2,
                'd_droits'       => 11112,
            ));
        }
    }

    foreach ($topad as $value) { //pour chaque droit adminsujet on regarde le nom de l'adminsujet
        if (!in_array($value, $topicadminX)) { //si le nom de l'adminsujet n'est pas dans les nouveaux adminsujet
            //on cherche si il administre un autre sujet
            // $resu =  mysqli_get_client_info() <= '8.0' 
            //     ? DB::table('topics')->select('*')->where('topicadmin', 'REGEXP', '[[:<:]]" . $value . "[[:>:]]')->first()

            //     : DB::table('topics')->select('*')->where('topicadmin', 'REGEXP', '\\b" . $value . "\\b')->first();

            $resu = DB::table('topics')->select('*')->where('topicadmin', 'REGEXP', '\\b" . $value . "\\b')->first();
            
            if (($resu == 1) and ($topicid == $resu['tid'])) {
                DB::table('droits')->where('d_aut_aid', $value)->where('d_droits', 11112)->wxhere('d_fon_fid', 2)->delete();
            }
        }
    }

    $topicname = stripslashes(str::FixQuotes($topicname));
    $topicimage = stripslashes(str::FixQuotes($topicimage));
    $topictext = stripslashes(str::FixQuotes($topictext));
    $name = stripslashes(str::FixQuotes($name));
    $url = stripslashes(str::FixQuotes($url));

    DB::table('topics')->where('topicid', $topicid)->update(array(
        'topicname'       => $topicname,
        'topicimage'      => $topicimage,
        'topictext'       => $topictext,
        'topicadmin'      => $topicadmin,
    ));

    global $aid;
    logs::Ecr_Log("security", "topicChange ($topicname, $topicid) by AID : $aid", "");
    
    if ($name) {
        DB::table('related')->insert(array(
            'tid'       => $topicid,
            'name'      => $name,
            'url'       => $url,
        ));
    }

    Header("Location: admin.php?op=topicedit&topicid=$topicid");
}

/**
 * [topicdelete description]
 *
 * @param   int   $topicid  [$topicid description]
 * @param   int   $ok       [$ok description]
 *
 * @return  void
 */
function topicdelete(int $topicid, int $ok = 0): void
{
    if ($ok == 1) {
        global $aid;

        // pourquoi  cette requete not used res'[sid']
        //$res = DB::table('stories')->select('sid')->where('topic', $topicid)->first();

        DB::table('stories')->where('topic', $topicid)->delete();

        logs::Ecr_Log("security", "topicDelete (stories, $topicid) by AID : $aid", "");

        DB::table('topics')->where('topicid', $topicid)->delete();

        logs::Ecr_Log("security", "topicDelete (topic, $topicid) by AID : $aid", "");

        DB::table('related')->where('tid', $topicid)->delete();

        logs::Ecr_Log("security", "topicDelete (related, $topicid) by AID : $aid", '');

        // commentaires
        if (file_exists("modules/comments/config/article.conf.php")) {
            include("modules/comments/config/article.conf.php");
            
            DB::table('posts')->where('forum_id', $forum)->where('topic_id', $topic)->delete();

            logs::Ecr_Log("security", "topicDelete (comments, $topicid) by AID : $aid", "");
        }

        Header("Location: admin.php?op=topicsmanager");
    } else {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('topics'));
        adminhead($f_meta_nom, $f_titre);

        $topic = DB::table('topics')->select('topicimage', 'topicname', 'topictext')->where('topicid', $topicid)->first();

        echo '<h3 class=""><span class="text-danger">' . adm_translate("Effacer le Sujet") . ' : </span>' . language::aff_langue($topicname) . '</h3>';
        echo '<div class="alert alert-danger lead" role="alert">';

        if ($topic['topicimage'] != "") {
            echo '
            <div class="thumbnail">
                <img class="img-fluid" src="' . Config::get('npds.tipath') . $topic['topicimage'] . '" alt="logo-topic" />
            </div>';
        }

        echo '
            <p>' . adm_translate("Etes-vous sûr de vouloir effacer ce sujet ?") . ' : ' . $topic['topicname'] . '</p>
            <p>' . adm_translate("Ceci effacera tous ses articles et ses commentaires !") . '</p>
            <p><a class="btn btn-danger" href="admin.php?op=topicdelete&amp;topicid=' . $topicid . '&amp;ok=1">' . adm_translate("Oui") . '</a>&nbsp;<a class="btn btn-primary"href="admin.php?op=topicsmanager">' . adm_translate("Non") . '</a></p>
        </div>';

        css::adminfoot('', '', '', '');
    }
}

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
