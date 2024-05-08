<?php

namespace Modules\TwoBanners\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Banners extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'banners';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = '';

        $this->f_titre = __d('two_banners', '');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_banners', ''));
    }

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


}