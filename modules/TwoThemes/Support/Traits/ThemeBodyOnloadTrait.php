<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Support\Traits;

use Two\Support\Facades\View;


trait ThemeBodyOnloadTrait 
{


    function importExternalJavacript()
    {
        // 
        $this->bodyOnloadDefault();

        // 
        $this->bodyOnloadTheme();
    }


    /**
     * 
     */
    public function bodyOnloadDefault()
    {
        if (View::exists('Modules/TwoThemes::Include/BodyOnload')) {
            echo $this->BodyOnloadJavaStart();
            echo View::fetch('Modules/TwoThemes::Include/BodyOnload');
            echo $this->BodyOnloadJavaEnd();
        }
    }

    /**
     * 
     */
    public function bodyOnloadTheme()
    {
        $theme = $this->getName();
        
        if (View::exists('Themes/'.$theme.'::Include/BodyOnload')) {
            echo $this->BodyOnloadJavaStart();
            echo View::fetch('Themes/'.$theme.'::Include/BodyOnload');
            echo $this->BodyOnloadJavaEnd();
        }
    }

    /**
     * 
     */
    private function bodyOnloadJavaStart()
    {
        return '
        <script type="text/javascript">
            //<![CDATA[
                function init() {';
    }

    /**
     * 
     */
    private function bodyOnloadJavaEnd()
    {
        return '
                }
            //]]>
        </script>';
    }

}