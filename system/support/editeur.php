<?php

declare(strict_types=1);

namespace npds\system\support;

use npds\system\config\Config;
use npds\system\language\language;

class editeur
{
 
    /**
     * Charge l'éditeur ... ou non
     * $Xzone = nom du textarea
     * $Xactiv = deprecated si $Xzone="custom" on utilise $Xactiv pour passer des paramètres spécifiques
     *
     * @param   [type]  $Xzone   [$Xzone description]
     * @param   [type]  $Xactiv  [$Xactiv description]
     *
     * @return  string           [return description]
     */
    public static function aff_editeur($Xzone, $Xactiv): string
    {
        $tmp = '';

        if (Config::get('npds.tiny_mce')) {
            static $tmp_Xzone;
            
            if ($Xzone == 'tiny_mce') {
                if ($Xactiv == 'end') {
                    if (substr( (string) $tmp_Xzone, -1) == ',')
                        $tmp_Xzone = substr_replace( (string) $tmp_Xzone, '', -1);
                    
                    if ($tmp_Xzone) {
                        $tmp = "
            <script type=\"text/javascript\">
            //<![CDATA[
                document.addEventListener(\"DOMContentLoaded\", function(e) {
                tinymce.init({
                    selector: 'textarea.tin',
                    mobile: {menubar: true},
                    language : '". language::language_iso(1, '', '') ."',";

                        include("assets/shared/editeur/tinymce/themes/advanced/npds.conf.php");
                        $tmp .= '
                    });
                });
            //]]>
            </script>';
                    }
                } else {
                    $tmp .= '<script type="text/javascript" src="assets/shared/editeur/tinymce/tinymce.min.js"></script>';
                }
            } else {
                $tmp_Xzone .= $Xzone != 'custom' ? $Xzone .',' : $Xactiv .',';
            }
        } else {
            $tmp = '';
        }

        return $tmp;
    }
}
