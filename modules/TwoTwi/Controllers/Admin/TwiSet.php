<?php

namespace Modules\TwoTwi\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class TwiSet extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'manuel';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'npds_twi';

        $this->f_titre = __d('two_twi', 'npds_twitter');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_', ''));
    }

    function Configuretwi($subop, $ModPath, $ModStart, $class_sty_2, $npds_twi_arti, $npds_twi_urshort, $npds_twi_post, $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $tbox_width, $tbox_height)
    {
        global $language;
        if (file_exists('modules/' . $ModPath . '/config/twi_conf.php'))
            include('modules/' . $ModPath . '/config/twi_conf.php');
        $hlpfile = 'modules/' . $ModPath . '/view/manuels/' . $language . '/manuel.html';
    
        global $f_meta_nom, $f_titre, $adminimg, $npds_twi;
        $checkarti_y = '';
        $checkarti_n = '';
        $checkpost_y = '';
        $checkpost_n = '';
        $urshort_mr = '';
        $urshort_ft = '';
        $urshort_c = '';
        if ($npds_twi_arti === 1) $checkarti_y = 'checked="checked"';
        else $checkarti_n = 'checked="checked"';
        if ($npds_twi_post === 1) $checkpost_y = 'checked="checked"';
        else $checkpost_n = 'checked="checked"';
        if ($npds_twi_urshort === 1) $urshort_mr = 'checked="checked"';
        if ($npds_twi_urshort === 2) $urshort_ft = 'checked="checked"';
        if ($npds_twi_urshort === 3) $urshort_c = 'checked="checked"';
        else {
            $checkpost_n = 'checked="checked"';
        };
        //en attente implémentation pour notice
        settype($tbox_width, 'integer');
        settype($tbox_height, 'integer');
    
        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);
        echo '<hr />';
        if ($npds_twi !== 1)
            echo '
        <div class="alert alert-danger">' . __d('two_twi', 'La publication de vos news sur twitter n\'est pas autorisée vous devez l\'activer') . ' <a class="alert-link" href="admin.php?op=Configure">' . __d('two_twi', 'Ici') . '</a></div>';
        else
            echo '
        <div class="alert alert-success">' . __d('two_twi', 'La publication de vos news sur twitter est autorisée. Vous pouvez révoquer cette autorisation') . ' <a class="alert-link" href="admin.php?op=Configure">' . __d('two_twi', 'Ici') . '</a></div>';
    
        echo '
        <h3 class="mb-3">' . __d('two_twi', 'Configuration du module npds_twi') . '</h3>
        <span class="text-danger">*</span> ' . __d('two_twi', 'requis') . '
        <form id="twitterset" action="admin.php" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-6" for="npds_twi_arti">' . __d('two_twi', 'Activation de la publication auto des articles') . '</label>
                <div class="col-sm-6 my-2">
                    <div class="form-check">
                    <input class="form-check-input" type="radio" id="npds_twi_arti_y" name="npds_twi_arti" value="1" ' . $checkarti_y . ' />
                    <label class="form-check-label" for="npds_twi_arti_y">' . __d('two_twi', 'Oui') . '</label>
                    </div>
                    <div class="form-check">
                    <input class="form-check-input" type="radio" id="npds_twi_arti_n" name="npds_twi_arti" value="0" ' . $checkarti_n . ' />
                    <label class="form-check-label" for="npds_twi_arti_n">' . __d('two_twi', 'Non') . '</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="form-label col-sm-6" for="npds_twi_urshort">' . __d('two_twi', 'Méthode pour le raccourciceur d\'URL') . '</label>
                <div class="col-sm-6">
                    <div class="custom-controls-stacked">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="npds_twi_mod" name="npds_twi_urshort" value="1" ' . $urshort_mr . ' />
                        <label class="form-check-label" for="npds_twi_mod">' . __d('two_twi', 'Réécriture d\'url avec mod_rewrite') . '</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="npds_twi_force" name="npds_twi_urshort" value="2" ' . $urshort_ft . ' />
                        <label class="form-check-label" for="npds_twi_force">' . __d('two_twi', 'Réécriture d\'url avec ForceType') . '</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="npds_twi_npd" name="npds_twi_urshort" value="3" ' . $urshort_c . ' />
                        <label class="form-check-label" for="npds_twi_npd">' . __d('two_twi', 'Réécriture d\'url avec contrôleur Npds') . '</label>
                    </div>
                    </div>
                </div>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="consumer_key" name="consumer_key" value="' . $consumer_key . '" required="required" />
                <label for="consumer_key">' . __d('two_twi', 'Votre clef de consommateur') . '&nbsp;<span class="text-danger">*</span></label>
                <span class="help-block small">' . $consumer_key . '</span>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="consumer_secret" name="consumer_secret" value="' . $consumer_secret . '" required="required" />
                <label for="consumer_secret">' . __d('two_twi', 'Votre clef secrète de consommateur') . '&nbsp;<span class="text-danger">*</span></label>
                <span class="help-block small">' . $consumer_secret . '</span>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="oauth_token" name="oauth_token" value="' . $oauth_token . '" required="required" />
                <label for="oauth_token" >' . __d('two_twi', 'Jeton d\'accès pour Open Authentification (oauth_token)') . '&nbsp;<span class="text-danger">*</span></label>
                <span class="help-block small">' . $oauth_token . '</span>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="oauth_token_secret" name="oauth_token_secret" value="' . $oauth_token_secret . '" />
                <label for="oauth_token_secret" >' . __d('two_twi', 'Jeton d\'accès secret pour Open Authentification (oauth_token_secret)') . ' <span class="text-danger">*</span></label>
                <span class="help-block small">' . $oauth_token_secret . '</span>
            </div>
            <!--
            <tr>
            <td colspan="2"><strong>' . __d('two_twi', 'Interface bloc') . '</strong></td>
            </tr>
            <td width="30%">
            ' . __d('two_twi', 'Largeur de la tweet box') . ' <span class="text-danger">*</span> : ' . $tbox_width . '
            </td>
            <td>
            <input type="text" " size="25" maxlength="3" name="tbox_width" value="' . $tbox_width . '" />
            </td>
            </tr>
            <tr>
            <td width="30%">
            ' . __d('two_twi', 'Hauteur de la tweet box') . '</span>  <span class="text-danger">*</span> : ' . $tbox_height . '
            </td>
            <td>
            <input type="text" " size="25" maxlength="3" name="tbox_height" value="' . $tbox_height . '" />
            </td>
            </tr>
            <tr>
            <td colspan="2"><strong>Styles</strong></td>
            </tr>
            <tr>
            <td width="30%">
            <span class="' . $class_sty_2 . '">' . __d('two_twi', 'Classe de style titre') . '</span> </td><td><input type="text" size="25" maxlength="255" name="class_sty_1" value="' . $class_sty_1 . '">
            </td>
            </tr>
            <tr>
            <td width="30%">
            <span class="' . $class_sty_2 . '">' . __d('two_twi', 'Classe de style sous-titre') . '</span>
            </td>
            <td>
            <input type="text" size="25" maxlength="255" name="class_sty_2" value="' . $class_sty_2 . '" />
            </td>
            </tr>
            -->';
        echo '
            <input class="btn btn-primary my-3" type="submit" value="' . __d('two_twi', 'Enregistrez') . '" />
            <input type="hidden" name="op" value="Extend-Admin-SubModule" />
            <input type="hidden" name="ModPath" value="' . $ModPath . '" />
            <input type="hidden" name="ModStart" value="' . $ModStart . '" />
            <input type="hidden" name="subop" value="SaveSettwi" />
        </form>
        <div class="text-end">Version : ' . $npds_twi_versus . '</div>';
        $arg1 = '
            var formulid = ["twitterset"];';
        css::adminfoot('fv', '', $arg1, '');
    }
    
    function SaveSettwi($npds_twi_arti, $npds_twi_urshort, $npds_twi_post, $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $tbox_width, $tbox_height, $class_sty_1, $class_sty_2, $ModPath, $ModStart)
    {
    
        //==> modifie le fichier de configuration
        $file_conf = fopen("modules/$ModPath/config/twi_conf.php", "w+");
        $content = "<?php \n";
        $content .= "/************************************************************************/\n";
        $content .= "/* DUNE by NPDS                                                         */\n";
        $content .= "/* ===========================                                          */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* NPDS Copyright (c) 2002-" . date('Y') . " by Philippe Brunier                     */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* This program is free software. You can redistribute it and/or modify */\n";
        $content .= "/* it under the terms of the GNU General Public License as published by */\n";
        $content .= "/* the Free Software Foundation; either version 2 of the License.       */\n";
        $content .= "/*                                                                      */\n";
        $content .= "/* module npds_twi version v.1.0                                        */\n";
        $content .= "/* twi_conf.php file 2015 by Jean Pierre Barbary (jpb)                  */\n";
        $content .= "/* dev team :                                                           */\n";
        $content .= "/************************************************************************/\n";
        if (!$npds_twi_arti) $npds_twi_arti = 0;
        $content .= "\$npds_twi_arti = $npds_twi_arti; // activation publication auto des news sur twitter\n";
        if (!$npds_twi_post) $npds_twi_post = 0;
        $content .= "\$npds_twi_post = $npds_twi_post; // activation publication auto des posts sur twitter\n";
        if (!$npds_twi_urshort) $npds_twi_urshort = 0;
        $content .= "\$npds_twi_urshort = $npds_twi_urshort; // activation du raccourciceur d'url\n";
        $content .= "\$consumer_key = \"$consumer_key\"; //\n";
        $content .= "\$consumer_secret = \"$consumer_secret\"; //\n";
        $content .= "\$oauth_token = \"$oauth_token\"; //\n";
        $content .= "\$oauth_token_secret = \"$oauth_token_secret\"; //\n";
        $content .= "// interface bloc \n";
        $content .= "\$tbox_width = \"$tbox_width\"; // largeur de la tweet box\n";
        $content .= "\$tbox_height = \"$tbox_height\"; // hauteur de la tweet box\n";
        $content .= "// style \n";
        $content .= "\$class_sty_1 = \"$class_sty_1\"; // titre de la page\n";
        $content .= "\$class_sty_2 = \"$class_sty_2\"; // sous-titre de la page\n";
        $content .= "\$npds_twi_versus = \"v.1.0\";\n";
        $content .= "?>";
        fwrite($file_conf, $content);
        fclose($file_conf);
        //<== modifie le fichier de configuration
    
        //==> modifie le fichier controleur
        $file_controleur = '';
        //     if (file_exists('modules/'.$ModPath.'/config/twi_conf.php'))
        //   include ('modules/'.$ModPath.'/config/twi_conf.php');
        if ($npds_twi_urshort <> 1) {
            $file_controleur = fopen("s.php", "w+");
            $content = "<?php \n";
            $content .= "/************************************************************************/\n";
            $content .= "/* DUNE by NPDS                                                         */\n";
            $content .= "/* ===========================                                          */\n";
            $content .= "/*                                                                      */\n";
            $content .= "/* NPDS Copyright (c) 2002-" . date('Y') . " by Philippe Brunier                     */\n";
            $content .= "/*                                                                      */\n";
            $content .= "/* This program is free software. You can redistribute it and/or modify */\n";
            $content .= "/* it under the terms of the GNU General Public License as published by */\n";
            $content .= "/* the Free Software Foundation; either version 2 of the License.       */\n";
            $content .= "/*                                                                      */\n";
            $content .= "/* module npds_twi version v.1.0                                        */\n";
            $content .= "/* a.php file 2015 by Jean Pierre Barbary (jpb)                         */\n";
            $content .= "/* dev team :                                                           */\n";
            $content .= "/************************************************************************/\n";
            $content .= "\n";
            $content .= "\$fol=preg_replace ('#s\.php/(\d+)\$#','',\$_SERVER['PHP_SELF']);\n";
            $content .= "preg_match ('#/s\.php/(\d+)\$#', \$_SERVER['PHP_SELF'],\$res);\n";
            $content .= "header('Location: http://'.\$_SERVER['HTTP_HOST'].\$fol.'article.php?sid='.\$res[1]);\n";
            $content .= "?>";
            fwrite($file_controleur, $content);
            fclose($file_controleur);
    
            $file_controleur = fopen("s", "w+");
            $content = "<?php \n";
            $content .= "/************************************************************************/\n";
            $content .= "/* DUNE by NPDS                                                         */\n";
            $content .= "/* ===========================                                          */\n";
            $content .= "/*                                                                      */\n";
            $content .= "/* NPDS Copyright (c) 2002-" . date('Y') . " by Philippe Brunier                     */\n";
            $content .= "/*                                                                      */\n";
            $content .= "/* This program is free software. You can redistribute it and/or modify */\n";
            $content .= "/* it under the terms of the GNU General Public License as published by */\n";
            $content .= "/* the Free Software Foundation; either version 2 of the License.       */\n";
            $content .= "/*                                                                      */\n";
            $content .= "/* module npds_twi version v.1.0                                        */\n";
            $content .= "/* a file 2015 by Jean Pierre Barbary (jpb)                             */\n";
            $content .= "/* dev team :                                                           */\n";
            $content .= "/************************************************************************/\n";
            $content .= "\n";
            $content .= "\$fol=preg_replace ('#s/(\d+)\$#','',\$_SERVER['PHP_SELF']);\n";
            $content .= "preg_match ('#/s/(\d+)\$#', \$_SERVER['PHP_SELF'],\$res);\n";
            $content .= "header('Location: http://'.\$_SERVER['HTTP_HOST'].\$fol.'article.php?sid='.\$res[1]);\n";
            $content .= "?>";
            fwrite($file_controleur, $content);
            fclose($file_controleur);
        }
        //<== modifie le fichier controleur
    }

}