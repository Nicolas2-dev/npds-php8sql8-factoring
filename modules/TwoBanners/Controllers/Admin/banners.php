<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2022 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\assets\css;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\DB;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'BannersAdmin';
$f_titre = __d('two_banners', 'Administration des bannières');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [BannersAdmin description]
 *
 * @return void
 */
function BannersAdmin(): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('banners'));
    adminhead($f_meta_nom, $f_titre);

    echo '
    <hr />
    <h3>'. __d('two_banners', 'Bannières actives') .'</h3>
    <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-toggle="true" data-show-columns="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'ID') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="center">
                    '. __d('two_banners', 'Nom de l\'annonceur') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Impressions') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Imp. restantes') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Clics') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    % '. __d('two_banners', 'Clics') .'
                </th>
                <th data-halign="center" data-align="center">
                    '. __d('two_banners', 'Fonctions') .'
                </th>
            </tr>
        </thead>
        <tbody>';

    $banners = DB::table('banner')->select('id', 'cid', 'imageurl', 'imptotal', 'impmade', 'clicks', 'date')->where('userlevel', '!=', 9)->orderBy('id')->get();

    foreach($banners as $banner) {
        
        $client = DB::table('bannerclient')->select('name')->first($banner['cid']);

        $float = (string) (100 * $banner['clicks'] / $banner['impmade']);

        $percent = (($banner['impmade'] == 0) ? '0' : substr($float , 0, 5));
        $left = (($banner['imptotal'] == 0) ? __d('two_banners', 'Illimité') : $banner['imptotal'] - $banner['impmade']);

        echo '
            <tr>
                <td>
                    '. $banner['id'] .'
                </td>
                <td>
                    '. $client['name'] .'
                </td>
                <td>
                    '. $banner['impmade'] .'
                </td>
                <td>
                    '. $left .'
                </td>
                <td>
                    '. $banner['clicks'] .'
                </td>
                <td>
                    '. $percent .'%
                </td>
                <td>
                    <a href="'. site_url('admin.php?op=BannerEdit&amp;bid='. $banner['id']) .'">
                        <i class="fa fa-edit fa-lg me-3" title="'. __d('two_banners', 'Editer') .'" data-bs-toggle="tooltip"></i>
                    </a>
                    <a href="'. site_url('admin.php?op=BannerDelete&amp;bid='. $banner['id'] .'&amp;ok=0') .'" class="text-danger">
                        <i class="fas fa-trash fa-lg" title="'. __d('two_banners', 'Effacer') .'" data-bs-toggle="tooltip"></i>
                    </a>
                </td>
            </tr>';
    }

    echo '
        </tbody>
    </table>';

    echo '
    <hr />
    <h3>'. __d('two_banners', 'Bannières inactives') .'</h3>
    <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-toggle="true" data-show-columns="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'ID') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Impressions') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Imp. restantes') .'
                </th>
                <th class="n-t-col-xs-2" data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Clics') .'
                </th>
                <th class="n-t-col-xs-2" data-sortable="true" data-halign="center" data-align="right">
                    % '. __d('two_banners', 'Clics') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Nom de l\'annonceur') .'
                </th>
                <th class="n-t-col-xs-1" data-halign="center" data-align="center">
                    '. __d('two_banners', 'Fonctions') .'
                </th>
            </tr>
        </thead>
        <tbody>';

    $banners = DB::table('banner')->select('id', 'cid', 'imageurl', 'imptotal', 'impmade', 'clicks', 'date')->where('userlevel', 9)->orderBy('id')->get();

    foreach($banners as $banner) {    

        $client = DB::table('bannerclient')->select('name')->first($banner['cid']);
        
        $float = (100 * $banner['clicks'] / $banner['impmade']);

        $percent = (($banner['impmade'] == 0) ? '0' : substr( (string) $float, 0, 5));
        $left = (($banner['imptotal'] == 0) ? __d('two_banners', 'Illimité') : $banner['imptotal'] - $banner['impmade']);
        
        echo '
            <tr>
                <td>
                    '. $banner['id'] .'
                </td>
                <td>
                    '. $banner['impmade'] .'
                </td>
                <td>
                    '. $left .'</td>
                <td>
                    '. $banner['clicks'] .'
                </td>
                <td>
                    '. $percent .'%
                </td>
                <td>
                    '. $clien['name'] .' | <span class="small">'. basename(language::aff_langue($imageurl)) .'</span>
                </td>
                <td>
                    <a href="'. site_url('admin.php?op=BannerEdit&amp;bid='. $banner['id']) .'" >
                        <i class="fa fa-edit fa-lg me-3" title="'. __d('two_banners', 'Editer') .'" data-bs-toggle="tooltip"></i>
                    </a>
                    <a href="'. site_url('admin.php?op=BannerDelete&amp;bid='. $banner['id'] .'&amp;ok=0') .'" class="text-danger">
                        <i class="fas fa-trash fa-lg" title="'. __d('two_banners', 'Effacer') .'" data-bs-toggle="tooltip"></i>
                    </a>
                </td>
            </tr>';
    }

    echo '
        </tbody>
    </table>
    <hr />
    <h3>'. __d('two_banners', 'Bannières terminées') .'</h3>
    <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-toggle="true" data-show-columns="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'ID') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Imp.') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Clics') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    % '. __d('two_banners', 'Clics') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="center">
                    '. __d('two_banners', 'Date de début') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="center">
                    '. __d('two_banners', 'Date de fin') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="center">
                    '. __d('two_banners', 'Nom de l\'annonceur') .'
                </th>
                <th data-halign="center" data-align="center">
                    '. __d('two_banners', 'Fonctions') .'
                </th>
            </tr>
        </thead>
        <tbody>';

    $bannerfinish = DB::table('bannerfinish')->select('id', 'cid', 'impressions', 'clicks', 'datestart', 'dateend')->orderBy('id')->get();

    foreach($bannerfinish as $finish) {        

        $client = DB::table('bannerclient')->select('name')->first($finish['cid']);
        
        if ($finish['impressions'] == 0) {
            $finish['impressions'] = 1;
        }
        
        $float = (100 * $finish['clicks'] / $finish['impressions']);
        $percent = substr( (string) $float, 0, 5);
        
        echo '
            <tr>
                <td>
                    '. $finish['id'] .'
                </td>
                <td>
                    '. $finish['impressions'] .'
                </td>
                <td>
                    '. $finish['clicks'] .'
                </td>
                <td>
                    '. $percent .'%
                </td>
                <td>
                    '. $finish['datestart'] .'
                </td>
                <td>
                    '. $finish['dateend'] .'
                </td>
                <td>
                    '. $client['name'] .'
                </td>
                <td>
                    <a href="'. site_url('admin.php?op=BannerFinishDelete&amp;bid='. $finish['id']) .'" class="text-danger">
                        <i class="fas fa-trash fa-lg" title="'. __d('two_banners', 'Effacer') .'" data-bs-toggle="tooltip"></i>
                    </a>
                </td>
            </tr>';
    }

    echo '
        </tbody>
    </table>
    <hr />
    <h3>'. __d('two_banners', 'Annonceurs faisant de la publicité') .'</h3>
    <table id="tad_banannon" data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-toggle="true" data-show-columns="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
        <thead>
            <tr>
                <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'ID') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="center">
                    '. __d('two_banners', 'Nom de l\'annonceur') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="right">
                    '. __d('two_banners', 'Bannières actives') .'
                </th>
                <th data-sortable="true" data-halign="center" data-align="center">
                    '. __d('two_banners', 'Nom du Contact') .'
                </th>
                <th data-sortable="true" data-halign="center">
                    '. __d('two_banners', 'E-mail') .'
                </th>
                <th data-halign="center" data-align="right">
                    '. __d('two_banners', 'Fonctions') .'
                </th>
            </tr>
        </thead>
        <tbody>';
   
    $bannerclient = DB::table('bannerclient')->select('id', 'name', 'contact', 'email')->orderBy('id')->get();

    foreach($bannerclient as $client) {         

        $count = DB::table('banner')->where('id', $client['id'])->count();

        echo '
            <tr>
                <td>
                    '. $client['id'] .'
                </td>
                <td>
                    '. $client['name'] .'
                </td>
                <td>
                    '. $count .'
                </td>
                <td>
                    '. $client['contact'] .'
                </td>
                <td>
                    '. $client['email'] .'
                </td>
                <td>
                    <a href="'. site_url('admin.php?op=BannerClientEdit&amp;cid='. $client['id']) .'">
                        <i class="fa fa-edit fa-lg me-3" title="'. __d('two_banners', 'Editer') .'" data-bs-toggle="tooltip"></i>
                    </a>
                    <a href="'. site_url('admin.php?op=BannerClientDelete&amp;cid='. $client['id'] .'&amp;ok=0') .'" class="text-danger">
                        <i class="fas fa-trash fa-lg text-danger" title="'. __d('two_banners', 'Effacer') .'" data-bs-toggle="tooltip"></i>
                    </a>
                </td>
            </tr>';
    }

    echo '
        </tbody>
    </table>';

    // Add Banner
    $counts = DB::table('bannerclient')->count();

    if ($counts > 0) {
        echo '
        <hr />
        <h3 class="my-3">'. __d('two_banners', 'Ajouter une nouvelle bannière') .'</h3>
        <span class="help-block">
            '. __d('two_banners', 'Pour les bannières Javascript, saisir seulement le code javascript dans la zone URL du clic et laisser la zone image vide.') .'
        </span>
        <span class="help-block">
            '. __d('two_banners', 'Pour les bannières encore plus complexes (Flash, ...), saisir simplement la référence à votre_répertoire/votre_fichier .txt (fichier de code php) dans la zone URL du clic et laisser la zone image vide.') .'
        </span>
        <form id="bannersnewbanner" action="'. site_url('admin.php') .'" method="post">
            <div class="form-floating mb-3">
                <select class="form-select" name="cid">';
        
        $clients = DB::table('bannerclient')->get(['id', 'name']);

        foreach($clients as $client) {
            echo '<option value="'. $client['id'] .'">'. $client['name'] .'</option>';
        }

        echo '
                </select>
                <label for="cid">'. __d('two_banners', 'Nom de l\'annonceur') .'</label>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="number" id="imptotal" name="imptotal" min="0" max="99999999999" required="required" />
                <label for="imptotal">'. __d('two_banners', 'Impressions réservées') .'</label>
                <span class="help-block">0 = '. __d('two_banners', 'Illimité') .'</span>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" id="imageurl" name="imageurl" maxlength="320" />
                <label for="imageurl">' . __d('two_banners', 'URL de l\'image') . '</label>
                <span class="help-block text-end"><span id="countcar_imageurl"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" id="clickurl" name="clickurl" maxlength="320" required="required" />
                <label for="clickurl">'. __d('two_banners', 'URL du clic') . '</label>
                <span class="help-block text-end"><span id="countcar_clickurl"></span></span>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="number" id="userlevel" name="userlevel" min="0" max="9" value="0" required="required" />
                <label for="userlevel">'. __d('two_banners', 'Niveau de l\'Utilisateur') .'</label>
                <span class="help-block">'. __d('two_banners', '0=Tout le monde, 1=Membre seulement, 3=Administrateur seulement, 9=Désactiver') .'.</span>
            </div>
            <input type="hidden" name="op" value="BannersAdd" />
            <button class="btn btn-primary my-3" type="submit"><i class="fa fa-plus-square fa-lg me-2"></i>'. __d('two_banners', 'Ajouter une bannière') .' </button>
        </form>';
    }

    // Add Client
    echo '
    <hr />
    <h3 class="my-3">'. __d('two_banners', 'Ajouter un nouvel Annonceur') .'</h3>
    <form id="bannersnewanno" action="'. site_url('admin.php') .'" method="post">
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="name" name="name" maxlength="60" required="required" />
            <label for="name">'. __d('two_banners', 'Nom de l\'annonceur') .'</label>
            <span class="help-block text-end" id="countcar_name"></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="contact" name="contact" maxlength="60" required="required" />
            <label for="contact">'. __d('two_banners', 'Nom du Contact') .'</label>
            <span class="help-block text-end" id="countcar_contact"></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="email" id="email" name="email" maxlength="254" required="required" />
            <label for="email">'. __d('two_banners', 'E-mail') .'</label>
            <span class="help-block text-end" id="countcar_email"></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="login" name="login" maxlength="10" required="required" />
            <label for="login">'. __d('two_banners', 'Identifiant') .'</label>
            <span class="help-block text-end" id="countcar_login"></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="password" id="passwd" name="passwd" maxlength="20" required="required" />
            <label for="passwd">'. __d('two_banners', 'Mot de Passe') .'</label>
            <span class="help-block text-end" id="countcar_passwd"></span>
            <div class="progress" style="height: 0.4rem;">
                <div id="passwordMeter_cont" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
            </div>
        </div>
        <div class="form-floating mb-3">
            <textarea class="form-control" id="extrainfo" name="extrainfo" style="height:140px"></textarea>
            <label for="extrainfo">'. __d('two_banners', 'Informations supplémentaires') .'</label>
        </div>
        <input type="hidden" name="op" value="BannerAddClient" />
        <button class="btn btn-primary my-3" type="submit"><i class="fa fa-plus-square fa-lg me-2"></i>'. __d('two_banners', 'Ajouter un annonceur') .'</button>
    </form>';

    $arg1 = $counts > 0 ? 'var formulid = ["bannersnewbanner","bannersnewanno"];' : 'var formulid = ["bannersnewanno"];';
    
    $arg1 .= '
        inpandfieldlen("imageurl",320);
        inpandfieldlen("clickurl",320);
        inpandfieldlen("name",60);
        inpandfieldlen("contact",60);
        inpandfieldlen("email",254);
        inpandfieldlen("login",10);
        inpandfieldlen("passwd",20);';

    $fv_parametres = '
    passwd: {
        validators: {
            checkPassword: {},
        }
    },';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [BannersAdd description]
 *
 * @param   int     $cid        [$cid description]
 * @param   int     $imptotal   [$imptotal description]
 * @param   string  $imageurl   [$imageurl description]
 * @param   int     $clickurl   [$clickurl description]
 * @param   int     $userlevel  [$userlevel description]
 *
 * @return  void
 */
function BannersAdd(int $cid, int $imptotal, string $imageurl, int $clickurl, int $userlevel): void
{
    DB::table('banner')->insert(array(
        'id'        => $cid,
        'imptotal'  => $imptotal,
        'impmade'   => 1,
        'clicks'    => 0,
        'imageurl'  => $imageurl,
        'clickurl'  => $clickurl,
        'userlevel' => $userlevel,
        'date'      => 'now()',
    ));

    Header('Location: '. site_url('admin.php?op=BannersAdmin'));
}

/**
 * [BannerAddClient description]
 *
 * @param   string  $name       [$name description]
 * @param   string  $contact    [$contact description]
 * @param   string  $email      [$email description]
 * @param   string  $login      [$login description]
 * @param   string  $passwd     [$passwd description]
 * @param   string  $extrainfo  [$extrainfo description]
 *
 * @return  void
 */
function BannerAddClient(string $name, string $contact, string $email, string $login, string $passwd, string $extrainfo): void
{
    DB::table('bannerclient')->insert(array(
        'name'       => $name,
        'contact'    => $contact,
        'email'      => $email,
        'login'      => $login,
        'passwd'     => $passwd,
        'extrainfo'  => $extrainfo,
    ));

    Header('Location: '. site_url('admin.php?op=BannersAdmin'));
}

/**
 * [BannerFinishDelete description]
 *
 * @param   int   $bid  [$bid description]
 *
 * @return  void
 */
function BannerFinishDelete(int $bid): void
{
    DB::table('bannerfinish')->where('id', $bid)->delete();
    
    Header('Location: '. site_url('admin.php?op=BannersAdmin'));
}

/**
 * [BannerDelete description]
 *
 * @param   int   $bid  [$bid description]
 * @param   int   $ok   [$ok description]
 *
 * @return  void
 */
function BannerDelete(int $bid, int $ok = 0): void
{
    global $f_meta_nom, $f_titre;

    if ($ok == 1) {
        DB::table('banner')->where('id', $bid)->delete();
        
        Header('Location: '. site_url('admin.php?op=BannersAdmin'));
    } else {
        include("themes/default/header.php");

        GraphicAdmin(manuel('banners'));
        adminhead($f_meta_nom, $f_titre);

        $banner = DB::table('banner')->select('id', 'cid', 'imptotal', 'impmade', 'clicks', 'imageurl', 'clickurl')->find($bid);

        echo '
        <hr />
        <h3 class="text-danger">'. __d('two_banners', 'Effacer Bannière') .'</h3>';
        
        echo (($banner['imageurl'] != '') 
            ? '<a href="'. language::aff_langue($banner['clickurl']) .'"><img class="img-fluid" src="'. language::aff_langue($banner['imageurl']) .'" alt="banner" /></a><br />' 
            : $banner['clickurl']);

        echo '
        <table data-toggle="table" data-mobile-responsive="true">
            <thead>
                <tr>
                    <th data-halign="center" data-align="right">
                        '. __d('two_banners', 'ID') .'
                    </th>
                    <th data-halign="center" data-align="right">
                        '. __d('two_banners', 'Impressions') .'
                    </th>
                    <th data-halign="center" data-align="right">
                        '. __d('two_banners', 'Imp. restantes') .'
                    </th>
                    <th data-halign="center" data-align="right">
                        '. __d('two_banners', 'Clics') .'
                    </th>
                    <th data-halign="center" data-align="right">
                        % '. __d('two_banners', 'Clics') .'
                    </th>
                    <th data-halign="center" data-align="center">
                        '. __d('two_banners', 'Nom de l\'annonceur') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $client = DB::table('bannerclient')->find($banner['cid'], ['name']);

        $float = (100 * $banner['clicks'] / $banner['impmade']);
        $percent = substr( (string) $float, 0, 5);
        $left = (($banner['imptotal'] == 0) ? __d('two_banners', 'Illimité') : $banner['imptotal'] - $banner['impmade']);

        echo '
            <tr>
            <td>
                '. $banner['id'] .'
            </td>
            <td>
                '. $banner['impmade'] .'
            </td>
            <td>
                '. $left . '</td>
            <td>
                '. $banner['clicks'] .'
            </td>
            <td>
                '. $percent .'%
            </td>
            <td>
                '. $client['name'] .'
            </td>
            </tr>';
    }

    echo '
            </tbody>
        </table>
        <br />
        <div class="alert alert-danger">'. __d('two_banners', 'Etes-vous sûr de vouloir effacer cette Bannière ?') .'
            <br />
            <a class="btn btn-danger btn-sm mt-3" href="'. site_url('admin.php?op=BannerDelete&amp;bid='. $banner['id'] .'&amp;ok=1') .'">
                '. __d('two_banners', 'Oui') . '
            </a>
            &nbsp;
            <a class="btn btn-secondary btn-sm mt-3" href="'. site_url('admin.php?op=BannersAdmin') .'" >
                '. __d('two_banners', 'Non') .'
            </a>
        </div>';
    
    css::adminfoot('', '', '', '');
}

/**
 * [BannerEdit description]
 *
 * @param   int   $bid  [$bid description]
 *
 * @return  void
 */
function BannerEdit(int $bid): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('banners'));
    adminhead($f_meta_nom, $f_titre);

    $banner = DB::table('banner')->select('id', 'cid', 'imptotal', 'impmade', 'clicks', 'imageurl', 'clickurl', 'userlevel')->find($bid);

    echo '
    <hr />
    <h3 class="mb-2">'. __d('two_banners', 'Edition Bannière') .'</h3>';

    if ($banner['imageurl'] != '') {
        echo '<img class="img-fluid" src="'. language::aff_langue($banner['imageurl']) .'" alt="banner" /><br />';
    } else {
        echo $banner['clickurl'];
    }

    echo '
    <span class="help-block mt-2">
        '. __d('two_banners', 'Pour les bannières Javascript, saisir seulement le code javascript dans la zone URL du clic et laisser la zone image vide.') .'
    </span>
    <span class="help-block">
        '. __d('two_banners', 'Pour les bannières encore plus complexes (Flash, ...), saisir simplement la référence à votre_répertoire/votre_fichier .txt (fichier de code php) dans la zone URL du clic et laisser la zone image vide.') .'
    </span>
    <form id="bannersadm" action="'. site_url('admin.php') .'" method="post">
        <div class="form-floating mb-3">
            <select class="form-select" id="cid" name="cid">';
    
    $client = DB::table('bannerclient')->select('id', 'name')->find($banner['cid']);

    echo '<option value="'. $client['id'] .'" selected="selected">'. $client['name'] .'</option>';
    
    $clients = DB::table('bannerclient')->get(['id', 'name']);

    foreach($clients as $_client) {
        if ($client['id'] != $_client['id']) {
            echo '<option value="'. $_client['id'] .'">'. $_client['name'] .'</option>';
        }
    }

    echo '
            </select>
            <label for="cid">'. __d('two_banners', 'Nom de l\'annonceur') .'</label>
        </div>';

    $impressions = (($banner['imptotal'] == 0) ? __d('two_banners', 'Illimité') : $banner['imptotal']);

    echo '
        <div class="form-floating mb-3">
            <input class="form-control" type="number" id="impadded" name="impadded" min="0" max="99999999999" required="required" value="'. $banner['imptotal'] .'"/>
            <label for="impadded">' . __d('two_banners', 'Ajouter plus d\'affichages') .'</label>
            <span class="help-block">' . __d('two_banners', 'Réservé : ') .'<strong>'. $impressions .'</strong> '. __d('two_banners', 'Fait : ') .'<strong>'. $banner['impmade'] .'</strong></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="imageurl" name="imageurl" maxlength="320" value="'. $banner['imageurl'] .'" />
            <label for="imageurl">'. __d('two_banners', 'URL de l\'image') .'</label>
            <span class="help-block text-end"><span id="countcar_imageurl"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="clickurl" name="clickurl" maxlength="320" value="'. htmlentities($banner['clickurl'], ENT_QUOTES, 'utf-8') .'" />
            <label for="clickurl">'. __d('two_banners', 'URL du clic') .'</label>
            <span class="help-block text-end"><span id="countcar_clickurl"></span></span>
        </div>
        <div class="form-floating mb-3"> 
            <input class="form-control" type="number" name="userlevel" min="0" max="9" value="'. $banner['userlevel'] .'" required="required" />
            <label for="userlevel">'. __d('two_banners', 'Niveau de l\'Utilisateur') . '</label>
            <span class="help-block">'. __d('two_banners', '0=Tout le monde, 1=Membre seulement, 3=Administrateur seulement, 9=Désactiver') . '.</span>
        </div>
        <input type="hidden" name="bid" value="'. $banner['id'] .'" />
        <input type="hidden" name="imptotal" value="'. $banner['imptotal'] .'" />
        <input type="hidden" name="op" value="BannerChange" />
        <button class="btn btn-primary my-3" type="submit"><i class="fa fa-check-square fa-lg me-2"></i>'. __d('two_banners', 'Modifier la Bannière') .'</button>
    </form>';

    $arg1 = '
        var formulid = ["bannersadm"];
        inpandfieldlen("imageurl",320);
        inpandfieldlen("clickurl",320);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [BannerChange description]
 *
 * @param   int     $bid        [$bid description]
 * @param   int     $cid        [$cid description]
 * @param   int     $imptotal   [$imptotal description]
 * @param   int     $impadded   [$impadded description]
 * @param   string  $imageurl   [$imageurl description]
 * @param   string  $clickurl   [$clickurl description]
 * @param   int     $userlevel  [$userlevel description]
 *
 * @return  void
 */
function BannerChange(int $bid, int $cid, int $imptotal, int $impadded, string $imageurl, string $clickurl, int $userlevel): void
{
    $imp = $imptotal + $impadded;

    DB::table('banner')->where('id', $bid)->update(array(
        'cid'       => $cid,
        'imptotal'  => $imp,
        'imageurl'  => $imageurl,
        'clickurl'  => $clickurl,
        'userlevel' => $userlevel,
    ));

    Header('Location: '. site_url('admin.php?op=BannersAdmin'));
}

/**
 * [BannerClientDelete description]
 *
 * @param   int   $cid  [$cid description]
 * @param   int   $ok   [$ok description]
 *
 * @return  void
 */
function BannerClientDelete(int $cid, int $ok = 0): void
{
    global $f_meta_nom, $f_titre;

    if ($ok == 1) {

        DB::table('banner')->where('id', $cid)->delete();
        DB::table('bannerclient')->where('id', $cid)->delete();

        Header('Location: '. site_url('admin.php?op=BannersAdmin'));
    } else {
        include("themes/default/header.php");

        GraphicAdmin(manuel('banners'));
        adminhead($f_meta_nom, $f_titre);

        $client = DB::table('bannerclient')->select('id', 'name')->find($cid);

        echo '
        <hr />
        <h3 class="text-danger">'. __d('two_banners', 'Supprimer l\'Annonceur') .'</h3>
        <div class="alert alert-secondary my-3">
            '. __d('two_banners', 'Vous êtes sur le point de supprimer cet annonceur : ') .' 
            <strong>'. $client['name'] .'</strong> '. __d('two_banners', 'et toutes ses bannières !!!');
        
        $banners = DB::table('banner')->select('imageurl', 'clickurl')->where('cid', $client['id'])->get();

        if (empty($banners)) {
            echo '<br />'. __d('two_banners', 'Cet annonceur n\'a pas de bannière active pour le moment.') .'</div>
            <div class="alert alert-danger mt-3">'. __d('two_banners', 'Etes-vous sûr de vouloir effacer cet annonceur ?') .'</div>';
        } else {

            echo '<br />
                <span class="text-danger">
                    <b>'. __d('two_banners', 'ATTENTION !!!') . '</b>
                </span>
                <br />'. __d('two_banners', 'Cet annonceur a les BANNIERES ACTIVES suivantes dans') .' '. Config::get('npds.sitename') .'
            </div>';
        
            foreach($banners as $banner) {
                echo (($banner['imageurl'] != '') 
                    ? '<img class="img-fluid" src="' . language::aff_langue($banner['imageurl']) . '" alt="" /><br />' 
                    : $banner['clickurl'] . '<br />');
            }

            echo '<div class="alert alert-danger mt-3">'. __d('two_banners', 'Etes-vous sûr de vouloir effacer cet annonceur et TOUTES ses bannières ?') .'</div>';
        }
    }

    echo '<a href="'. site_url('admin.php?op=BannerClientDelete&amp;cid='. $client['id'] .'&amp;ok=1') .'" class="btn btn-danger">
            '. __d('two_banners', 'Oui') .'
        </a>
        <a href="'. site_url('admin.php?op=BannersAdmin') .'" class="btn btn-secondary">
            '. __d('two_banners', 'Non') .'
        </a>';
    
    css::adminfoot('', '', '', '');
}

/**
 * [BannerClientEdit description]
 *
 * @param   int   $cid  [$cid description]
 *
 * @return  void
 */
function BannerClientEdit(int $cid): void
{
    global $f_meta_nom, $f_titre;

    include("themes/default/header.php");

    GraphicAdmin(manuel('banners'));
    adminhead($f_meta_nom, $f_titre);

    $client = DB::table('bannerclient')->select('id', 'name', 'contact', 'email', 'login', 'passwd', 'extrainfo')->first($cid);

    echo '
    <hr />
    <h3 class="mb-3">'. __d('two_banners', 'Editer l\'annonceur') . '</h3>
    <form action="'. site_url('admin.php') .'" method="post" id="bannersedanno">
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="name" name="name" value="'. $client['name'] .'" maxlength="60" required="required" />
            <label for="name">'. __d('two_banners', 'Nom de l\'annonceur') .'</label>
            <span class="help-block text-end"><span id="countcar_name"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="contact" name="contact" value="'. $client['contact'] .'" maxlength="60" required="required" />
            <label for="contact">'. __d('two_banners', 'Nom du Contact') .'</label>
            <span class="help-block text-end"><span id="countcar_contact"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="email" id="email" name="email" maxlength="254" value="'. $client['email'] .'" required="required" />
            <label for="email">'. __d('two_banners', 'E-mail') .'</label>
            <span class="help-block text-end"><span id="countcar_email"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="text" id="login" name="login" maxlength="10" value="'. $client['login'] .'" required="required" />
            <label for="login">'. __d('two_banners', 'Identifiant') .'</label>
            <span class="help-block text-end"><span id="countcar_login"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="password" id="passwd" name="passwd" maxlength="20" value="'. $client['passwd'] .'" required="required" />
            <label for="passwd">'. __d('two_banners', 'Mot de Passe') .'</label>
            <span class="help-block text-end"><span id="countcar_passwd"></span></span>
            <div class="progress" style="height: 0.4rem;">
                <div id="passwordMeter_cont" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
            </div>
        </div>
        <div class="form-floating mb-3">
            <textarea class="form-control" id="extrainfo" name="extrainfo" style="height:140px">'. $client['extrainfo'] .'</textarea>
            <label for="extrainfo">'. __d('two_banners', 'Informations supplémentaires') .'</label>
        </div>
        <input type="hidden" name="cid" value="'. $client['id'] .'" />
        <input type="hidden" name="op" value="BannerClientChange" />
        <input class="btn btn-primary my-3" type="submit" value="'. __d('two_banners', 'Modifier annonceur') .'" />
    </form>';

    $arg1 = '
        var formulid = ["bannersedanno"];
        inpandfieldlen("name",60);
        inpandfieldlen("contact",60);
        inpandfieldlen("email",254);
        inpandfieldlen("login",10);
        inpandfieldlen("passwd",20);';

    $fv_parametres = '
    passwd: {
        validators: {
            checkPassword: {},
        }
    },';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

/**
 * [BannerClientChange description]
 *
 * @param   int     $cid        [$cid description]
 * @param   string  $name       [$name description]
 * @param   string  $contact    [$contact description]
 * @param   string  $email      [$email description]
 * @param   string  $extrainfo  [$extrainfo description]
 * @param   string  $login      [$login description]
 * @param   string  $passwd     [$passwd description]
 *
 * @return  void
 */
function BannerClientChange(int $cid, string $name, string $contact, string $email, string $extrainfo, string $login, string $passwd): void
{
    DB::table('bannerclient')->where('id', $cid)->update(array(
        'name'        => $name,
        'contact'     => $contact,
        'email'       => $email,
        'login'       => $login,
        'passwd'      => $passwd,
        'extrainfo'   => $extrainfo,
    )); 

    Header('Location: '. site_url('admin.php?op=BannersAdmin'));
}

switch ($op) {
    case 'BannersAdd':
        BannersAdd($cid, $imptotal, $imageurl, $clickurl, $userlevel);
        break;

    case 'BannerAddClient':
        BannerAddClient($name, $contact, $email, $login, $passwd, $extrainfo);
        break;

    case 'BannerFinishDelete':
        BannerFinishDelete($bid);
        break;

    case 'BannerDelete':
        BannerDelete($bid, $ok);
        break;

    case 'BannerEdit':
        BannerEdit($bid);
        break;

    case 'BannerChange':
        BannerChange($bid, $cid, $imptotal, $impadded, $imageurl, $clickurl, $userlevel);
        break;

    case 'BannerClientDelete':
        BannerClientDelete($cid, $ok);
        break;

    case 'BannerClientEdit':
        BannerClientEdit($cid);
        break;

    case 'BannerClientChange':
        BannerClientChange($cid, $name, $contact, $email, $extrainfo, $login, $passwd);
        break;

    case 'BannersAdmin':
    default:
        BannersAdmin();
        break;
}