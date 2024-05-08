<?php

namespace Modules\TwoModules\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Modules extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'modules';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'modules';

        $this->f_titre = __d('two_modules', 'Gestion, Installation Modules');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        include("themes/default/header.php");

        GraphicAdmin(manuel('modules'));
        adminhead($f_meta_nom, $f_titre);
        
        $handle = opendir('modules');
        $modlist = '';
        while (false !== ($file = readdir($handle))) {
            if (!@file_exists("modules/$file/kernel")) {
                if (is_dir("modules/$file") and ($file != '.') and ($file != '..')) {
                    $modlist .= "$file ";
                }
            }
        }
        closedir($handle);
        $modlist = explode(' ', rtrim($modlist));
        
        foreach (DB::table('modules')->select('mnom')->get() as $module) {
            if (!in_array($module['mnom'], $modlist)) {
                DB::table('modules')->where('mnom', $module['mnom'])->delete();
            }
        }
        
        foreach ($modlist as $value) {
            $moexiste = DB::table('modules')->select('mnom')->where('mnom', $value)->first();
        
            if ($moexiste !== 1) {
                DB::table('modules')->insert(array(
                    'mnom'       => $value,
                    'minstall'   => 0,
                ));
            }
        }
        
        echo '
            <hr />
            <h3>'. __d('two_modules', 'Les modules') .'</h3>
            <table id="tad_modu" data-toggle="table" data-striped="false" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
                <thead>
                    <tr>
                        <th data-align="center" class="n-t-col-xs-1"><img class="adm_img" src="assets/images/admin/module.png" alt="icon_module" /></th>
                        <th data-sortable="true">'. __d('two_modules', 'Nom') .'</th>
                        <th data-align="center" class="n-t-col-xs-2" >'. __d('two_modules', 'Fonctions') .'</th>
                    </tr>
                </thead>
                <tbody>';
        
        $modules = DB::table('modules')->select('mid', 'mnom', 'minstall')->orderBy('mid')->get();
        
        foreach ($modules as $module) {
            $icomod = '';
            $clatd = '';
        
            $icomod = file_exists("modules/" . $module["mnom"] . "/" . $module["mnom"] . ".png") ?
                '<img class="adm_img" src="modules/'. $module["mnom"] .'/'. $module["mnom"] .'.png" alt="icon_'. $module["mnom"] .'" title="" />' :
                '<img class="adm_img" src="assets/images/admin/module.png" alt="icon_module" title="" />';
        
            if ($module["minstall"] == 0) {
                $status_chngac = file_exists("modules/" . $module["mnom"] . "/install.conf.php") 
                    ? '<a class="text-success" href="'. site_url('admin.php?op=Module-Install&amp;ModInstall='. $module["mnom"] .'&amp;subop=install') .'" ><i class="fa fa-compress fa-lg"></i><i class="fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. __d('two_modules', 'Installer le module') .'" data-bs-toggle="tooltip"></i></a>' 
                    : '<a class="text-success" href="'. site_url('admin.php?op=Module-Install&amp;ModInstall='. $module["mnom"] .'&amp;subop=install') .'"><i class="fa fa-check fa-lg"></i><i class="fa fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. __d('two_modules', 'Pas d\'installeur disponible') .' '. __d('two_modules', 'Marquer le module comme installé') .'" data-bs-toggle="tooltip"></i></a>';
                $clatd = 'table-danger';
            } else {
                $status_chngac =  file_exists("modules/" . $module["mnom"] . "/install.conf.php") 
                    ? '<a class="text-danger" href="'. site_url('admin.php?op=Module-Install&amp;ModDesinstall='. $module["mnom"]) .'" ><i class="fa fa-expand fa-lg"></i><i class="fa fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. __d('two_modules', 'Désinstaller le module') .'" data-bs-toggle="tooltip"></i></a>' 
                    : '<a class="text-danger" href="'. site_url('admin.php?op=Module-Install&amp;ModDesinstall='. $module["mnom"]) .'" ><i class="fa fa fa-ban fa-lg"></i><i class="fa fa fa-puzzle-piece fa-2x fa-rotate-90" title="'. __d('two_modules', 'Marquer le module comme désinstallé') .'" data-bs-toggle="tooltip"</i></a>';
                $clatd = 'table-success';
            }
        
            echo '
                    <tr>
                        <td class="'. $clatd .'">'. $icomod .'</td>
                        <td class="'. $clatd .'">'. $module["mnom"] .'</td>
                        <td class="'. $clatd .'">'. $status_chngac .'</td>
                    </tr>';
        }
        
        echo '
                </tbody>
            </table>';
            
        css::adminfoot('', '', '', '');

        return $this->createView()
            ->shares('title', __d('two_', ''));
    }
}