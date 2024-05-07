<?php

//declare(strict_types=1);

namespace Modules\TwoThemes\Support\Traits;

use Two\Support\Str;
use Two\Support\Facades\Asset;
use Two\Support\Facades\Config;
use Two\Support\Facades\Package;


Trait ThemeAssetsTrait
{

    /**
     * [$form_validation_Js description]
     *
     * @var [type]
     */
    private $form_validation_Js = false;


    /**
     * 
     * 
     * @return 
     */
    public function importHeaderJs()
    {
        // 
        if ($base_js = Config::get('two_themes::BaseJs')) {

            $this->registerJs($base_js);
        }

        // js page ref
    }

    /**
     * 
     * 
     * @return 
     */
    public function importFooterJs()
    {
        if (($this->form_validation_Js == true) && ($admin_foot_js = Config::get('two_themes::AdminFootjs'))) {
            $this->registerJs($admin_foot_js);
        }

        //
        if ($extra_js = $this->getConfig('ExtraJs')) {

            $this->registerJs($extra_js);
        }

        // js page ref
    }


    /**
     * [registerJs description]
     *
     * @param   [type]  $_js  [$_js description]
     *
     * @return  [type]        [return description]
     */
    private function registerJs($_js)
    {
        foreach ($_js as $js) {

            if($js['type'] == 'theme') {
                $file = 'themes' .DS .$this->getName() .DS .'Assets' .DS; 
                $type = 'themes/'.$this->getHint();
            } else if($js['type'] == 'module') {
                $file = 'modules' .DS .$js['folder'] .DS .'Assets' .DS; 
                $type = 'modules/'. Str::snake($js['folder']);
            } elseif ($js['type'] == 'assets') {
                $file = $js['folder'] .DS;
                $type = 'assets';
            }

            if ($js['url'] == 'vendor/formvalidation/dist/js/locales/#locale#.min.js') {
                $locale_validation = explode('.', Config::get('two_core::config.locale'));
                $js['url'] = str_replace('#locale#', $locale_validation[0], $js['url']);
            }

            $js['url'] = str_replace('#locale#', $this->localBootrstrap(), $js['url']);

            if ($this->app->files->exists(base_path($file. $js['url']))) {

                if ($type != 'assets') {
                    $url = asset_url($js['url'], $type);
                } else {
                    $url = asset_url($js['url']);
                }

                Asset::register($url, 'js', $js['position'], $js['order'], $js['mode']);
            }
        }
    }

    /**
     * [SetFormValidation_Js description]
     *
     * @param   [type]  $render  [$render description]
     *
     * @return  [type]           [return description]
     */
    public function SetFormValidation_Js(bool $val) {
        $this->form_validation_Js = $val;
    }

    /**
     * 
     * 
     * @return 
     */
    public function importHeaderCss()
    {
        // 
        if ($base_css = Config::get('two_themes::BaseCss')) {
            $this->registerCss($base_css);
        }

        //
        if ($extra_css = $this->getConfig('ExtraCss')) {
            $this->registerCss($extra_css);
        }

        // css page ref
    }

    /**
     * [registerCss description]
     *
     * @param   [type]  $_css  [$_css description]
     *
     * @return  [type]         [return description]
     */
    private function registerCss($_css)
    {
        $tab_css = array();


            $package = Package::slugs();

vd($package);

        foreach ($_css as $css) {
            //$id++;
            if($css['type'] == 'theme') {
                $file = 'themes' .DS .$this->getName() .DS .'Assets' .DS; 
                $type = 'themes/'.$this->getHint();
            } else if($css['type'] == 'module') {
                $file = 'modules' .DS .$css['folder'] .DS .'Assets' .DS; 
                $type = 'modules/'. Str::snake($css['folder']);
            } elseif ($css['type'] == 'assets') {
                $file = $css['folder'] .DS;
                $type = 'assets';
            }

            // 
            $css['url'] = $this->ReplaceLocale($css);

            //
            //$css['url'] = str_replace('/', DS, $css['url']);




            // Theme name
            //if ($this->app->files->exists(base_path($file. $css['url']))) {
        //     if (($css['type'] == 'theme') && ($css['folder'] == $this->getName)) {   
        //         $this->registerCssRow($css, $type);
                
        //         $tab_css = array_merge(array(
        //             $css['url'] => $css 
        //         ), $tab_css);

                

        //         if (($css['url'] == 'css/locale-style-AA.css') && ($this->app->files->exists(base_path($file. $css['url'])))) {
                    
        //             $this->registerCssRow($css, $type);

        //             $tab_css = array_merge(array(
        //                 $css['url'] => $css 
        //             ), $tab_css);

        //         }

        //         if (($css['url'] == 'css/locale-print.css') && ($this->app->files->exists(base_path($file. $css['url'])))) {
                    
        //             $this->registerCssRow($css, $type);
                    

        //             $tab_css = array_merge(array(
        //                 $css['url'] => $css 
        //             ), $tab_css);

        //         }
        //     // TwoThemes
        //     } elseif (($css['type'] == 'module') && ($css['folder'] == $this->getName)) {
        //     //} elseif ($this->app->files->exists(base_path($file. $css['url']))) {
                    
        //         $this->registerCssRow($css, $type);
                

        //         $tab_css = array_merge(array(
        //             $css['url'] => $css 
        //         ), $tab_css);

        //         if (($css['url'] == 'css/style-AA.css') && ($this->app->files->exists(base_path($file. $css['url'])))) {
        //             $this->registerCssRow($css, $type);
                    

        //             $tab_css = array_merge(array(
        //                 $css['url'] => $css 
        //             ), $tab_css);

        //         }

        //         if (($css['url'] == 'css/print.css') && ($this->app->files->exists(base_path($file. $css['url'])))) {
        //             $this->registerCssRow($css, $type);
                    

        //             $tab_css = array_merge(array(
        //                 $css['url'] => $css 
        //             ), $tab_css);

        //         }
        //     // base assets
        //     } elseif($this->app->files->exists(base_path('modules' .DS .'TwoThemes' .DS .'Assets' .DS . $css['url']))) {
        //         $this->registerCssRow($css, 'modules/two_themes');
                

        //         $tab_css = array_merge(array(
        //             $css['url'] => $css 
        //         ), $tab_css);

        //     }
                
        //     if ($this->app->files->exists(base_path($file . $css['url']))) {

        //         // skin user ans config
        //         $skin = $this->getSkin();

        //         // // On regarde si le theme est skinable.
        //         if ($skin) {

        //             // skin option dans le config du theme.        
        //             $option = $this->getConfig('skinable');

        //             // on regarde et force le skin du theme.
        //             if ($option['force'] == true and $option['use'] == true) {
        //                 $skin = $option['name'];
        //             }

        //             $css['url'] = 'skins/' . Str::lower($skin) . '/bootstrap.min.css';
        //             $this->registerCssRow($css, 'modules/two_themes');
                    

        //             $tab_css = array_merge(array(
        //                 $css['url'] => $css 
        //             ), $tab_css);


        //             $css['url'] = 'skins/' . Str::lower($skin) . '/extra.css';
        //             $this->registerCssRow($css, 'modules/two_themes');
                    

        //             $tab_css = array_merge(array(
        //                 $css['url'] => $css 
        //             ), $tab_css);

        //         }
        //         //  } else {

        //         //     if ($css['url'] == 'vendor/bootstrap/dist/css/bootstrap.min.css') {
        //         //         $this->registerCssRow($css, $type);
        //         //     }

        //         //     if ($css['url'] == 'vendor/bootstrap/dist/css/extra.css') {
        //         //         $this->registerCssRow($css, $type);
        //         //     }
        //         // }

        //     //     $this->registerCssRow($css, $type);
        //     }
        }

        vd($tab_css);
    }

    /**
     * [registerCssRow description]
     *
     * @param   [type]  $css   [$css description]
     * @param   [type]  $type  [$type description]
     *
     * @return  [type]         [return description]
     */
    private function registerCssRow($css, $type)
    {
        // if ($locale) {
        //     if (($css['url'] == 'css/#locale#-style.css') || ($css['url'] == 'css/#locale#-style-AA.css') || ($css['url'] == 'css/#locale#-print.css')) {
        //         $css['url'] = str_replace('#locale#', (string) $locale, $css['url']);
        //     }
        // }

        if ($type != 'assets') {
            $url = asset_url($css['url'], $type);
        } else {
            $url = asset_url($css['url']);
        }

        Asset::register($url, 'css', $css['position'], $css['order'], $css['mode']);
    }

    /**
     * [getLocale description]
     *
     * @param   [type]  $css  [$css description]
     *
     * @return  [type]        [return description]
     */
    private function ReplaceLocale($css) 
    {
        if (!is_null($css['locale'])) {
            if ($css['locale'] == 'code') {
                $locale = explode('_', Config::get('two_core::config.locale'));
                $css['url'] = sprintf($css['url'], $locale[0]);
            } elseif ($css['locale'] == 'iso') {
                $locale = explode('.', Config::get('two_core::config.locale'));
                $css['url'] = sprintf($css['url'], $locale[0]);
            }
        }

        return $css['url'];
    }

    // if($css['type'] == 'theme') {
    //     $file = 'themes' .DS .$this->getName() .DS .'Assets' .DS; 
    //     $type = 'themes/'.$this->getHint();
    // } else if($css['type'] == 'module') {
    //     $file = 'modules' .DS .$css['folder'] .DS .'Assets' .DS; 
    //     $type = 'modules/'. Str::snake($css['folder']);
    // } elseif ($css['type'] == 'assets') {
    //     $file = $css['folder'] .DS;
    //     $type = 'assets';
    // }

    private function getPathTheme($css)
    {
        return; 
    }

    private function getPathModule($css)
    {
        return; 
    }

    private function getPathAssets($css)
    {
        return; 
    }

    /**
     * [replaceDyrectorySeparator description]
     *
     * @param   [type]  $css  [$css description]
     *
     * @return  [type]        [return description]
     */
    private function replaceDyrectorySeparator($css)
    {
        return str_replace('/', DS, $css['url']);
    }

    /**
     * 
     * 
     * @return 
     */
    private function localBootrstrap()
    {
        $locale = str_replace('_', '-', explode('.', Config::get('two_core::config.locale')));

        return $locale[0];
    }

}
