<?php
/**
 * Two - TinyMce
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

declare(strict_types=1);

namespace Shared\TinyMce;

use Two\Support\Facades\Asset;
use Two\Support\Facades\Config;


class TinyMce
{

    /**
     * path.
     *
     * @var array $config
     */
    protected $config;

    /**
     * [$viewPath description]
     *
     * @var [type]
     */
    protected $viewPath;

    /**
     * [$viewPathConf description]
     *
     * @var [type]
     */
    protected $viewPathConf;

    /**
     * [$viewPathModule description]
     *
     * @var [type]
     */
    protected $viewPathModule;

    /**
     * [$tiny_mce_theme description]
     *
     * @var [type]
     */
    protected $tiny_mce_theme;


    /**
     * Create a new Rgpd Citron instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Setup the View path.
        $this->viewPath = realpath(__DIR__) .DS .'Views' .DS .'TinyMce.php';        
        
        // Setup the View path.
        $this->viewPathConf = realpath(__DIR__) .DS .'assets' .DS .'tinymce' .DS .'themes' .DS .'advenced' .DS .'NpdsConf.php';

        // Setup the View path.
        $this->viewPathModule =  'modules' .DS .'%s' .DS .'assets' .DS .'tinymce' .DS .'tiny_mce_setup.php';
    }

    /**
     * Charge l'éditeur ... ou non
     * $Xzone = nom du textarea
     * $Xactiv = deprecated si $Xzone="custom" on utilise $Xactiv pour passer des paramètres spécifiques
     *
     * @param   [type]  $Xzone   [$Xzone description]
     * @param   [type]  $Xactiv  [$Xactiv description]
     *
     * @return  string           [return description]
     */
    public function aff_editeur($Xzone, $Xactiv): string
    {
        $tmp = '';

        if (Config::get('two_core::config.tiny_mce')) {
            static $tmp_Xzone;
            
            if ($Xzone == 'tiny_mce') {
                if ($Xactiv == 'end') {
                    if (substr( (string) $tmp_Xzone, -1) == ',') {
                        $tmp_Xzone = substr_replace( (string) $tmp_Xzone, '', -1);
                    }
                    
                    if ($tmp_Xzone) {

                        $output['tinyconf']  = $this->viewPathConf; 
                        $output['language']  = Language::language_iso(1, '', '');
                        $output['tmp_Xzone'] = $tmp_Xzone;
                        $output['module']    = Config::get('editeur.module');
                        
                        $tmp = $this->render($output, true);
                    }
                } else {
                    $tmp .= $this->renderJs();
                }
            } else {
                $tmp_Xzone .= $Xzone != 'custom' ? $Xzone .',' : $Xactiv .',';
            }
        } else {
            $tmp = '';
        }

        return $tmp;
    }

    /**
     * [renderJs description]
     *
     * @return  [type]  [return description]
     */
    function renderJs()
    {
        Asset::register(
            array(
                shared_url('assets/tinymce/tinymce.min.js', 'TinyMce'),
            ),'js', 'header', 1600);
    }

    /*
     * HTML Output for Php TinyMce
     */
    function renderTinyConf($output, $fetch)
    {
        $tinylangmenu   = Config::get('two_core::config.multi_langue') !== false ? 'npds_langue' : '';
        $relative_urls  = Config::get('editeur.tiny_mce_relurl') == "false" ? 'relative_urls : false,' : 'relative_urls : true,';      

        $auto_focus     = substr($output['tmp_Xzone'], 0, strpos($output['tmp_Xzone'], ",", 0));

        $css            = ''. asset_url('font-awesome/css/all.min.css', 'modules/TwoThemes') .', '. asset_url('bootstrap/dist/css/bootstrap.min.css', 'modules/TwoThemes') .', '. shared_url('assets/tinymce/themes/advanced/npds.css', 'TinyMce') .'';
        
        $setup = explode('+', Config::get('editeur.tiny_mce_theme'));
        
        if(array_key_exists(0, $setup)) {
            $tiny_mce_theme = $setup[0];
        } else {
            $tiny_mce_theme = Config::get('editeur.tiny_mce_theme');
        }

        if (!array_key_exists(1, $setup)) {
            $setup[1] = '';
        }

        if ($setup[1] == 'setup') {
            $external_module = $this->renderSetupModule($output['module'], true);
        }

        if($fetch) {
            ob_start();
        }

        require $this->viewPathConf;
        
        if($fetch) {
            return ob_get_clean();
        }

        return true;
    }

    /**
     * [renderSetupModule description]
     *
     * @param   [type]  $output  [$output description]
     * @param   [type]  $fetch   [$fetch description]
     *
     * @return  [type]           [return description]
     */
    function renderSetupModule($output, $fetch)
    {
        if($fetch) {
            ob_start();
        }
            
        require sprintf($this->viewPathModule, $output['module']);
        $tmp_module = ob_get_contents();
        $tmp_module .= "remove_script_host : false,\n";

        if($fetch) {
            return ob_get_clean();
        }

        return true;
    }

    /**
     * [render description]
     *
     * @param   [type]  $output  [$output description]
     * @param   [type]  $fetch   [$fetch description]
     *
     * @return  [type]           [return description]
     */
    function render($output, $fetch)
    {
        if($fetch) {
            ob_start();
        }

        require $this->viewPath;

        if($fetch) {
            return ob_get_clean();
        }

        return true;
    }

    /**
     * [setTinyMceTheme description]
     *
     * @param   [type]  $theme  [$theme description]
     *
     * @return  [type]          [return description]
     */
    public function setTinyMceTheme($theme)
    {
        $this->tiny_mce_theme = $theme;
    }

    /**
     * [getTinyMceTheme description]
     *
     * @return  [type]  [return description]
     */
    public function getTinyMceTheme()
    {
        return $this->tiny_mce_theme;
    }

}