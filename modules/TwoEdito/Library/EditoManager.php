<?php

declare(strict_types=1);

namespace Modules\TwoEdito\Library;

use Two\Foundation\Application;


class EditoManager
{
 
    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    public $app;

    /**
     * [$ext description]
     *
     * @var [type]
     */
    protected $ext = '.txt';


    /**
     * Créez une nouvelle instance de Metas Manager.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        //
        $this->app = $app;

    }

    /**
     * Construit l'edito
     *
     * @return  array   [return description]
     */
    public function fab_edito(): array
    {
        $cookie = $this->user()->cookieUser(3);

        $package_path = $this->package()->getModulePath('two_edito');

        if (isset($cookie)) {

            $edito_membre = $package_path .'storage' .DS. 'edito_membres' . $this->ext;

            if ($this->file()->exists($edito_membre)) {
                if ($this->file()->size($edito_membre) > 0) {
                    $Xcontents = $this->file()->sharedGet($edito_membre);
                }
            } else {
                $edito = $package_path .'storage' .DS. 'edito' . $this->ext;

                if ($this->file()->exists($edito)) {
                    if ($this->file()->size($edito) > 0) {
                        $Xcontents = $this->file()->sharedGet($edito);
                    }
                }
            }
        } else {
            $edito = $package_path .'storage' .DS. 'edito' . $this->ext;

            if ($this->file()->exists($edito)) {
                if ($this->file()->size($edito) > 0) {
                    $Xcontents = $this->file()->sharedGet($edito);
                }
            }
        }

        $affich = false;
        $Xibid = strstr($Xcontents, 'aff_jours');

        if ($Xibid) {
            parse_str($Xibid, $Xibidout);

            if (($Xibidout['aff_date'] + ($Xibidout['aff_jours'] * 86400)) - time() > 0) {
                $affichJ = false;
                $affichN = false;

                if ((NightDay() == 'Jour') and ($Xibidout['aff_jour'] == 'checked')) {
                    $affichJ = true;
                }

                if ((NightDay() == 'Nuit') and ($Xibidout['aff_nuit'] == 'checked')) { 
                    $affichN = true;
                }
            }

            $XcontentsT = substr($Xcontents, 0, strpos($Xcontents, 'aff_jours'));
            $contentJ   = substr($XcontentsT, strpos($XcontentsT, "[jour]") + 6, strpos($XcontentsT, "[/jour]") - 6);
            $contentN   = substr($XcontentsT, strpos($XcontentsT, "[nuit]") + 6, strpos($XcontentsT, "[/nuit]") - 19 - strlen($contentJ));
            $Xcontents  = '';

            if (isset($affichJ) and $affichJ === true) {
                $Xcontents = $contentJ;
            }

            if (isset($affichN) and $affichN === true) {
                $Xcontents = $contentN != '' ? $contentN : $contentJ;
            }

            if ($Xcontents != '') {
                $affich = true;
            }

        } else {
            $affich = true;
        }

        $Xcontents = $this->metalang()->meta_lang($this->language()->aff_langue($Xcontents));

        return array($affich, $Xcontents);
    }

    /**
     * [aff_edito description]
     *
     * @return  void    [return description]
     */
    public function aff_edito(): string
    {
        list($affich, $Xcontents) = $this->fab_edito();
        
        if (($affich) and ($Xcontents != '')) {
            $notitle = false;
            
            if (strstr($Xcontents, '!edito-notitle!')) {
                $notitle = 'notitle';
                $Xcontents = str_replace('!edito-notitle!', '', $Xcontents);
            }

            if (method_exists($theme_class = $this->theme(), 'themedito')) {
                return $this->theme()->themedito($Xcontents);
            } else {

                $theme_option_class = $theme_class->classOptionInstance();

                if (method_exists($theme_option_class, 'theme_centre_box')) {
                    $title = (!$notitle) ? __d('two_edito', 'EDITO') : '';
                    
                    return $theme_option_class->theme_centre_box($title, $Xcontents);
                }
            }

            if (!empty($Xcontents)) {
                $edito = '';
                if (!$notitle) {
                    $edito .= '<span class="edito">'. __d('two_edito', 'EDITO') .'</span>';
                }
                
                $edito .= $Xcontents;
                $edito .= '<br />';

                return $edito;
            }
        }
    }

    /**
     * 
     *
     * @return \Modules\TwoCore\Library\MetalangManager
     */
    public function metalang()
    {
        return $this->app['two_metalang'];
    }

    /**
     * 
     *
     * @return \Modules\TwoCore\Library\LanguageManager
     */
    public function language()
    {
        return $this->app['two_language'];
    }

    /**
     * 
     *
     * @return \Modules\TwoThemes\Library\ThemeManager
     */
    public function theme()
    {
        return $this->app['two_theme'];
    }

    /**
     * 
     *
     * @return \Modules\TwoUsers\Library\UserManager
     */
    public function user()
    {
        return $this->app['two_user'];
    }

    /**
     * 
     *
     * @return \Two\Packages\Repository
     */
    public function package()
    {
        return $this->app['packages'];
    }

    /**
     * 
     *
     * @return \Two\Filesystem\Filsystem
     */
    public function file()
    {
        return $this->app['files'];
    }

}
