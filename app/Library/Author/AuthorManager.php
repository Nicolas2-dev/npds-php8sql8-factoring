<?php

declare(strict_types=1);

namespace App\Library\author;

use App\Support\Security\Protect;

use Npds\Support\Facades\DB;
use Npds\Foundation\Application;


class AuthorManager
{
    
    /**
     * The Application Instance.
     *
     * @var Application
     */
    public $app;

    
    /**
     * Mailer constructor.
     *
     * @param string $theme
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 
     *
     * @return  string|null
     */
    public function extractAdmin()
    {
        $admin = $this->extratCookie('admin');

        if (isset($admin)) {
            $ibid = explode(':', base64_decode($admin));
            array_walk($ibid, [Protect::class, 'url']);
            
            return base64_encode(str_replace("%3A", ":", urlencode(base64_decode($admin))));
        }

        return null;
    }

    /**
     * 
     *
     * @param   string  $name  [$name description]
     *
     * @return  string|bool
     */
    public function extratCookie(string $name)
    {
        if (!empty($_COOKIE)) {
            extract($_COOKIE, EXTR_OVERWRITE);
        }

        if(isset($$name)) {
            return $$name;            
        }
    }

    /**
     * 
     *
     * @return  string|null
     */
    public function getAdmin()
    {
        return $this->extractAdmin();
    }

    /**
     * 
     *
     * @return  string|array|bool|int
     */
    public function cookieAdmin($arg = null)
    {
        $admin = $this->extractAdmin();

        if (isset($admin)) {

            $cookie = explode(':',base64_decode($admin));

            if(is_null($arg)) {
                return $cookie;
            } elseif (is_numeric($arg)) {
                if (array_key_exists($arg, $cookie)) {
                    return $cookie[$arg];
                }
                
                return null;
            }
        }

        return null;
    }

    /**
     * Phpnuke compatibility functions
     *
     * @param   string  $xadmin  [$xadmin description]
     *
     * @return  bool
     */    
    public function is_admin(string $xadmin)
    {
        $admin = $this->getAdmin();

        if (isset($admin) and ($admin != '')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Affiche URL et Email d'un auteur
     *
     * @param   string  $aid  [$aid description]
     *
     * @return  void
     */
    public function formatAidHeader(string $aid)
    {
        $author = DB::table('authors')->select('url', 'email')->where('aid', $aid)->first();

        if ($author) {
            
            if (isset($author['url'])) {
                echo '<a href="' . $author['url'] . '" >' . $aid . '</a>';
            } elseif (isset($author['email'])) {
                echo '<a href="mailto:' . $author['email'] . '" >' . $aid . '</a>';
            } else {
                echo $aid;
            }
        }
    }

    /**
     * [login description]
     *
     * @return  void
     */
    public function login()
    {
        include("themes/default/header.php");
    
        echo '
        <h1>' . adm_translate("Administration") . '</h1>
        <div id ="adm_men">
            <h2 class="mb-3"><i class="fas fa-sign-in-alt fa-lg align-middle me-2"></i>' . adm_translate("Connexion") . '</h2>
            <form action="admin.php" method="post" id="adminlogin" name="adminlogin">
                <div class="row g-3">
                    <div class="col-sm-6">
                    <div class="mb-3 form-floating">
                        <input id="aid" class="form-control" type="text" name="aid" maxlength="20" placeholder="' . adm_translate("Administrateur ID") . '" required="required" />
                        <label for="aid">' . adm_translate("Administrateur ID") . '</label>
                    </div>
                    <span class="help-block text-end"><span id="countcar_aid"></span></span>
                    </div>
                    <div class="col-sm-6">
                    <div class="mb-3 form-floating">
                        <input id="pwd" class="form-control" type="password" name="pwd" maxlength="18" placeholder="' . adm_translate("Mot de Passe") . '" required="required" />
                        <label for="pwd">' . adm_translate("Mot de Passe") . '</label>
                    </div>
                    <span class="help-block text-end"><span id="countcar_pwd"></span></span>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg" type="submit">' . adm_translate("Valider") . '</button>
                <input type="hidden" name="op" value="login" />
            </form>
            <script type="text/javascript">
                //<![CDATA[
                    document.adminlogin.aid.focus();
                    $(document).ready(function() {
                    inpandfieldlen("pwd",18);
                    inpandfieldlen("aid",20);
                    });
                //]]>
            </script>';
    
        $arg1 = '
            var formulid =["adminlogin"];
            ';
    
        $this->asset()->adminfoot('fv', '', $arg1, '');
    }

    /**
     * [logout description]
     *
     * @return  void    [return description]
     */
    public function logout()
    {
        $this->auth()->guarg('admin')->logout();

        return $this->redirect()->to('login')->with('success', 'You have successfully logged out.');
    }

    /**
     * [getAssets description]
     *
     * @return  [type]  [return description]
     */
    public function asset()
    {
        return $this->app['npds.assets'];
    }

    /**
     * [getAuths description]
     *
     * @return  [type]  [return description]
     */
    public function auth()
    {
        return $this->app['auth'];
    }

    /**
     * [redirect description]
     *
     * @return  [type]  [return description]
     */
    public function redirect()
    {
        return $this->app['redirect'];
    }
}
