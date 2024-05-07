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
declare(strict_types=1);

use App\Support\Assets\Css;
use App\Support\Auth\Users;
use App\Support\Cache\Cache;
use App\Support\Routing\Url;
use App\Support\Str;
use App\Support\Mail\Mailler;
use App\Support\Language\Language;
use Npds\Config\Config;
use Npds\Support\Facades\DB;
use Npds\Support\Facades\Request;


include_once('boot/bootstrap.php');

/**
 * [viewbanner description]
 *
 * @return  void
 */
function viewbanner(): void
{
    $okprint = false;
    $while_limit = 3;
    $while_cpt = 0;

    $numrows = DB::table('banner')
                ->select('id')
                ->where('userlevel', '!=', 9)
                ->first();

    while ((!$okprint) and ($while_cpt < $while_limit)) {

        if ($numrows > 0) {
            $float = ((float) microtime() * 1000000);
            mt_srand( (int) $float ); 
            $bannum = mt_rand(0, $numrows['id']);
        } else {
            break;
        }

        $banner = DB::table('banner')
                    ->select('id', 'userlevel')
                    ->where('userlevel', '!=', 9)
                    ->limit(1)
                    ->offset($bannum)
                    ->get();

        $bid = $banner[0]['id'];

        if ($banner[0]['userlevel'] == 0) {
            $okprint = true;
        } else {
            if ($banner[0]['userlevel'] == 1) {
                if (Users::secur_static("member")) {
                    $okprint = true;
                }
            }

            if ($banner[0]['userlevel'] == 3) {
                if (Users::secur_static("admin")) {
                    $okprint = true;
                }
            }
        }

        $while_cpt = $while_cpt + 1;
    }

    // Le risque est de sortir sans un BID valide
    if (!isset($bid)) {

        $rowQ1 = Cache::Q_Select3(
            DB::table('banner')->select('id')->where('userlevel', 0)->limit(0)->offset(1)->first(), 
            86400, 
            'banner(id)'
        );

        if ($rowQ1) {
            // erreur à l'install quand on n'a pas de banner dans la base ....
            $bid = $rowQ1;
            $okprint = true;
        }
    }

    if ($okprint) {
        $myhost = Request::getIp();
        
        if (Config::get('npds.myIP') != $myhost) {
            DB::table('banner')->where('id', $bid)->update(array(
                'impmade'       => DB::raw('impmade+1'),
            ));
        }

        if (($numrows > 0) and ($bid)) {

            $banner = DB::table('banner')
                        ->select('id', 'imptotal', 'impmade', 'clicks', 'imageurl', 'clickurl', 'date')
                        ->where('id', $bid)
                        ->first();

            if ($banner['imptotal'] == $banner['impmade']) {
                DB::table('bannerfinish')->insert(array(
                    'cid'           => $banner['cid'],
                    'impressions'   => $banner['impmade'],
                    'clicks'        => $banner['clicks'],
                    'datestart'     => $banner['date'],
                    'dateend'       => 'now()',
                ));

                DB::table('banner')->where('id', $bid)->delete();
            }

            if ($banner['imageurl'] != '') {
                echo '<a href="' . site_url('banners.php?client=click&amp;bid=' . $bid) .'" target="_blank"><img class="img-fluid" src="' . Language::aff_langue($banner['imageurl']) . '" alt="" /></a>';
            } else {
                if (stristr($banner['clickurl'], '.txt')) {
                    if (file_exists($banner['clickurl'])) {
                        include_once($banner['clickurl']);
                    }
                } else {
                    echo $banner['clickurl'];
                }
            }
        }
    }
}

/**
 * [clickbanner description]
 *
 * @param   int   $bid  [$bid description]
 *
 * @return  void
 */
function clickbanner(): void
{
    $banner = DB::table('banner')
                ->select('clickurl')
                ->where('id', $bid = Request::query('bid'))
                ->first();

    DB::table('banner')->where('id', $bid)->update(array(
        'clicks'       => DB::raw('clicks+1'),
    ));

    if ($banner['clickurl'] == '') {
        $banner['clickurl'] = Config::get('npds.nuke_url');
    }

    Header("Location: " . Language::aff_langue($banner['clickurl']));
}

/**
 * [clientlogin description]
 *
 * @return  void
 */
function clientlogin(): void 
{
    header_page();
    
    echo '
        <div class="card card-body mb-3">
            <h3 class="mb-4"><i class="fas fa-sign-in-alt fa-lg me-3 align-middle"></i>' . translate("Connexion") . '</h3>
            <form id="loginbanner" action="' . site_url('banners.php') .'" method="post">
                <fieldset>
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="login" name="login" maxlength="25" required="required" />
                    <label for="login">' . translate("Identifiant ") . '</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" type="password" id="pass" name="pass" maxlength="25" required="required" />
                    <label for="pass">' . translate("Mot de passe") . '</label>
                    <span class="help-block">' . translate("Merci de saisir vos informations") . '</span>
                </div>
                <input type="hidden" name="client" value="Ok" />
                <button class="btn btn-primary my-3" type="submit">' . translate("Valider") . '</button>
                </div>
                </fieldset>
            </form>
        </div>';
    
    $arg1 = 'var formulid=["loginbanner"];';

    Css::adminfoot('fv', '', $arg1, 'no');

    footer_page();
}

/**
 * [IncorrectLogin description]
 *
 * @return  void
 */
function IncorrectLogin(): void
{
    header_page();

    echo '<div class="alert alert-danger lead">' . translate("Identifiant incorrect !") . '<br /><button class="btn btn-secondary mt-2" onclick="javascript:history.go(-1)" >' . translate("Retour en arrière") . '</button></div>';
    
    footer_page();
}

/**
 * [header_page description]
 *
 * @return  void
 */
function header_page(): void 
{
    include_once("modules/upload/config/upload.conf.php");
    include("storage/meta/meta.php");

    if ($url_upload_css) {
        $url_upload_cssX = str_replace('style.css', Config::get('npds.language') . '-style.css', $url_upload_css);
        
        if (is_readable($url_upload . $url_upload_cssX)) {
            $url_upload_css = $url_upload_cssX;
        }

        print("<link href=\"" . $url_upload . $url_upload_css . "\" title=\"default\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />\n");
    }

    if (file_exists('themes/default/view/include/header_head.inc')) {
        include('themes/default/view/include/header_head.inc');
    }

    $theme = Config::get('npds.Default_Theme');

    if (file_exists('themes/' . $theme . '/include/header_head.inc')) {
        include('themes/' . $theme . '/include/header_head.inc');
    }

    if (file_exists('themes/' . $theme . '/style/style.css')) {
        echo '<link href="themes/' . $theme . '/style/style.css" rel="stylesheet" type=\"text/css\" media="all" />';
    }

    echo '
    </head>
    <body style="margin-top:64px;">
        <div class="container-fluid">
        <nav class="navbar navbar-dark navbar-expand-lg fixed-top bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand" href="' . site_url('index.php') .'"><i class="fa fa-home fa-lg me-2"></i></a>
            <span class="navbar-text">' . translate("Bannières - Publicité") . '</span>
            </div>
        </nav>
        <h2 class="mt-4">' . translate("Bannières - Publicité") . ' @ ' . Config::get('npds.Titlesitename') . '</h2>
        <p align="center">';
}

/**
 * [footer_page description]
 *
 * @return  void
 */
function footer_page(): void 
{
    include('themes/default/view/include/footer_after.inc');

    echo '</p>
        </div>
    </body>
    </html>';
}

/**
 * [bannerstats description]
 *
 * @param   string  $login  [$login description]
 * @param   string  $pass   [$pass description]
 *
 * @return  void
 */
function bannerstats(): void
{
    $login = Request::input('login');

    $bannerclient = DB::table('bannerclient')
                        ->select('id', 'name', 'passwd')
                        ->where('login', $login)
                        ->first();

    $pass = Request::input('pass');    
                        
    if ($login == '' and $pass == '' or $pass == '') {
        IncorrectLogin();
    } else {
        if ($pass == $bannerclient['passwd']) {
            header_page();

            echo '
            <h3>' . translate("Bannières actives pour") . ' ' . $bannerclient['name'] . '</h3>
            <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-columns="true" data-icons="icons" data-icons-prefix="fa">
                <thead>
                <tr>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">ID</th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate("Réalisé") . '</th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate("Impressions") . '</th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate("Imp. restantes") . '</th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate("Clics") . '</th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">% ' . translate("Clics") . '</th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right">' . translate("Fonctions") . '</th>
                </tr>
                </thead>
                <tbody>';

            foreach (DB::table('banner')
                        ->select('id', 'imptotal', 'impmade', 'clicks', 'date')
                        ->where('cid', $bannerclient['id'])
                        ->get() as $banner) 
            {
                $float = (100 * $banner['clicks'] / $banner['impmade']);

                $percent = $banner['impmade'] == 0 ? '0' : substr( (string) $float, 0, 5);
                $left = $banner['imptotal'] == 0 ? translate("Illimité") : $banner['imptotal'] - $banner['impmade'];
                
                echo '
                <tr>
                    <td>' . $banner['id'] . '</td>
                    <td>' . $banner['impmade'] . '</td>
                    <td>' . $banner['imptotal'] . '</td>
                    <td>' . $left . '</td>
                    <td>' . $banner['clicks'] . '</td>
                    <td>' . $percent . '%</td>
                    <td><a href="' . site_url('banners.php?client=EmailStats&amp;login=' . $login . '&amp;cid=' . $bannerclient['id'] . '&amp;bid=' . $banner['id']) .'" ><i class="far fa-envelope fa-lg me-2 tooltipbyclass" data-bs-placement="top" title="E-mail Stats"></i></a></td>
                </tr>';
            }

            echo '
                </tbody>
            </table>
            <div class="lead my-3">
                <a href="' . Config::get('npds.nuke_url') . '" target="_blank">' . Config::get('npds.sitename') . '</a>
            </div>';

            foreach (DB::table('banner')
                        ->select('id', 'imageurl', 'clickurl')
                        ->where('cid', $bannerclient['id'])
                        ->get() as $banner) 
            {
                echo '<div class="card card-body mb-3">';

                if ($banner['imageurl'] != '') {
                    echo '<p><img src="' . Language::aff_langue($banner['imageurl']) . '" class="img-fluid" />'; // pourquoi aff_langue ??
                } else {
                    echo '<p>';
                    echo $banner['clickurl'];
                }

                echo '<h4 class="mb-2">Banner ID : ' . $banner['id'] . '</h4>';

                if ($banner['imageurl'] != '') {
                    echo '<p>' . translate("Cette bannière est affichée sur l'url") . ' : <a href="' . Language::aff_langue($banner['clickurl']) . '" target="_Blank" >[ URL ]</a></p>';
                }

                echo '<form action="' . site_url('banners.php') .'" method="get">';
                
                if ($banner['imageurl'] != '') {
                    echo '
                    <div class="mb-3 row">
                        <label class="control-label col-sm-12" for="url">' . translate("Changer") . ' URL</label>
                        <div class="col-sm-12">
                            <input class="form-control" type="text" name="url" maxlength="200" value="' . $banner['clickurl'] . '" />
                        </div>
                    </div>';
                } else {
                    echo '
                    <div class="mb-3 row">
                        <label class="control-label col-sm-12" for="url">' . translate("Changer") . ' URL</label>
                        <div class="col-sm-12">
                            <input class="form-control" type="text" name="url" maxlength="200" value="' . htmlentities($banner['clickurl'], ENT_QUOTES, 'utf-8') . '" />
                        </div>
                    </div>';
                }

                echo '
                <input type="hidden" name="login" value="' . $login . '" />
                <input type="hidden" name="bid" value="' . $banner['id'] . '" />
                <input type="hidden" name="pass" value="' . $pass . '" />
                <input type="hidden" name="cid" value="' . $bannerclient['id'] . '" />
                <input class="btn btn-primary" type="submit" name="client" value="' . translate("Changer") . '" />
                </form>
                </p>
                </div>';
            }

            // Finnished Banners
            echo "<br />";
            echo '
            <h3>' . translate("Bannières terminées pour") . ' ' . $bannerclient['name'] . '</h3>
            <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-columns="true" data-icons="icons" data-icons-prefix="fa">
                <thead>
                <tr>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">ID</td>
                    <th data-halign="center" data-align="right" data-sortable="true">' . translate("Impressions") . '</th>
                    <th data-halign="center" data-align="right" data-sortable="true">' . translate("Clics") . '</th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">% ' . translate("Clics") . '</th>
                    <th data-halign="center" data-align="right" data-sortable="true">' . translate("Date de début") . '</th>
                    <th data-halign="center" data-align="right" data-sortable="true">' . translate("Date de fin") . '</th>
                </tr>
                </thead>
                <tbody>';

            foreach (DB::table('bannerfinish')
                        ->select('id', 'impressions', 'clicks', 'datestart', 'dateend')
                        ->where('cid', $bannerclient['id'])
                        ->get() as $finish) 
            {  
                $float = (100 * $finish['clicks'] / $finish['impressions']);
                $percent = substr((string) $float, 0, 5);
                
                echo '
                <tr>
                    <td>' . $finish['id'] . '</td>
                    <td>' . Str::wrh($finish['impressions']) . '</td>
                    <td>' . $finish['clicks'] . '</td>
                    <td>' . $percent . ' %</td>
                    <td><small>' . $finish['datestart'] . '</small></td>
                    <td><small>' . $finish['dateend'] . '</small></td>
                </tr>';
            }

            echo '
                </tbody>
            </table>';

            Css::adminfoot('fv', '', '', 'no');

            footer_page();
        } else {
            IncorrectLogin();
        }
    }
}

/**
 * [EmailStats description]
 *
 * @param   string  $login  [$login description]
 * @param   int     $cid    [$cid description]
 * @param   int     $bid    [$bid description]
 *
 * @return  void
 */
function EmailStats(): void
{
    $cid = (int) Request::query('cid');
    $bid = (int) Request::query('bid');

    $bannerclient = DB::table('bannerclient')
                        ->select('login')
                        ->where('id', $cid)
                        ->first();

    if (Request::query('login') == $bannerclient['login']) {

        $bannerclient = DB::table('bannerclient')
                            ->select('name', 'email')
                            ->where('id', $cid)
                            ->first();

        if ($bannerclient['email'] == '') {
            header_page();

            echo "<p align=\"center\"><br />" . translate("Les statistiques pour la bannières ID") . " : $bid " . translate("ne peuvent pas être envoyées.") . "<br /><br />
                " . translate("Email non rempli pour : ") . $bannerclient['name'] ."<br /><br /><a href=\"javascript:history.go(-1)\" >" . translate("Retour en arrière") . "</a></p>";
            
            footer_page();
        } else {

            $banner  = DB::table('banner')
                ->select('id', 'imptotal', 'impmade', 'clicks', 'imageurl', 'clickurl', 'date')
                ->where('id', $bid)
                ->where('cid', $cid)
                ->first();

            $float = (100 * $banner['clicks'] / $banner['impmade']);

            $percent = $banner['impmade'] == 0 ? '0' : substr( (string) $float , 0, 5);
            
            if ($banner['imptotal'] == 0) {
                $left = translate("Illimité");
                $banner['imptotal'] = translate("Illimité");
            } else {
                $left = $banner['imptotal'] - $banner['impmade'];
            }

            $fecha = date(translate("dateinternal"), time() + ((int) Config::get('npds.gmt') * 3600));
            
            $subject = html_entity_decode(translate("Bannières - Publicité"), ENT_COMPAT | ENT_HTML401, 'utf-8') . ' : ' . Config::get('npds.sitename');
            
            $message  = "Client : ". $bannerclient['name'] ."\n" . translate("Bannière") . " ID : ". $banner['id'] ."\n" . translate("Bannière") . " Image : ". $banner['imageurl'] ."\n" . translate("Bannière") . " URL : ". $banner['clickurl'] ."\n\n";
            $message .= "Impressions " . translate("Réservées") . " : ". $banner['imptotal'] ."\nImpressions " . translate("Réalisées") . " : " .$banner['impmade'] ."\nImpressions " . translate("Restantes") . " : $left\nClicks " . translate("Reçus") . " : ". $banner['clicks'] ."\nClicks " . translate("Pourcentage") . " : $percent%\n\n";
            $message .= translate("Rapport généré le") . ' : ' . "$fecha\n\n";
            $message .= Config::get('signature.message');

            Mailler::send_email($bannerclient['email'], $subject, $message, '', true, 'html', '');

            header_page();
            echo '
            <div class="card bg-light">
                <div class="card-body"
                <p>' . $fecha . '</p>
                <p>' . translate("Les statistiques pour la bannières ID") . ' : ' . $banner['id'] . ' ' . translate("ont été envoyées.") . '</p>
                <p>' . $bannerclient['email'] . ' : Client : ' . $bannerclient['name'] . '</p>
                <p><a href="javascript:history.go(-1)" class="btn btn-primary">' . translate("Retour en arrière") . '</a></p>
                </div>
            </div>';
        }
    } else {
        header_page();
        echo "<p align=\"center\"><br />" . translate("Identifiant incorrect !") . "<br /><br />" . translate("Merci de") . " <a href=\"" . site_url('banners.php?op=login') ."\" class=\"noir\">" . translate("vous reconnecter.") . "</a></p>";
    }

    footer_page();
}

/**
 * [change_banner_url_by_client description]
 *
 * @param   string  $login  [$login description]
 * @param   string  $pass   [$pass description]
 * @param   int     $cid    [$cid description]
 * @param   int     $bid    [$bid description]
 * @param   string  $url    [$url description]
 *
 * @return  void
 */
function change_banner_url_by_client(): void
{
    header_page();

    $bannerclient = DB::table('bannerclient')
                        ->select('passwd', 'login')
                        ->where('id', Request::query('cid'))
                        ->first();

    $login = Request::query('login');                    
    $pass = Request::query('pass');

    if (!empty($pass) and !empty($login) and $pass == $bannerclient['passwd'] and $login == $bannerclient['login']) {

        DB::table('banner')->where('id', Request::query('bid'))->update(array(
            'clickurl'       => Request::query('url'),
        ));

        echo '
            <div class="alert alert-success">
                ' . translate("Vous avez changé l'url de la bannière") . '
                <br />
                <a href="javascript:history.go(-1)" class="alert-link">
                    ' . translate("Retour en arrière") . '
                </a>
            </div>';
    } else
        echo '
            <div class="alert alert-danger">
                ' . translate("Identifiant incorrect !") . '
                <br />' . translate("Merci de") . ' 
                <a href="' . site_url('banners.php?client=login') .'" class="alert-link">
                    ' . translate("vous reconnecter.") . '
                </a>
            </div>';
    
    footer_page();
}

switch (Request::input('client')) {
    case 'click':
        clickbanner();
        break;

    case 'login':
        clientlogin();
        break;

    case 'Ok':
        bannerstats();
        break;

    case translate('Changer'):
        change_banner_url_by_client();
        break;

    case 'EmailStats':
        EmailStats();
        break;
        
    default:
        if (Config::get('npds.banners')) {
            viewbanner();
        } else {
            Url::redirect_url('index.php');
        }
        break;
}
