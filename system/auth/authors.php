<?php

declare(strict_types=1);

namespace npds\system\auth;

use npds\system\assets\css;
use npds\system\cookie\cookie;
use npds\system\security\protect;
use npds\system\support\facades\DB;

class authors
{
 
    /**
     * 
     *
     * @return  string
     */
    public static function extractAdmin(): string 
    {
        $admin = cookie::extratCookie('admin');

        if (isset($admin)) {
            $ibid = explode(':', base64_decode($admin));
            array_walk($ibid, [protect::class, 'url']);
            $admin = base64_encode(str_replace("%3A", ":", urlencode(base64_decode($admin))));
        }

        return $admin;
    }

    /**
     * 
     *
     * @return  string
     */
    public static function getAdmin(): string
    {
        return static::extractAdmin();
    }

    /**
     * 
     *
     * @return  array
     */
    public static function cookieAdmin():array|bool
    {
        $admin = static::extractAdmin();

        if (isset($admin)) {
            return cookie::cookiedecode($admin);
        }

        return false;
    }

    /**
     * Phpnuke compatibility functions
     *
     * @param   string  $xadmin  [$xadmin description]
     *
     * @return  bool
     */    
    public static function is_admin(string $xadmin): bool
    {
        $admin = static::getAdmin();
        
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
    public static function formatAidHeader(string $aid): void
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
    public static function login(): void
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
    
        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [logout description]
     *
     * @return  void    [return description]
     */
    public static function logout(): void
    {
        setcookie("admin");
        setcookie("adm_exp");
        unset($admin);
        Header("Location: index.php");
    }
}
