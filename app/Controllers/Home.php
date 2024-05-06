<?php

namespace App\Controllers;

use Npds\Http\Request;

use App\Support\Facades\Assets;
use App\Controllers\Core\FrontController;


class Home extends FrontController
{
    
    protected $layout = 'Sample';

 
    public function index(Request $request)
    {
        $content = 'This is the Homepage';

        //dump(Assets::import_css());
        //dump($request->route());
        //dump(__($content));

        return $this->createView()
            ->shares('title', 'Homepage')
            ->with('content', __($content));
    }
}
