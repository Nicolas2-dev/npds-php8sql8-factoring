<?php

namespace Modules\TwoEdito\Support\Traits;

use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;


trait ThemeEditoTrait 
{

    function themedito($content)
    {
        $inclusion = false;
        
        $theme = Theme::getName();
    
        if (file_exists("themes/" . $theme . "/view/editorial.html")) {
            $inclusion = "themes/" . $theme . "/view/editorial.html";
        } elseif (file_exists("themes/default/view/editorial.html")) {
            $inclusion = "themes/default/view/editorial.html";
        } else {
            echo 'editorial.html manquant / not find !<br />';
            die();
        }
    
        if ($inclusion) {
            ob_start();
                include($inclusion);
                $Xcontent = ob_get_contents();
            ob_end_clean();
    
            $npds_METALANG_words = array(
                "'!editorial_content!'i" => $content
            );
    
            echo Metalang::meta_lang(Language::aff_langue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
        }
    
        return $inclusion;
    }

}