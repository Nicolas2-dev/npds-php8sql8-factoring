<?php

namespace Modules\Two\Controllers;

use Modules\TwoCore\Core\FrontController;


class Front extends FrontController
{

    public $packages;


    /**
     * test
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_themes', 'Test Front theme'));
    }

}

