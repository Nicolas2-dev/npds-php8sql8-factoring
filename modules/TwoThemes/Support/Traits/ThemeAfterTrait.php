<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Support\Traits;

use Two\Support\Facades\View;


trait ThemeAfterTrait 
{

    /**
     * 
     */
    public function headerAfter()
    {
        if (View::exists('Modules/TwoThemes::Include/HeaderAfter')) {
            echo View::fetch('Modules/TwoThemes::Include/HeaderAfter');
        }
    }

    /**
     * 
     */
    public function footerAfter() 
    {
        $theme = $this->getName();

        if (View::exists('Themes/'.$theme.'::Include/FooterAfter')) {
            echo View::fetch('Themes/'.$theme.'::Include/FooterAfter');
        } elseif (View::exists('Modules/TwoThemes::Include/FooterAfter')) {
            echo View::fetch('Modules/TwoThemes::Include/FooterAfter');
        }  
    }

}