<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class GroupeChat extends AdminController
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
     * [groupe_chat_create description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function groupe_chat_create(int $groupe_id): void
    {
        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_chat'   => 1,
        ));

        global $aid;
        logs::Ecr_Log('security', "ActivateChatWS($groupe_id) by AID : $aid", '');
    }

    /**
     * [groupe_chat_delete description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function groupe_chat_delete(int $groupe_id): void
    {
        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_chat'    => 0,
        ));

        global $aid;
        logs::Ecr_Log('security', "DesactivateChatWS($groupe_id) by AID : $aid", '');
    }

    /**
     * [bloc_groupe_create description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function bloc_groupe_create(int $groupe_id): void
    {
        // Creation bloc espace de travail user
        // On créer le bloc s'il n'existe pas déjà
        $bloc = false;
        $menu_workspace = "function#bloc_espace_groupe\r\nparams#$groupe_id,1";

        $count_lblock = DB::table('lblocks')->where('content', $menu_workspace)->count('id');
        
        if ($count_lblock == 0) {
            $count_rblock = DB::table('rblocks')->where('content', $menu_workspace)->count('id');
            
            if ($count_rblock <> 0) { 
                $bloc = true;
            }
        } else {
            $bloc = true;
        }

        if ($bloc == false) {
            DB::table('lblocks')->insert(array(
                'title'     => '',
                'content'   => $menu_workspace,
                'member'    => $groupe_id,
                'Lindex'    => 3,
                'cache'     => 0,
                'actif'     => 1,
                'css'       => 0,
                'aide'      => NULL,
            ));
        }
    }


}