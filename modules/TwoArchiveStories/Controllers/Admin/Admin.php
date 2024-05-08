<?php

namespace Modules\TwoArchiveStories\Controllers\Admin;


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
    protected $hlpfile = 'mod-archive-stories';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'archive-stories';

        $this->f_titre = __d('two_archive_storie', 'archive-stories');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function ConfigureArchive(Request $request)
    {


        return $this->createView()
            ->shares('title', __d('two_archive_storie', 'archive-stories'));
    }
}
