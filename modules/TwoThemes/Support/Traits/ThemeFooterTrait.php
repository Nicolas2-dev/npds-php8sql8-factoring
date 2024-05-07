<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Support\Traits;

use Two\Support\Facades\View;
use Two\Support\Facades\Asset;
use Two\Support\Facades\Config;
use Shared\RgpdCitron\Support\Facades\Rgpd;
use Shared\TinyMce\Support\Facades\TinyMce;
use Modules\TwoCore\Support\Facades\MetaFunction;


trait ThemeFooterTrait 
{

    /**
     * [footmsg description]
     *
     * @return  [type]  [return description]
     */
    public function footmsg() 
    {
        $foot = '<p align="center">';
    
        $config = $this->getConfig('footer');

        if ($config['force'] == true) {
            $foot .= stripslashes($config['foot1']) . '<br />';
        } elseif ($foot1 = Config::get('two_core::config.foot1')) {
            $foot .= stripslashes($foot1) . '<br />';
        }
    
        if ($config['force'] == true) {
            $foot .= stripslashes($config['foot2']) . '<br />';
        } else if ($foot2 = Config::get('two_core::config.foot2')) {
            $foot .= stripslashes($foot2) . '<br />';
        }
    
        if ($config['force'] == true) {
            $foot .= stripslashes($config['foot3']) . '<br />';
        } else if ($foot3 = Config::get('two_core::config.foot3')) {
            $foot .= stripslashes($foot3) . '<br />';
        }
    
        if ($config['force'] == true) {  
            $foot .= stripslashes($config['foot4']);
        } else if ($foot4 = Config::get('two_core::config.foot4')) {  
            $foot .= stripslashes($foot4);
        }
    
        $foot .= '</p>';
    
        return $this->language($foot);
    }

    /**
     * [foot description]
     *
     * @return  [type]  [return description]
     */
    public function foot() 
    {
        //
        if (class_exists($this->getClassOption()) && method_exists($this->getClassOption(), 'containerEnd')) {
            $this->getThemeOptions()->containerEnd();
        }

        // 
        $msg_foot = $this->footmsg();

        ob_start();
            if (View::exists('Themes/'.$this->getName().'::Partials/Footer')) {
                echo View::fetch('Themes/'.$this->getName().'::Partials/Footer', compact('msg_foot'));
            } else {
                echo View::fetch('modules/TwoThemes::Partials/Footer', compact('msg_foot'));
            }
            $Xcontent = ob_get_contents();
        ob_end_clean();

        echo $this->metalang($this->language($Xcontent));
        // echo $this->metalang($Xcontent);
        // echo $Xcontent;

        // Chargement du Footer Rgpd Citron
        Rgpd::footerCitron();

        // import js
        $this->importFooterJs();

        // 
        echo Asset::position('footer', 'js');

        // 
        echo Asset::position('footer_two', 'js');

        // sitemap dans un listener et controller

        MetaFunction::MM_debugOFF();
    }

    /**
     * [footer description]
     *
     * @return  [type]  [return description]
     */
    public function footer()
    {
        // Editeur TinyMce
        if (class_exists(TinyMce::class) && method_exists(TinyMce::class, 'aff_editeur')) {
            if (Config::get('two_core::config.tiny_mce')) {
                TinyMce::aff_editeur('tiny_mce', 'end');
            }
        }

        // include externe file from modules/TwoThemes/view/include include for functions, codes ...
        $this->footerBefore();

        // chargement de la vue du footer
        $this->foot();

        // include externe file from themes/ {$this->getName()} /view/include or modules/TwoThemes/view/include include for functions, codes ...
        $this->footerAfter();
    }

}