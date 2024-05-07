<?php
/**
 * Two - RgpdCitron
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

declare(strict_types=1);

namespace Shared\RgpdCitron;

use Two\Support\Facades\Asset;
use Two\Support\Facades\Config;


class RgpdCitron
{

    /**
     * path.
     *
     * @var string $path
     */
    protected $path;

    /**
     * path.
     *
     * @var array $config
     */
    protected $config;

    /**
     * [$position description]
     *
     * @var [type]
     */
    protected $position = 2400;


    /**
     * Create a new Rgpd Citron instance.
     *
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * .
     *
     * @return void
     */
    public function headerCitron()
    {
        $locale = explode('_', Config::get('two_core::config.locale'));

//vd($locale);

        Asset::register(
            array(
                'var tarteaucitronForceLanguage = "'.$locale[0].'";'
            ),'js', 'header', $this->position, 'inline');

        Asset::register(
            array(
                shared_url('assets/tarteaucitron.js', 'RgpdCitron'),
            ),'js', 'header', $this->position);

        Asset::register(
            array(
    '//<![CDATA[
    tarteaucitron.init({
        "privacyUrl": "'.$this->config['privacyUrl'].'",
        "hashtag": "'.$this->config['hashtag'].'",
        "cookieName": "'.$this->config['cookieName'].'",
        "orientation": '.$this->config['orientation'].',
        "showAlertSmall": '.$this->config['showAlertSmall'].',
        "cookieslist": '.$this->config['cookieslist'].',
        "showIcon": '.$this->config['showIcon'].',
        "iconPosition": "'.$this->config['iconPosition'].'",
        "adblocker": '.$this->config['adblocker'].',
        "AcceptAllCta" : '.$this->config['AcceptAllCta'].',
        "highPrivacy": '.$this->config['highPrivacy'].',
        "handleBrowserDNTRequest": '.$this->config['handleBrowserDNTRequest'].',
        "removeCredit": '.$this->config['removeCredit'].',
        "moreInfoLink": '.$this->config['moreInfoLink'].',
        "useExternalCss": '.$this->config['useExternalCss'].',
        "cookieDomain": "'.$this->config['cookieDomain'].'",
        "readmoreLink": "'.$this->config['readmoreLink'].'",
        "mandatory": '.$this->config['mandatory'].',
    });
    //]]'
            ),'js', 'header', $this->position, 'inline');   
    }

    /**
     * .
     *
     * @return void
     */
    public function footerCitron()
    {
        Asset::register(
            array(
    '//<![CDATA[
        (tarteaucitron.job = tarteaucitron.job || []).push("vimeo");
        (tarteaucitron.job = tarteaucitron.job || []).push("youtube");
        (tarteaucitron.job = tarteaucitron.job || []).push("dailymotion");
        //tarteaucitron.user.gtagUa = ""; /*uncomment the line and add your gtag*/
        //tarteaucitron.user.gtagMore = function () { /* uncomment the line add here your optionnal gtag() */ };
        //(tarteaucitron.job = tarteaucitron.job || []).push("gtag");
    //]]'
            ),'js', 'footer', $this->position, 'inline');
    }

}
