<?php

namespace Modules\TwoCore\Controllers\Admin\Api;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class AlerteApi extends AdminController
{

    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        parent::initialize($request);
    }

    /**
     * [alerte_api description]
     *
     * @return  void
     */
    function alerte_api(): void 
    {
        $id = Request::input('$id');

        if (isset($id)) {

            $$row = DB::table('fonctions')->select('*')->where('fid', $id)->first();

            if (isset($$row)) {
                if (count($row) > 0)
                    $data = $row;
            }

            echo json_encode($data);
        }
    }

    /**
     * [alerte_update description]
     *
     * @return  void
     */
    function alerte_update(): void
    {
        $admin = authors::getAdmin();

        $Xadmin = base64_decode($admin);
        $Xadmin = explode(':', $Xadmin);
        $aid = urlencode($Xadmin[0]);

        $id = Request::input('$id');

        if (isset($id)) {

            $row = DB::table('fonctions')->select('*')->where('fid', $id)->first();

            DB::table('fonctions')->where('fid', $id)->update(array(
                'fdroits1_descr'    => ($aid . '|' . $row['fdroits1_descr']),
            ));
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}