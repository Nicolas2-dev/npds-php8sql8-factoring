<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Library\Interface;


interface ThemeInterface
{

    /**
     * [local_var description]
     *
     * @param   [type]  $Xcontent  [$Xcontent description]
     *
     * @return  [type]             [return description]
     */
    public function local_var(string $Xcontent);

    /**
     * [theme_Image description]
     *
     * @param   [type]  $theme_img  [$theme_img description]
     *
     * @return  [type]              [return description]
     */
    public function theme_Image(string $theme_img);

    /**
     * [theme_Image_Row description]
     *
     * @param   [type]  $theme_img    [$theme_img description]
     * @param   [type]  $default_img  [$default_img description]
     *
     * @return  [type]                [return description]
     */
    public function theme_Image_Row(string $theme_img, string $default_img);

    /**
     * [asset_theme_url description]
     *
     * @param   [type]  $image  [$image description]
     *
     * @return  [type]          [return description]
     */
    public function asset_theme_url($image, $hint);

    /**
     * [colsyst description]
     *
     * @param   [type]  $coltarget  [$coltarget description]
     *
     * @return  [type]              [return description]
     */
    public function colsyst(string $coltarget);

    /**
     * [header description]
     *
     * @return  [type]  [return description]
     */
    public function header();

    /**
     * [headerHead description]
     *
     * @return  [type]  [return description]
     */
    public function headerHead();

    /**
     * [headerHeadTheme description]
     *
     * @return  [type]  [return description]
     */
    public function headerHeadTheme();

    /**
     * [footmsg description]
     *
     * @return  [type]  [return description]
     */
    public function footmsg();

    /**
     * [foot description]
     *
     * @return  [type]  [return description]
     */
    public function foot();

    /**
     * [themedito description]
     *
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public function themedito(string $content);

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
    public function themearticle($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive);

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
    public function themeindex($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id);

    /**
     * [leftBlock description]
     *
     * @param   [type]  $pdst  [$pdst description]
     *
     * @return  [type]         [return description]
     */
    public function leftBlock($pdst);

    /**
     * [rightBlock description]
     *
     * @param   [type]  $pdst  [$pdst description]
     *
     * @return  [type]         [return description]
     */
    public function rightBlock($pdst);

    /**
     * [themesidebox description]
     *
     * @param   [type]  $title    [$title description]
     * @param   [type]  $content  [$content description]
     *
     * @return  [type]            [return description]
     */
    public function themesidebox($title, $content);

    /**
     * [bodyOnloadDefault description]
     *
     * @return  [type]  [return description]
     */
    public function bodyOnloadDefault();

    /**
     * [bodyOnloadTheme description]
     *
     * @return  [type]  [return description]
     */
    public function bodyOnloadTheme();

    /**
     * [headerAfter description]
     *
     * @return  [type]  [return description]
     */
    public function headerAfter();

    /**
     * [footerAfter description]
     *
     * @return  [type]  [return description]
     */
    public function footerAfter();

    /**
     * [headerBefore description]
     *
     * @return  [type]  [return description]
     */
    public function headerBefore();

    /**
     * [footerBefore description]
     *
     * @return  [type]  [return description]
     */
    public function footerBefore();

    /**
     * [getThemeOptions description]
     *
     * @return  [type]  [return description]
     */
    public function getThemeOptions();

    /**
     * [getApp description]
     *
     * @return  [type]  [return description]
     */
    public function getApp(string $classname = null);

    /**
     * [getName description]
     *
     * @return  [type]  [return description]
     */
    public function getName();

    /**
     * [getConfig description]
     *
     * @return  [type]  [return description]
     */
    public function getConfig(string $config = 'config');

    /**
     * [getOptions description]
     *
     * @return  [type]  [return description]
     */
    public function getOptions();

    /**
     * [getName description]
     *
     * @return  [type]  [return description]
     */
    public function getHint();

    /**
     * [getPath description]
     *
     * @return  [type]  [return description]
     */
    public function getPath();

    /**
     * Get theme namespace.
     *
     * @return string
     */
    public function getNamespace();

}
