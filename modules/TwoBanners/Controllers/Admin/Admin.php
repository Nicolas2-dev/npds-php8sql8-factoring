<?php

namespace Modules\Two___\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Admin extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = '';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = '';

        $this->f_titre = __d('two_banners', '');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_banners', ''));
    }
}