<?php

namespace Modules\TwoBlocks\Support\Traits;

use Two\Support\Facades\View;
use Modules\TwoCore\Support\Facades\Metalang;


trait ThemeSideboxTrait 
{

    /**
     * [themesidebox description]
     *
     * @param   [type]  $title    [$title description]
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    function themesidebox($title, $content)
    {
        global $B_class_title, $B_class_content, $bloc_side, $htvar;
        
        $theme = $this->getTheme();

        $view = false;

        if (View::exists('Themes/'.$theme.'::Partials/Sidebox/Block_Right') 
            and ($bloc_side == "RIGHT")) {
            $view = View::fetch('Themes/'.$theme.'::Partials/Sidebox/Block_Right');
        }
        
        if (View::exists('Themes/'.$theme.'::Partials/Sidebox/Block_Left') 
            and ($bloc_side == "LEFT")) {
            $view = View::fetch('Themes/'.$theme.'::Partials/Sidebox/Block_Left');
        }

        if (!$view) {
            if (View::exists('Themes/'.$theme.'::Partials/Sidebox/Block')) {
                $view = View::fetch('Themes/'.$theme.'::Partials/Sidebox/Block');
            } else {
                echo 'Themes/'.$theme.'::partials/Sidebox/Block manquant (.php or .tpl) / not find !<br />';
                die();
            }
        }

        ob_start();
            echo $view;
            $render = ob_get_contents();
        ob_end_clean();
        
        if ($title == 'no-title') {
            $render = str_replace('<div class="LB_title">!B_title!</div>', '', $render);
            $title = '';
        }

        $npds_METALANG_words = array(
            "'!B_title!'i"          => $title,
            "'!B_class_title!'i"    => $B_class_title,
            "'!B_class_content!'i"  => $B_class_content,
            "'!B_content!'i"        => $content
        );
        
        echo $htvar;
       
        echo Metalang::meta_lang(
            preg_replace(
                array_keys($npds_METALANG_words),
                array_values($npds_METALANG_words), 
                $render
            )
        );
        
        echo '</div>';
    }

}