<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class GroupeWorkspace extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'groupes';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'groupes';

        $this->f_titre = __d('two_groupes', 'Gestion des groupes');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_', ''));
    }

    /**
     * [workspace_create description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function workspace_create(int $groupe_id): void
    {
        //==>creation fichier conf du groupe
        @copy('modules/f-manager/config/groupe.conf.php', 'modules/f-manager/config/groupe_'. $groupe_id  .'.conf.php');
        
        $file = file('modules/f-manager/config/groupe_'. $groupe_id  .'.conf.php');
        $file[29] = "   \$access_fma = \"$groupe_id\";\n";
        $fic = fopen('modules/f-manager/config/groupe_'. $groupe_id  .'.conf.php', "w");
        
        foreach ($file as $n => $ligne) {
            fwrite($fic, $ligne);
        }

        fclose($fic);

        include("modules/upload/config/upload.conf.php");

        if ($DOCUMENTROOT == '') {
            global $DOCUMENT_ROOT;
            if ($DOCUMENT_ROOT) {
                $DOCUMENTROOT = $DOCUMENT_ROOT;
            } else {
                $DOCUMENTROOT = $_SERVER['DOCUMENT_ROOT'];
            }
        }

        $user_dir = $DOCUMENTROOT . $racine  .'/storage/users_private/groupe/'. $groupe_id;

        // DOCUMENTS_GROUPE
        @mkdir('storage/users_private/groupe/'. $groupe_id  .'/documents_groupe');

        $repertoire = $user_dir  .'/documents_groupe';
        $directory = $racine  .'/modules/groupe/matrice/documents_groupe';
        $handle = opendir($DOCUMENTROOT . $directory);

        while (false !== ($file = readdir($handle))) {
            $filelist[] = $file;
        }
        
        asort($filelist);

        foreach ($filelist as $key => $file) {
            if ($file <> '.' and $file <> '..') {
                @copy($DOCUMENTROOT . $directory  .'/'. $file, $repertoire  .'/'. $file);
            }
        }

        closedir($handle);
        unset($filelist);

        // IMAGES_GROUPE
        @mkdir('storage/users_private/groupe/'. $groupe_id  .'/images_groupe');

        $repertoire = $user_dir  .'/images_groupe';
        $directory = $racine  .'/modules/groupe/matrice/images_groupe';
        $handle = opendir($DOCUMENTROOT . $directory);

        while (false !== ($file = readdir($handle))) {
            $filelist[] = $file;
        }

        asort($filelist);
        foreach ($filelist as $key => $file) {
            if ($file <> '.' and $file <> '..') {
                @copy($DOCUMENTROOT . $directory  .'/'. $file, $repertoire  .'/'. $file);
            }
        }
        closedir($handle);
        unset($filelist);

        @unlink('storage/users_private/groupe/'. $groupe_id  .'/delete');

        global $aid;
        logs::Ecr_Log('security', "CreateWS($groupe_id) by AID : $aid", '');
    }

}