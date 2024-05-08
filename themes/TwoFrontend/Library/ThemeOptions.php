<?php

declare(strict_types=1);

namespace Themes\TwoFrontend\Library;

use Two\Support\Facades\DB;
use Two\Support\Facades\View;
use Two\Foundation\Application;
use Modules\TwoThemes\Library\ThemeManager;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoAuthors\Support\Facades\Author;
use Modules\TwoUsers\Library\User\UserManager;
use Modules\TwoUsers\Support\Traits\UserMenuTrait;
use Modules\TwoUsers\Support\Traits\UserAvatarTrait;


class ThemeOptions
{
    use UserAvatarTrait, UserMenuTrait;
    

    /**
     * The ThemeOptions instance
     *
     * @var ThemeOptions
     */
    private static ?ThemeOptions $instance = null;

    /**
     * The Application Instance.
     *
     * @var \Two\Application\Application
     */
    private Application $app;

    /**
     * The Application Instance.
     *
     * @var \Modules\TwoUsers\Library\User\UserManager
     */
    private UserManager $user;

    /**
     * [$theme description]
     *
     * @var [type]
     */
    private ?ThemeManager $theme = null;


    /**
     * [__construct description]
     *
     * @param   \Two\Application\Application  $app         [$app description]
     *
     * @return  [type]                    [return description]
     */
    public function __construct(Application $app, UserManager $user)
    {
        $this->app = $app;

        $this->user = $user;

        $this->theme = $this->getTheme();

        //$this->sharesThemeOptions();
    }

    /**
     * instance ThemeOptions
     *
     *
     * @return ThemeOptions
     */
    public static function instance(Application $app, UserManager $user): ThemeOptions
    {
        if (static::$instance === null) {
            static::$instance = new self($app, $user);
        }

        return static::$instance;
    }

    /**
     * Get instance ThemeOptions
     *
     * @return ThemeOptions
     */
    public static function getInstance(): ThemeOptions
    {
        return static::$instance;
    }

    /**
     * [getTheme description]
     *
     * @return  [type]  [return description]
     */
    public function getTheme(): ThemeManager
    {
        return Theme::getInstance();
    }

    /**
     * [containerStart description]
     *
     * @return  [type]  [return description]
     */
    public function containerStart()
    {
        echo $this->theme->getConfig('container.start');
    }

    /**
     * [containerEnd description]
     *
     * @return  [type]  [return description]
     */
    public function containerEnd()
    {
        echo $this->theme->getConfig('container.end');
    }

    /**
     * [headerClass description]
     *
     * @return  [type]  [return description]
     */
    public function headerClass()
    {
        switch ($this->theme->getSkin()) {
            case 'cyborg':
            case 'solar':
            case 'superhero':
               $headerclasses ='navbar navbar-expand-md navbar-dark bg-dark fixed-top';
            break;

            case 'lumen':
            case 'journal':
            case 'materia':
               $headerclasses ='navbar navbar-expand-md navbar-dark bg-primary fixed-top';
            break;

            case 'simplex':
            case 'litera':
            case 'spacelab':
               $headerclasses ='navbar navbar-expand-md navbar-light bg-light fixed-top';
            break;

            default :
                // empty & cerulean cosmo darkly flatly lux minty pulse sandstone slate united yeti default
               $headerclasses = 'navbar navbar-expand-md navbar-dark bg-primary fixed-top'; 
            break;
        }

        View::share('headerclasses', ' class="'. $headerclasses .'"');
    }

    /**
     * [userAvatar description]
     *
     * @return  [type]  [return description]
     */
    public function userAvatar()
    {
        $avatar = null;

        if ($this->user->autorisation(-1)) {
            $avatar = '<a class="dropdown-item" href="' . site_url('user?op=dashboard') . '"><i class="fa fa-user fa-3x text-muted"></i></a>';
        } elseif ($this->user->autorisation(1)) {

            $user = $this->user->getUser();

            $user_avatar = $this->avatar($user);

            $avatar = '<a class="dropdown-item" href="' . route('dashboard') . '" >
                    <img src="' . $user_avatar . '" class="n-ava-64" alt="avatar" title="' . __d('two_frontend', 'Votre compte') . '" data-bs-toggle="tooltip" data-bs-placement="right" /></a>
                    <li class="dropdown-divider">
                </li>';
        }

        View::share('avatar', $avatar);
    }

    /**
     * [btCon description]
     *
     * @return  [type]  [return description]
     */
    public function btCon()
    {
        $btn_con = '';

        if ($this->user->autorisation(-1)) {
            $btn_con = '<a class="dropdown-item" href="rsdfgsdqfqs">
                    <i class="fas fa-sign-in-alt fa-lg me-2 align-middle"></i>
                    ' . __d('two_frontend', 'Connexions') . '
                </a>';
        } elseif ($this->user->autorisation(1)) {
            $btn_con = '<a class="dropdown-item" href="dfgqdsdf">
                    <i class="fas fa-sign-out-alt fa-lg text-danger me-2">
                    </i>' . __d('two_frontend', 'Déconnexion') . '
                </a>';
        }

        View::share('btn_con', $btn_con);
    }

    /**
     * [getUsername description]
     *
     * @return  [type]  [return description]
     */
    public function getUsername()
    {
        $user = $this->user->getUser();

        if ($user) {
            $cookie = $this->user->cookieUser();
            View::share('username', $cookie[1]);
        } 
    }

    /**
     * [userMenu description]
     *
     * @return  [type]  [return description]
     */
    public function userMenu()
    {
        $usermenu = null;

        $user = $this->user->getUser();

        if ($user) {
            $usermenu = $this->userMenu();
        }

        View::share('usermenu', $usermenu);
    }

    /**
     * [adminMenu description]
     *
     * @return  [type]  [return description]
     */
    public function adminMenu()
    {
        if (Author::getAdmin()) {

            $adminMenu = '<li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-server fa-lg"></i>
                        ' . __d('two_frontend', 'Administration') . '
                    </a>
                    <ul class="dropdown-menu">
                        <a class="dropdown-item" href="' . site_url('logout/admin?op=logout') . '"><i class="fas fa-sign-out-alt fa-lg text-danger me-2"></i>' . __d('two_frontend', 'Déconnexion') . '</a>
                    </ul>
                </li>';

            View::share('adminMenu', $adminMenu);   
        }
    }

    /**
     * [privMsgs description]
     *
     * @return  [type]  [return description]
     */
    public function privMsgs()
    {
        if ($this->user->autorisation(-1)) {
            //
        } elseif ($this->user->autorisation(1)) {

            $cookie = $this->user->cookieUser();

            $nbmes = DB::table('priv_msgs')->where('to_userid', $cookie[0])->where('read_msg', 0)->count();

            View::share('cl', ($nbmes > 0 ? ' faa-shake animated ' : ''));

            if ($nbmes > 0) {
                $privmsgs = '<li class="nav-item">
                    <a class="nav-link" href="viewpmsg.php">
                        <i class="fa fa-envelope fs-4 faa-shake animated" title="' . __d('two_frontend', 'Message personnel') . ' <span class=\'badge rounded-pill bg-danger ms-2\'>' . $nbmes . '</span>" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="right"></i>
                    </a>
                </li>';
 
                View::share('privmsgs', $privmsgs); 
            }  
        }
    }

    /**
     * [sharesThemeOptions description]
     *
     * @return  [type]  [return description]
     */
    public function sharesThemeOptions()
    {
        $this->headerClass();
        $this->userAvatar();
        $this->btCon();
        $this->getUsername();
        $this->userMenu();
        $this->adminMenu();
        $this->privMsgs();
    }

    // test Good !
    // public function theme_centre_box(string $title, string $content): string  
    // {
    //     $center_box = 'Center Box Theme Option : ' .$title;
    //     $center_box .= '<br>' . $content;

    //     return $center_box;
    // }


}