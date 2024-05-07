<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Support\Traits;

use Two\Support\Facades\View;


trait ThemeBeforeTrait 
{

    /**
     * 
     */
    public function headerBefore() 
    {
        if (View::exists('Modules/TwoThemes::Include/HeaderBefore')) {
            echo View::fetch('Modules/TwoThemes::Include/HeaderBefore');
        }
    }

    /**
     * 
     */
    public function footerBefore() 
    {
        $theme = $this->getName();

        if (View::exists('Themes/'.$theme.'::Include/FooterBefore')) {
            echo View::fetch('Themes/'.$theme.'::Include/FooterBefore');
        } elseif (View::exists('Modules/TwoThemes::Include/FooterBefore')) {
            echo View::fetch('Modules/TwoThemes::Include/FooterBefore');
        }  
    }

}