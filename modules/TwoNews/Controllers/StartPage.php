<?php

namespace Modules\TwoNews\Controllers;

use Two\Http\Request;
use Two\Support\Facades\Url;
use Two\Support\Facades\View;
use Two\Support\Facades\Config;

use Modules\TwoNews\Support\Facades\News;
use Modules\TwoEdito\Support\Facades\Edito;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Core\FrontController as BaseController;


class StartPage extends BaseController
{

    /**
     * Disposition des blocks
     */
    protected $pdst = 0;


    /**
     * Redirect for default Start Page of the portal - look at Admin Preferences for choice
     *  
     * @param request $request
     * @return void
     */
    function start(Request $request)
    {
        // if (!User::AutoReg()) {
        //     global $user;
        //     unset($user);
        // }
    
        $Start_Page = Config::get('two_core::config.Start_Page');

        $start = explode('?op=', $Start_Page);

        $op = $start[1];

        if (($Start_Page == '') 
        or ($op == "index.php") 
        or ($op == "edito") 
        or ($op == "edito-nonews")) {          
            News::automatednews();

            $catid      = $request->query('catid', 0);
            $marqeur    = $request->query('marqeur', 0);

            if (($op == 'newcategory') 
                or ($op == 'newtopic') 
                or ($op == 'newindex') 
                or ($op == 'edito-newindex')) 
            {
                $news = News::aff_news($op, $catid, $marqeur);
            } else {

                $theme = Theme::getName();

                if (View::exists('Themes/'.$theme.'::Partials/Central')) {
                    $central = View::fetch('Themes/'.$theme.'::Partials/Central');
                } else {
                    $central = false;
                    $edito   = false;
                    $news    = false;
                    
                    if (($op == 'edito') or ($op == 'edito-nonews')) {
                        $edito = Edito::aff_edito();
                    }

                    if ($op != 'edito-nonews') {
                        $news = News::aff_news($op, $catid, $marqeur);
                    }
                }
            }

            return $this->createView(compact('edito', 'news', 'central'))
                ->shares('title', __d('two_news', 'Test Front theme'));
            
        } else {
            Url::redirect_url($Start_Page);
        }
    }

}
