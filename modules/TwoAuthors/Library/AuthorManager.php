<?php

declare(strict_types=1);

namespace Modules\TwoAuthors\Library;

use Two\Http\Request;
use Two\Support\Facades\DB;
use Two\Foundation\Application;
use Modules\TwoCore\Support\Protect;


class AuthorManager
{
 
    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * The Request Instance.
     *
     * @var \Two\Http\Request
     */
    public $request;


    /**
     * Mailer constructor.
     *
     * @param string $theme
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;

        $this->request = $request;
    }

    /**
     * 
     *
     * @return  string|null
     */
    public function extractAdmin()
    {
        $admin = $this->request->cookie(PREFIX .'admin');

        if (isset($admin)) {
            $ibid = explode(':', base64_decode($admin));
            array_walk($ibid, [Protect::class, 'url']);
            return base64_encode(str_replace("%3A", ":", urlencode(base64_decode($admin))));
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
            $cookie = explode(':', base64_decode($admin));

            if(is_null($arg)) {
                return $cookie;
            } elseif (is_numeric($arg)) {
                if (array_key_exists($arg, $cookie)) {
                    return $cookie[$arg];
                }
            }
        }
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
        <h1>' . __d('two_authors', 'Administration') . '</h1>
        <div id ="adm_men">
            <h2 class="mb-3"><i class="fas fa-sign-in-alt fa-lg align-middle me-2"></i>' . __d('two_authors', 'Connexion') . '</h2>
            <form action="admin.php" method="post" id="adminlogin" name="adminlogin">
                <div class="row g-3">
                    <div class="col-sm-6">
                    <div class="mb-3 form-floating">
                        <input id="aid" class="form-control" type="text" name="aid" maxlength="20" placeholder="' . __d('two_authors', 'Administrateur ID') . '" required="required" />
                        <label for="aid">' . __d('two_authors', 'Administrateur ID') . '</label>
                    </div>
                    <span class="help-block text-end"><span id="countcar_aid"></span></span>
                    </div>
                    <div class="col-sm-6">
                    <div class="mb-3 form-floating">
                        <input id="pwd" class="form-control" type="password" name="pwd" maxlength="18" placeholder="' . __d('two_authors', 'Mot de Passe') . '" required="required" />
                        <label for="pwd">' . __d('two_authors', 'Mot de Passe') . '</label>
                    </div>
                    <span class="help-block text-end"><span id="countcar_pwd"></span></span>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg" type="submit">' . __d('two_authors', 'Valider') . '</button>
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
    
        adminfoot('fv', '', $arg1, '');
    }

    /**
     * [logout description]
     *
     * @return  void    [return description]
     */
    public function logout()
    {
        setcookie("admin");
        setcookie("adm_exp");
        unset($admin); // normalement ne sert plus par la suite a verifier !
        Header("Location: index.php");
    }
}
