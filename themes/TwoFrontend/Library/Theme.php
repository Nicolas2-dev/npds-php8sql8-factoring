<?php

declare(strict_types=1);

namespace Themes\TwoFrontend\Library;

use Two\Foundation\Application;
use Modules\TwoThemes\Library\ThemeManager;
use Modules\TwoUsers\Library\User\UserManager;
use Themes\TwoFrontend\Support\Facades\ThemeOptions;
use Modules\TwoCore\Library\Language\LanguageManager;
use Modules\TwoCore\Library\Metalang\MetaLangManager;


class Theme extends ThemeManager
{

    /**
     * The ThemeManager instance
     *
     * @var ThemeManager
     */
    private static ?ThemeManager $instance = null;


    /**
     * instance ThemeManager
     *
     *
     * @return ThemeManager
     */
    public static function instance(Application $app, string $theme, MetaLangManager $metalang, LanguageManager $language, UserManager $user): ThemeManager
    {
        if (static::$instance === null) {
            static::$instance = new self($app, $theme, $metalang, $language, $user);
        }

        return static::$instance;
    }

    /**
     * Get instance ThemeManager
     *
     * @return ThemeManager
     */
    public static function getInstance(): ThemeManager
    {
        return static::$instance;
    }

    // header

    /**
     * [header description]
     *
     * @return  [type]  [return description]
     */
    public function header()
    {
        return parent::header();
    }

    /**
     * [headerHead description]
     *
     * @return  [type]  [return description]
     */
    public function headerHead()
    {
        return parent::headerHead();
    }

    /**
     * [headerHeadTheme description]
     *
     * @return  [type]  [return description]
     */
    public function headerHeadTheme()
    {
        return parent::headerHeadTheme();
    }

    // footer

    /**
     * [footmsg description]
     *
     * @return  [type]  [return description]
     */
    public function footmsg() 
    {
        return parent::footmsg();
    }

    /**
     * [foot description]
     *
     * @return  [type]  [return description]
     */
    public function foot() 
    {
        return parent::foot();
    }

    // edito

    /**
     * [themedito description]
     *
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public function themedito($content)
    {
        return parent::themedito($content);
    }

    // news

    /**
     * [themearticle description]
     *
     * @param   [type]  $aid           [$aid description]
     * @param   [type]  $informant     [$informant description]
     * @param   [type]  $time          [$time description]
     * @param   [type]  $title         [$title description]
     * @param   [type]  $thetext       [$thetext description]
     * @param   [type]  $topic         [$topic description]
     * @param   [type]  $topicname     [$topicname description]
     * @param   [type]  $topicimage    [$topicimage description]
     * @param   [type]  $topictext     [$topictext description]
     * @param   [type]  $id            [$id description]
     * @param   [type]  $previous_sid  [$previous_sid description]
     * @param   [type]  $next_sid      [$next_sid description]
     * @param   [type]  $archive       [$archive description]
     *
     * @return  [type]                 [return description]
     */
    public function themearticle($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive) 
    {
        return parent::themearticle($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive) ;
    }

    /**
     * [themeindex description]
     *
     * @param   [type]  $aid         [$aid description]
     * @param   [type]  $informant   [$informant description]
     * @param   [type]  $time        [$time description]
     * @param   [type]  $title       [$title description]
     * @param   [type]  $counter     [$counter description]
     * @param   [type]  $topic       [$topic description]
     * @param   [type]  $thetext     [$thetext description]
     * @param   [type]  $notes       [$notes description]
     * @param   [type]  $morelink    [$morelink description]
     * @param   [type]  $topicname   [$topicname description]
     * @param   [type]  $topicimage  [$topicimage description]
     * @param   [type]  $topictext   [$topictext description]
     * @param   [type]  $id          [$id description]
     *
     * @return  [type]               [return description]
     */
    public function themeindex($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id)
    {
        return parent::themeindex($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id);
    }

    // block

    /**
     * [leftBlock description]
     *
     * @param   [type]  $pdst  [$pdst description]
     *
     * @return  [type]         [return description]
     */
    public function leftBlock($pdst)
    {
        return parent::leftBlock($pdst);
    }

    /**
     * [rightBlock description]
     *
     * @param   [type]  $pdst  [$pdst description]
     *
     * @return  [type]         [return description]
     */
    public function rightBlock($pdst)
    {
        return parent::rightBlock($pdst);
    }

    /**
     * [themesidebox description]
     *
     * @param   [type]  $title    [$title description]
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public function themesidebox($title, $content)
    {
        return parent::themesidebox($title, $content);
    }

    // bodyonload

    /**
     * [bodyOnloadDefault description]
     *
     * @return  [type]  [return description]
     */
    public function bodyOnloadDefault()
    {
        return parent::bodyOnloadDefault();
    }

    /**
     * [bodyOnloadTheme description]
     *
     * @return  [type]  [return description]
     */
    public function bodyOnloadTheme()
    {
        return parent::bodyOnloadTheme();
    }

    // after

    /**
     * [headerAfter description]
     *
     * @return  [type]  [return description]
     */
    public function headerAfter()
    {
        return parent::headerAfter();
    }

    /**
     * [footerAfter description]
     *
     * @return  [type]  [return description]
     */
    public function footerAfter() 
    {
        return parent::footerAfter();
    }

    // before

    /**
     * [headerBefore description]
     *
     * @return  [type]  [return description]
     */
    public function headerBefore() 
    {
        return parent::headerBefore();
    }

    /**
     * [footerBefore description]
     *
     * @return  [type]  [return description]
     */
    public function footerBefore() 
    {
        return parent::footerBefore();
    }

    /**
     * [getThemeOptions description]
     *
     * @return  [type]  [return description]
     */
    public function getThemeOptions()
    {
        return ThemeOptions::getInstance();
    }

}