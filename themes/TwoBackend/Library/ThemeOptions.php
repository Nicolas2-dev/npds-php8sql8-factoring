<?php

namespace Themes\TwoBackend\Library;

use Two\Foundation\Application;
use Modules\TwoThemes\Library\ThemeManager;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoUsers\Library\User\UserManager;


class ThemeOptions
{


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
     * [test_option description]
     *
     * @return  [type]  [return description]
     */
    function test_option() {

        echo '<br>theme option backend !<br>';
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

}