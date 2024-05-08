<?php

namespace Modules\TwoBanners\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class BannersClient extends AdminController
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
        $this->f_meta_nom = 'BannersAdmin';

        $this->f_titre = __d('two_banners', 'Administration des bannières');

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


}