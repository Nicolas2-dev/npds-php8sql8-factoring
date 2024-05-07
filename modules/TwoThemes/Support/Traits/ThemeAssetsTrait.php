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
     * [$register_css_list description]
     *
     * @var [type]
     */
    private $register_css_list  = [];


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

        // foreach ($this->getPackageCss() as $list_css)
        // {
        //     foreach ($list_css as $css) {

        //         // on recherche si la css a une locale si oui ont remplace %s par la locale iso ou code
        //         $css = $this->getLocaleForUrl($css);

        //         // on cherche les css dans le theme et module si elle existe on la charge 
        //         if($this->app->files->exists($css['path'] . 'Assets/' . $css['url'])) {

        //             $this->register_css_list[$css['url']] = [
        //                 'url'       => asset_url($css['url'], $this->getTypePathUrl($css)), 
        //                 'type'      => 'css', 
        //                 'position'  => $css['position'], 
        //                 'order'     => $css['order'], 
        //                 'mode'      => $css['mode']
        //             ];
        //         // si la css nexiste pas dans le theme ou dans le module alors ont essais de la charger sur la bas assets si elle existe on la charge
        //         } elseif($this->app->files->exists(base_path('assets/' . $css['url']))) {
        //             if(!array_key_exists($css['url'], $this->register_css_list)) {

        //                 $this->register_css_list[$css['url']] = [
        //                     'url'       => asset_url($css['url']), 
        //                     'type'      => 'css', 
        //                     'position'  => $css['position'], 
        //                     'order'     => $css['order'], 
        //                     'mode'      => $css['mode']
        //                 ];
        //             }
        //         }
        //     }
        // }

        // foreach ($this->register_css_list as $register) {
        //     Asset::register($register['url'], $register['type'], $register['position'], $register['order'], $register['mode']);
        // }

        // css page ref
    }

    /**
     * [getPackageCss description]
     *
     * @return  [type]  [return description]
     */
    private function getPackageCss()
    {
        $package_list_css = [];

        foreach(Package::all() as $package)  {
            
            $prepare_package_css[$package['slug']] = Config::get($package['slug'].'::cssregister', null);
            
            if(!is_null($prepare_package_css[$package['slug']])) {
                foreach ($prepare_package_css[$package['slug']] as $cc => $gg) {
                    $prepare_package_css[$package['slug']][$cc] = array_merge($package, $gg);
                }

                $package_list_css[$package['slug']] = $prepare_package_css[$package['slug']];
            }
        }

        return $package_list_css;
    }

    /**
     * [getTypePathUrl description]
     *
     * @param   [type]  $css  [$css description]
     *
     * @return  [type]        [return description]
     */
    private function getTypePathUrl($css)
    {
        if($css['type'] == 'theme') {
            $type = 'themes/'.Str::snake($css['slug']);
        } elseif($css['type'] == 'module') {
            $type = 'modules/'.$css['slug'];
        }

        return $type;
    }

    /**
     * [getLocaleForUrl description]
     *
     * @param   [type]  $css  [$css description]
     *
     * @return  [type]        [return description]
     */
    private function getLocaleForUrl($css)
    {
        if (!is_null($css['locale'])) {
            if ($css['locale'] == 'iso') {
                $locale = explode('.', Config::get('two_core::config.locale'));
                $css['url'] = sprintf($css['url'], $locale[0]);
                //vd($css['url']);
            } elseif ($css['locale'] == 'code') {
                $locale = explode('_', Config::get('two_core::config.locale'));
                $css['url'] = sprintf($css['url'], $locale[0]);
                //vd($css['url']);
            }
        }

        return $css;
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
        foreach ($_css as $css) {

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

            $locale = explode('_', Config::get('two_core::config.locale'));

            if ($this->app->files->exists(base_path($file. str_replace('/', DS, str_replace('#locale#', $locale[0], $css['url']))))) {
                
                $this->registerCssRow($css, $type, $locale[0]);

                if (($css['url'] == 'css/locale-style-AA.css') && ($this->app->files->exists(base_path($file. str_replace('/', DS, str_replace('#locale#', $locale[0], $css['url'])))))) {
                    $this->registerCssRow($css, $type, $locale[0]);
                }

                if (($css['url'] == 'css/locale-print.css') && ($this->app->files->exists(base_path($file. str_replace('/', DS, str_replace('#locale#', $locale[0], $css['url'])))))) {
                    $this->registerCssRow($css, $type, $locale[0]);
                }
            } elseif ($this->app->files->exists(base_path($file. str_replace('/', DS, $css['url'])))) {
                $this->registerCssRow($css, $type);

                if (($css['url'] == 'css/style-AA.css') && ($this->app->files->exists(base_path($file. str_replace('/', DS, $css['url']))))) {
                    $this->registerCssRow($css, $type);
                }

                if (($css['url'] == 'css/print.css') && ($this->app->files->exists(base_path($file. str_replace('/', DS, $css['url']))))) {
                    $this->registerCssRow($css, $type);
                }
            } elseif ($this->app->files->exists(base_path('modules' .DS .'TwoThemes' .DS .'Assets' .DS . str_replace('/', DS, $css['url'])))) {
                    $this->registerCssRow($css, 'modules/two_themes');

            } else {
                if ($this->app->files->exists(base_path($file . str_replace('/', DS, $css['url'])))) {

                    // skin user ans config
                    $skin = $this->getSkin();

                    // On regarde si le theme est skinable.
                    if ($skin) {

                        // skin option dans le config du theme.        
                        $option = $this->getConfig('skinable');

                        // on regarde et force le skin du theme.
                        if ($option['force'] == true and $option['use'] == true) {
                            $skin = $option['name'];
                        }

                        if ($css['url'] == 'skins/' . Str::lower($skin) . '/bootstrap.min.css') {
                            $css['url'] = 'skins/' . Str::lower($skin) . '/bootstrap.min.css';
                            $this->registerCssRow($css, $type);
                        }

                        if ($css['url'] == 'skins/' . Str::lower($skin) . '/extra.css') {
                            $css['url'] = str_replace('vendor/bootstrap/dist/css/extra.css', 'skins/' . Str::lower($skin) . '/extra.css', $css['url']);
                            $this->registerCssRow($css, $type);
                        }
                    } else {

                        if ($css['url'] == 'vendor/bootstrap/dist/css/bootstrap.min.css') {
                            $this->registerCssRow($css, $type);
                        }

                        if ($css['url'] == 'vendor/bootstrap/dist/css/extra.css') {
                            $this->registerCssRow($css, $type);
                        }
                    }

                    $this->registerCssRow($css, $type);
                }
            }
        }
    }

    /**
     * [registerCssRow description]
     *
     * @param   [type]  $css   [$css description]
     * @param   [type]  $type  [$type description]
     *
     * @return  [type]         [return description]
     */
    private function registerCssRow($css, $type, $locale = false)
    {
        if ($locale) {
            if (($css['url'] == 'css/#locale#-style.css') || ($css['url'] == 'css/#locale#-style-AA.css') || ($css['url'] == 'css/#locale#-print.css')) {
                $css['url'] = str_replace('#locale#', (string) $locale, $css['url']);
            }
        }

        if ($type != 'assets') {
            $url = asset_url($css['url'], $type);
        } else {
            $url = asset_url($css['url']);
        }

        Asset::register($url, 'css', $css['position'], $css['order'], $css['mode']);
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
