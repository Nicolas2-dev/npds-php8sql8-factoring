<?php

namespace Modules\TwoCore\Core;

use Two\Support\Str;
use Two\Http\Request;
use Two\Support\Facades\DB;
use Two\Support\Facades\Url;
use Two\Support\Facades\View;
use Two\Support\Facades\Config;
use Two\Support\Facades\Package;
use Modules\TwoCore\Core\BaseController;
use Modules\TwoCore\Support\Facades\Language;


abstract class AdminController extends BaseController
{

    //protected $guard = 'admin';

    /**
     * Le thème actuellement utilisé.
     *
     * @var string
     */
    //protected $theme = 'TwoBackend';
    protected $theme = 'TwoFrontend';

    /**
     * La mise en page actuellement utilisée.
     *
     * @var string
     */
    protected $layout = 'Default';

    /**
     * 
     */
    protected $filemanager = false;

    /**
     * 
     */
    protected $f_meta_nom;

    /**
     * 
     */
    protected $f_titre;

    /**
     * 
     */
    protected $hlpfile = 'admin';

    /**
     * 
     */
    protected $short_menu_admin = false;

    /**
     * 
     */
    protected $admin_head = true;


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        parent::initialize($request);

        $filemanager = Config::get('two_core::filemanager.filemanager', false);

        if (!isset($filemanager)) {
            $this->filemanager = $filemanager;
        }

        $this->GraphicAdmin();
    }

    /**
     * [admindroits description]
     *
     * @param   [type]  $aid         [$aid description]
     * @param   [type]  $f_meta_nom  [$f_meta_nom description]
     *
     * @return  [type]               [return description]
     */
    function admindroits($aid)
    {
        global $radminsuper;
    
        $res = DB::table('authors')
            ->select('fnom', 'radminsuper')
            ->leftJoin('droits', 'authors.aid', '=', 'droits.d_aut_aid')
            ->leftJoin('fonctions', 'droits.d_fon_fid', '=', 'fonctions.fdroits1')
            ->where('aid', $aid)
            ->get();
        
        $foncts = array();
        $supers = array();
    
        foreach($res as $data) {
            $foncts[] = $data->fnom;
            $supers[] = $data->radminsuper;
        }
    
        if ((!in_array('1', $supers)) and (!in_array($this->f_meta_nom, $foncts))) {
            Url::redirect('die.php?op=admin');
        }
    
        $radminsuper = $supers[0];
    }
    
    /**
     * [adminhead description]
     *
     * @param   [type]  $f_meta_nom  [$f_meta_nom description]
     * @param   [type]  $f_titre     [$f_titre description]
     * @param   [type]  $null        [$null description]
     *
     * @return  [type]               [return description]
     */
    function adminHead()
    {
        $res = DB::table('fonctions')
                    ->select('furlscript', 'ficone'. 'fpackage')
                    ->where('fnom', $this->f_meta_nom)
                    ->where('fenabled', '=', 1)
                    ->first();
    
        $admf_ext   = Config::get('two_core::config.admf_ext');
        $adminimg   = Config::get('two_core::config.adminimg');

        $package    = Package::where('basename', $res->fpackage);
        $namespace  = $this->getPackagehint($package['name']);

        $package_core    = Package::where('basename', Config::get('two_core::config.packageTwoThemes', 'TwoThemes'));
        $namespace_core  = $this->getPackagehint($package_core['name']);

        //on cherche l'image dans le package et on la charge 
        if (($package['enabled']) && (app('files')->exists($package['path'] . 'Assets/' . $adminimg . $res->ficone . '.' . $admf_ext))) {

            $img_adm = '<img src="' . asset_url($adminimg . $res->ficone . '.' . $admf_ext, $namespace) . '" class="vam " alt="' . $this->f_titre . '" />';
        
        //sinon on cherche l'image dans le package TwoCor et on la charge
        } elseif (($package['enabled']) && (app('files')->exists($package_core['path'] . 'Assets/' . $adminimg . $res->ficone . '.' . $admf_ext))) {
            
            $img_adm = '<img src="' . asset_url($adminimg . $res->ficone . '.' . $admf_ext, $namespace_core) . '" class="vam " alt="' . $this->f_titre . '" />';
        }

        return '<div id="adm_workarea" class="adm_workarea">
            ' . "\n" . '<h2><a ' . $res->furlscript . ' >' . (!isset($img_adm) ?: $img_adm) . '&nbsp;' . $this->f_titre . '</a></h2>';
    }

    function GraphicAdmin()
    {
        global $aid; 
        
        // provisoire pour le dev gestion login admin pas fait encore !!!!!
        $aid = 'Root';
        
        $bloc_foncts = '';
        $bloc_foncts_A = '';
    
        $Q = DB::table('authors')->select('radminsuper')->where('aid', $aid)->limit(1)->first();
    
        // voir note.txt pour alerte !!!
        // provisoire rappel !!!!!

        // construction des blocs menu : selection de fonctions actives ayant une interface graphique de premier niveau 
        // et dont l'administrateur connecté en posséde les droits d'accès on prend tout ce qui a une interface 
        $R = (($Q->radminsuper == 1) 
            ? DB::table('fonctions')
                    ->select('*')
                    ->where('finterface', '=', 1)
                    ->where('fetat', '!=', 0)
                    ->where('fenabled', '=', 1)
                    ->orderBy('fcategorie')
                    ->orderBy('fordre')
                    ->get()

            : DB::table('fonctions')
                    ->select('*')
                    ->leftJoin('droits', 'fonctions.droits1', '=', 'droits.d_fon_fid')
                    ->leftJoin('authors', 'droits.d_aut_aid', '=', 'authors.aid')
                    ->where('fonctions.finterface', 1)
                    ->where('fonctions.fetat', '!=', 0)
                    ->where('fenabled', '=', 1)
                    ->where('droits.d_aut_aid', $aid)
                    ->where('droits.d_droits', 'REGEXP', '^1')
                    ->orderBy('fonctions.fcategorie')
                    ->orderBy('fonctions.fordre')
                    ->get()
        );

        $j = 0;
        foreach ($R as $SAQ) {

            $cat[]      = $SAQ->fcategorie;
            $cat_n[]    = $SAQ->fcategorie_nom;
            $fid_ar[]   = $SAQ->fid;

            $admf_ext   = Config::get('two_core::config.admf_ext');
            $adminimg   = Config::get('two_core::config.adminimg');

            if ($SAQ->fcategorie == 6 or $SAQ->fcategorie == 9) {

                $package    = Package::where('basename', $SAQ->fpackage);
                $namespace  = $this->getPackagehint($package['name']);

                $package_core    = Package::where('basename', Config::get('two_core::config.packageTwoThemes', 'TwoThemes'));
                $namespace_core  = $this->getPackagehint($package_core['name']);

                if ($package['enabled'] && app('files')->exists($package['path'] . 'Assets/' . $adminimg . $SAQ->ficone . '.' . $admf_ext)) {
                    $adminico = asset_url($adminimg . $SAQ->ficone . '.' . $admf_ext, $namespace);
                } else {
                    if ($package['enabled'] && app('files')->exists($package['path'] . 'Assets/' . $adminimg . 'module' . '.' . $admf_ext)) {
                        $adminico = asset_url($adminimg . 'module' . '.' . $admf_ext, $namespace);
                    } elseif($package['enabled']) {
                        $adminico = asset_url($adminimg . 'module' . '.' . $admf_ext, $namespace_core);
                    }
                }
            } else {

                if (!is_null($SAQ->fpackage)) {

                    $package    = Package::where('basename', $SAQ->fpackage);
                    $namespace  = $this->getPackagehint($package['name']);

                    $package_core    = Package::where('basename', Config::get('two_core::config.packageTwoThemes', 'TwoThemes'));
                    $namespace_core  = $this->getPackagehint($package_core['name']);

                    if (app('files')->exists($package['path'] . 'Assets/' . $adminimg . $SAQ->ficone . '.' . $admf_ext)) {
                        $adminico = asset_url($adminimg . $SAQ->ficone . '.' . $admf_ext, $namespace);
                    } elseif (app('files')->exists($package_core['path'] . 'Assets/' . $adminimg . $SAQ->ficone . '.' . $admf_ext)) {
                        $adminico = asset_url($adminimg . $SAQ->ficone . '.' . $admf_ext, $namespace_core);
                    }
                }
            }

            if ($SAQ->fcategorie == 9) {
                
                $data = array(
                    'fretour_h'  => $SAQ->fretour_h,
                    'fid'        => $SAQ->fid,
                    'furlscript' => $SAQ->furlscript,
                    'fretour'    => $SAQ->fretour,
                    'adminico'   => $adminico,
                );
                
                if (preg_match('#mes_npds_\d#', $SAQ->fnom)) {
                    
                    $adm_lecture = explode('|', $SAQ->fdroits1_descr);
                    
                    if (!in_array($aid, $adm_lecture, true)) {
                        $bloc_foncts_A .= View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Bloc_Fonctions_Href', $data);
                    }
                } else {
                    $bloc_foncts_A .= View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Bloc_Fonctions_Href', $data);
                 
                }
                array_pop($cat_n);

            } else {
                // lancement du FileManager 
                $blank = '';
                if ($SAQ->fnom == "FileManager" && $package['enabled']) {
                    if (app('files')->exists($package['path'] .'config/' . strtolower($aid) . '.conf.php')) {
                        $filemanager = app('files')->getRequire($package['path'] .'config/' . strtolower($aid) . '.conf.php');

                        if (!$filemanager['NPDS_fma']) {
                            $blank = ' target="_blank"';
                        }
                    }
                }

                $title = $fnom_affich = Language::aff_langue($SAQ->fnom_affich);
                $fcategorie_nom       = Language::aff_langue($SAQ->fcategorie_nom);

                $ul_f = '';
                if ($j !== 0) {
                    $ul_f = View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Js/Admin_Graphic_Tog', ['id' => strtolower(substr($cat_n[$j-1], 0, 3))]);
                }

                if ($j == 0) {
                    $bloc_foncts .= View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Admin_Graphic_Row_Link', 
                        [
                            // 
                            'aff_ul_o'           => true,
                            'fcategorie_nom_tog' => strtolower(substr($fcategorie_nom, 0, 3)), 
                            'fcategorie_nom'     => $fcategorie_nom, 

                            // 
                            'aff_lic_c'          => true,
                            'enabled'            => $package['enabled'],
                            'title'              => $title,
                            'fid'                => $SAQ->fid,
                            'furlscript'         => $SAQ->furlscript,
                            'fnom_affich'        => $fnom_affich,
                            'blank'              => $blank,
                            'adminico'           => $adminico,
                            'admingraphic'       => Config::get('two_core::config.admingraphic'), 
                        ]
                    );

                } else {
                    if ($j > 0 and $cat[$j] > $cat[$j - 1]) { 

                        $bloc_foncts .= View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Admin_Graphic_Row_Link', 
                            [
                                // 
                                'aff_ul_f'           => true,
                                'ul_f'               => $ul_f,

                                // 
                                'aff_ul_o'           => true,
                                'fcategorie_nom_tog' => strtolower(substr($fcategorie_nom, 0, 3)),
                                'fcategorie_nom'     => $fcategorie_nom,

                                // 
                                'aff_lic_c'          => true,
                                'enabled'            => $package['enabled'],
                                'title'              => $title,
                                'fid'                => $SAQ->fid,
                                'furlscript'         => $SAQ->furlscript,
                                'fnom_affich'        => $fnom_affich,
                                'blank'              => $blank,
                                'adminico'           => $adminico,
                                'admingraphic'       => Config::get('two_core::config.admingraphic'), 
                            ]
                        );

                    } else {
                        $bloc_foncts .= View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Admin_Graphic_Row_Link', 
                            [                                
                                //
                                'aff_lic_c'     => true,
                                'enabled'       => $package['enabled'],
                                'title'         => $title,
                                'fid'           => $SAQ->fid,
                                'furlscript'    => $SAQ->furlscript,
                                'fnom_affich'   => $fnom_affich,
                                'blank'         => $blank,
                                'adminico'      => $adminico,
                                'admingraphic'  => Config::get('two_core::config.admingraphic'), 
                            ]
                        );
                    }
                }
            }
            $j++;
        }
    
        if (isset($cat_n)) {
            $ca = array();
            $ca = array_unique($cat_n);
            $ca = array_pop($ca);

            $bloc_foncts .= View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Js/Admin_Graphic_Tog', ['id' => strtolower(substr($ca, 0, 3))]);
        }
    
        View::share('admin_menu',  View::fetch('Modules/TwoCore::Partials/Admin/GraphicAdmin/Admin_Graphic', 
            [
                'hlpfile'           => $this->hlpfile, 
                'short_menu_admin'  => $this->short_menu_admin, 
                'bloc_foncts'       => $bloc_foncts
            ]
        ));
    
        return ($Q->radminsuper);
    }

    /**
     * [getPackageHint description]
     *
     * @param   [type]  $package  [$package description]
     *
     * @return  [type]            [return description]
     */
    protected function getPackageHint($package)
    {
        if (strpos($package, '/') === false) {
            return $package;
        }
    
        list ($vendor, $namespace) = explode('/', $package);
    
        $slug = (Str::length($namespace) <= 3) ? Str::lower($namespace) : Str::snake($namespace);
    
        return Str::lower($vendor) . '/' . $slug;
    }

}