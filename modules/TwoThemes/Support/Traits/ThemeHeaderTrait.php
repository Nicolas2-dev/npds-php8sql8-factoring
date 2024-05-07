<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Support\Traits;

use Two\Support\Facades\View;
use Two\Support\Facades\Asset;
use Two\Support\Facades\Config;
use Shared\RgpdCitron\Support\Facades\Rgpd;
use Shared\TinyMce\Support\Facades\TinyMce;
use Modules\TwoCore\Support\Facades\Metatag;
use Modules\TwoCore\Support\Facades\MetaFunction;


trait ThemeHeaderTrait 
{

    /**
     * [header description]
     *
     * @return  [type]  [return description]
     */
    public function head($head = true)
    {
        if($head) {
            echo '<head>';
        }

        // Affichage des metatags dans la balise head
        Metatag::metas();

        // 
        if (Config::get('two_core::config.gzhandler') == 1) {
            ob_start("ob_gzhandler");
        }

        // Editeur TinyMce
        if (class_exists(TinyMce::class) && method_exists(TinyMce::class, 'aff_editeur')) {
            if (Config::get('two_core::config.tiny_mce')) {
                TinyMce::aff_editeur('tiny_mce', 'begin');
            }
        }

        // Chargement du Header Rgpd Citron 
        Rgpd::headerCitron();

        // import css
        $this->importHeaderCss();

        echo Asset::position('header', 'css');

        // import js
        $this->importHeaderJs();

        echo Asset::position('header', 'js');

        // include externe JAVASCRIPT file from modules/TwoThemes/view/include or themes/ {$this->getName()} /view/include for functions, codes in the <body onload="..." event...
        $this->importExternalJavacript();

        if($head) {
            echo '</head>';
        }
    }

    /**
     * [headerHead description]
     *
     * @return  [type]  [return description]
     */
    public function header()
    {
        // referer update dans un listener et controller


        // counter udate dans un listener et controller

        MetaFunction::MM_debugON();

        // chargement de la vue du header
        $this->headerHeadTheme();

        Config::Set('two_themes::theme.header', 1);

        // include externe file from modules/TwoThemes/view/include for functions, codes ...
        $this->headerBefore();

        // include externe file from modules/TwoThemes/view/include for functions, codes ...
        $this->headerAfter();
    }

    /**
     * [headerHeadTheme description]
     *
     * @return  [type]  [return description]
     */
    public function headerHeadTheme()
    {
        if (View::exists('Modules/TwoThemes::Include/BodyOnload') or View::exists('Themes/'.$this->getName().'::Include/BodyOnload')) {
            $onload_init = ' onload="init();"';
        } else {
            $onload_init = '';
        }

        // 
        if (class_exists($this->getClassOption()) && method_exists($this->getClassOption(), 'containerStart')) {
            echo '<body' . $onload_init . '>';
            $this->getThemeOptions()->containerStart();
        } else {
            echo '<body' . $onload_init . ' class="body">';
        }

        $Start_Page = str_replace('/', '', Config::get('two_core::config.Start_Page'));
        $uri = $this->getApp('request')->getUri();

        $this->getThemeOptions()->sharesThemeOptions();

        ob_start();
            // landing page
            if (stristr($uri, $Start_Page) and View::exists('Themes/'.$this->getName().'::Partials/HeaderLanding')) {
                echo View::fetch('Themes/'.$this->getName().'::Partials/HeaderLanding');
            } elseif (View::exists('Themes/'.$this->getName().'::Partials/Header')) {
                echo View::fetch('Themes/'.$this->getName().'::Partials/Header');
            } else {
                echo View::fetch('modules/TwoThemes::Partials/Header');
            }
    
            $render = ob_get_contents();
        ob_end_clean();
    
        echo $this->metalang($this->language($render));
        // echo $this->metalang($render);
        // echo $render;

        // a metre dans le base contoller
        //$this->pdstBlock($pdst);

        // a metre dans le layout
        //$this->leftBlock($pdst);
    }

}