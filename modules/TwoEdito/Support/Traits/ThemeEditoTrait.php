<?php

namespace Modules\TwoEdito\Support\Traits;

use Two\Support\Facades\View;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;


trait ThemeEditoTrait 
{

    function themedito($content)
    {
        $view = false;
        
        $theme = $this->getName();

        if (!$view) {
            if (View::exists('Themes/'.$theme.'::Partials/Edito/Editorial')) {
                $view = View::fetch('Themes/'.$theme.'::Partials/Edito/Editorial');
            } elseif (View::exists('Themes/TwoNews::Partials/Edito/Editorial')) {
                $view = View::fetch('Themes/TwoNews::Partials/Edito/Editorial');
            } else {
                echo 'Themes/'.$theme.'::Partials/Edito/Editorial manquant or Themes/TwoNews::Partials/Edito/Editorial (.php or .tpl) / not find !<br />';
                die();
            }
        }        

        if ($view) {
            ob_start();
                echo $view;
                $Xcontent = ob_get_contents();
            ob_end_clean();
    
            $npds_METALANG_words = array(
                "'!editorial_content!'i" => $content
            );
    
            return Metalang::meta_lang(Language::aff_langue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
        }
    }

}