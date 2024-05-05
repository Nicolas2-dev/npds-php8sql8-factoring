<?php

namespace App\Controllers;

use Npds\Http\Request;

use App\Controllers\BaseController;


class Home extends BaseController
{
    protected $layout = 'Sample';


    public function index(Request $request)
    {
        $content = 'This is the Homepage';

        //dump($request->route());
        dump(__($content));

        return $this->createView()
            ->shares('title', 'Homepage')
            ->with('content', __($content));
    }
}
