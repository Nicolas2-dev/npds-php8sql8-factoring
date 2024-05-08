<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class GroupePad extends AdminController
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
     * [pad_create description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function pad_create(int $groupe_id): void
    {
        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_pad'    => 1,
        ));

        global $aid;
        logs::Ecr_Log('security', "CreatePadWS($groupe_id) by AID : $aid", '');
    }

    /**
     * [pad_remove description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function pad_remove(int $groupe_id): void
    {
        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_pad'    => 0,
        ));

        global $aid;
        logs::Ecr_Log('security', "DeletePadWS($groupe_id) by AID : $aid", '');
    }

}