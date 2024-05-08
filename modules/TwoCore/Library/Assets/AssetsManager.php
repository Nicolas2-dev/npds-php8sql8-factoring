<?php

declare(strict_types=1);

namespace Modules\TwoCore\Library\Assets;

use Two\Support\Arr;

use Two\Support\Str;
use InvalidArgumentException;
use Two\Support\Facades\Asset;
use Two\Foundation\Application;
use Two\Support\Facades\Config;
use Two\Support\Facades\Package;



class AssetsManager
{
 
    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * @var \Two\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Two\Config\Repository
     */
    protected $config;

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
     * [$register_js_list description]
     *
     * @var [type]
     */
    private $register_js_list  = [];



    /**
     * Mailer constructor.
     *
     * @param string $theme
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->files = $app['files'];

        $this->config = $app['config'];
    }

    /**
     * 
     * 
     * @return 
     */
    public function importHeaderCss()
    {


$register_css_list_t = collect();  

        foreach ($this->getPackageCss() as $list_css)
        {

            //vd($list_css);

            foreach ($list_css as $css) {

                // on recherche si la css a une locale si oui ont remplace %s par la locale iso ou code
                $css = $this->getLocaleForUrl($css);
//vd($css);
                // on cherche les css dans le theme et module si elle existe on la charge 
                if($this->app->files->exists(str_replace('/', '\\', $css['path']) . 'Assets' .DS . str_replace('/', '\\', $css['url']))) {

                    // si le theme ou le module et actif alors on charge la css 
                    if ($css['enabled'] == true) {
                        $this->register_css_list[$css['url']] = [
                            'url'       => asset_url($css['url'], $this->getTypePathUrl($css)), 
                            'type'      => 'css', 
                            'position'  => $css['position'], 
                            'order'     => $css['order'], 
                            'mode'      => $css['mode'],
                            'path'      => $css['path']
                        ];

                        $register_css_list_t->put($css['url'], [
                            'url'       => asset_url($css['url'], $this->getTypePathUrl($css)), 
                            'type'      => 'css', 
                            'position'  => $css['position'], 
                            'order'     => $css['order'], 
                            'mode'      => $css['mode'],
                            'path'      => $css['path']
                        ]);

                    }

                // si la css nexiste pas dans le theme ou dans le module alors ont essais de la charger sur la bas assets si elle existe on la charge
                } elseif($this->app->files->exists(base_path('assets' .DS . str_replace('/', '\\', $css['url'])))) {
                    if(!array_key_exists($css['url'], $this->register_css_list) && $register_css_list_t->has($css['url'])) {

                        $this->register_css_list[$css['url']] = [
                            'url'       => asset_url($css['url']), 
                            'type'      => 'css', 
                            'position'  => $css['position'], 
                            'order'     => $css['order'], 
                            'mode'      => $css['mode'],
                            'path'      => $css['path']
                        ];

                        $register_css_list_t->put($css['url'], [
                            'url'       => asset_url($css['url']), 
                            'type'      => 'css', 
                            'position'  => $css['position'], 
                            'order'     => $css['order'], 
                            'mode'      => $css['mode'],
                            'path'      => $css['path']
                        ]);

                    }
                }
            }
        }

        //$paths = collect();


//vd($this->register_css_list, $register_css_list_t, $register_css_list_t->count());


        foreach ($this->register_css_list as $register) {
            Asset::register($register['url'], $register['type'], $register['position'], $register['order'], $register['mode']);
        }

        // css page ref
    }


    /**
     * 
     * 
     * @return 
     */
    public function importHeaderJs()
    {
        // 
        // if ($base_js = Config::get('two_themes::BaseJs')) {

        //     $this->registerJs($base_js);
        // }

        // js page ref
    }

    /**
     * 
     * 
     * @return 
     */
    public function importFooterJs()
    {
        // if (($this->form_validation_Js == true) && ($admin_foot_js = Config::get('two_themes::AdminFootjs'))) {
        //     $this->registerJs($admin_foot_js);
        // }




        // js page ref
    }

    /**
     * [getPackageCss description]
     *
     * @return  [type]  [return description]
     */
    private function getPackageCss()
    {
        $package_list_css = [];

//         $packages = collect();

//         $path = $this->getModulesPath();

//         if ($this->files->isDirectory($path)) {
//             try {
//                 $paths = collect(
//                     $this->files->directories($path)
//                 );
//             }
//             catch (InvalidArgumentException $e) {
//                 $paths = collect();
//             }
//         }

//         $namespace = $this->getModulesNamespace();

//         $vendor = class_basename($namespace);

//         $paths->each(function ($path) use ($packages, $vendor)
//         {
//             $name = $vendor .'/' .basename($path);

//             $basename = $this->getPackageName($name);

//             $slug = (Str::length($basename) <= 3) ? Str::lower($basename) : Str::snake($basename);

// // Get the Package options from configuration.
//             $options = $this->config->get('packages.options.' .$slug, array());

//             $packages->put($slug, array(
//                 'path' => Str::finish($path, DS),

//                 //
//                 'type'      => 'module',
//                 'enabled'   => Arr::get($options, 'enabled', true),
//                 'css'       => Config::get($slug.'::CssRegister', null)
//             ));
//         });

// vd($packages);


        foreach(Package::enabled() as $package)  {
            $prepare_package_css[$package['slug']] = Config::get($package['slug'].'::CssRegister', null);
            
            if(!is_null($prepare_package_css[$package['slug']])) {
                foreach ($prepare_package_css[$package['slug']] as $key => $value) {
                    $prepare_package_css[$package['slug']][$key] = array_merge($package, $value);
                }

                $package_list_css[$package['slug']] = $prepare_package_css[$package['slug']];
            }
        }

        return $package_list_css;
    }

    /**
     * [getPackageCss description]
     *
     * @return  [type]  [return description]
     */
    private function getPackageJs()
    {
        $package_list_js = [];

        foreach(Package::all() as $package)  {
            
            $prepare_package_js[$package['slug']] = Config::get($package['slug'].'::JsRegister', null);
            
            if(!is_null($prepare_package_js[$package['slug']])) {
                foreach ($prepare_package_js[$package['slug']] as $key => $value) {
                    $prepare_package_js[$package['slug']][$key] = array_merge($package, $value);
                }

                $package_list_js[$package['slug']] = $prepare_package_js[$package['slug']];
            }
        }

        return $package_list_js;
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
            } elseif ($css['locale'] == 'code') {
                $locale = explode('_', Config::get('two_core::config.locale'));
                $css['url'] = sprintf($css['url'], $locale[0]);
            }
        }

        return $css;
    }

    /**
     * Get modules namespace.
     *
     * @return string
     */
    public function getModulesNamespace()
    {
        $namespace = $this->config->get('packages.modules.namespace', 'Modules\\');

        return rtrim($namespace, '/\\');
    }

    /**
     * Get modules path.
     *
     * @return string
     */
    public function getModulesPath()
    {
        return $this->config->get('packages.modules.path', BASEPATH .'modules');
    }

    /**
     * Get the name for a Package.
     *
     * @param  string  $package
     * @param  string  $namespace
     * @return string
     */
    protected function getPackageName($package)
    {
        if (strpos($package, '/') === false) {
            return $package;
        }

        list ($vendor, $namespace) = explode('/', $package);

        return $namespace;
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

}
