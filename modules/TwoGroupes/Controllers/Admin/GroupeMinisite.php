<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class GroupeMinisite extends AdminController
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
     * [groupe_mns_create description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function groupe_mns_create(int $groupe_id): void
    {
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
        $repertoire = $user_dir  .'/mns';

        if (!is_dir($user_dir)) {
            @umask(0000);
            
            if (@mkdir($user_dir, 0777)) {
                $fp = fopen($user_dir  .'/index.html', 'w');
                fclose($fp);
                @umask(0000);
                
                if (@mkdir($repertoire, 0777)) {
                    $fp = fopen($repertoire  .'/index.html', 'w');
                    fclose($fp);

                    $fp = fopen($repertoire  .'/.htaccess', 'w');
                    @fputs($fp, 'Deny from All');
                    fclose($fp);
                }
            }
        } else {
            @umask(0000);
            if (@mkdir($repertoire, 0777)) {
                $fp = fopen($repertoire  .'/index.html', 'w');
                fclose($fp);

                $fp = fopen($repertoire  .'/.htaccess', 'w');
                @fputs($fp, 'Deny from All');
                fclose($fp);
            }
        }

        // copie de la matrice par défaut
        $directory = $racine  .'/modules/groupe/matrice/mns_groupe';
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

        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_mns'    => 1,
        ));

        global $aid;
        logs::Ecr_Log('security', "CreateMnsWS($groupe_id) by AID : $aid", '');
    }

    /**
     * [groupe_mns_delete description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function groupe_mns_delete(int $groupe_id): void
    {
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

        // Supprimer son ministe s'il existe
        if (is_dir($user_dir  .'/mns')) {
            $dir = opendir($user_dir  .'/mns');
            
            while (false !== ($nom = readdir($dir))) {
                if ($nom != '.' && $nom != '..' && $nom != '') {
                    @unlink($user_dir  .'/mns/'. $nom);
                }
            }

            closedir($dir);
            @rmdir($user_dir  .'/mns');
        }

        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_mns'    => 0,
        ));

        global $aid;
        logs::Ecr_Log('security', "DeleteMnsWS($groupe_id) by AID : $aid", '');
    }

}