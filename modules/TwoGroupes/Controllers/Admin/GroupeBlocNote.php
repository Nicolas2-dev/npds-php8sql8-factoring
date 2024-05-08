<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class GroupeBlocNote extends AdminController
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
     * [note_create description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function note_create(int $groupe_id): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . DB::getTablePrefix() . "blocnotes (
        bnid text COLLATE utf8mb4_unicode_ci NOT NULL,
        texte text COLLATE utf8mb4_unicode_ci,
        PRIMARY KEY (bnid(32))
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        DB::statement($sql);

        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_blocnote'   => 1,
        ));

        global $aid;
        logs::Ecr_Log('security', "CreateBlocnoteWS($groupe_id) by AID : $aid", '');
    }

    /**
     * [note_remove description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function note_remove(int $groupe_id): void
    {
        DB::table('blocnotes')->where('bnid', md5("WS-BN" . $groupe_id))->delete();

        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_blocnote'   => 0,
        ));

        global $aid;
        logs::Ecr_Log('security', "DeleteBlocnoteWS($groupe_id) by AID : $aid", '');
    }

    /**
     * [workspace_archive description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function workspace_archive(int $groupe_id): void
    {
        //=> archivage espace groupe
        $fp = fopen('storage/users_private/groupe/'. $groupe_id  .'/delete', 'w');
        fclose($fp);

        //suppression fichier conf
        @unlink('modules/f-manager/config/groupe_'. $groupe_id  .'.conf.php');

        global $aid;
        logs::Ecr_Log('security', "ArchiveWS($groupe_id) by AID : $aid", '');
    }

}