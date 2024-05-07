<?php

declare(strict_types=1);

namespace Modules\TwoThemes\Support\Traits;


trait ThemeFavicorait 
{

    /**
     * 
     * 
     * @param string
     */
    const FAVICON_DEFAUT = 'favicon.ico';


    /**
     * 
     * 
     * @return string
     */
    function favico()
    {
        $option = $this->app['two_theme']->getConfig('config.favicon');

        if ($option['use']) {

            if ($this->app->files->exists($this->app['two_theme']->getPath() .DS. 'assets' .DS. 'images' .DS. $option['name'])) {
                $favicon = asset_url('images/'. $option['name'], 'themes/'. $this->app['two_theme']->getHint());
            } else {
                $favicon = $this->Favico_Default();
            }

            $this->prepare($favicon, 'favicon', 'favicon');

        } else {

            $favicon = $this->favico_Default();

            $this->prepare($favicon, 'favicon', 'favicon');
        }
    }

    /**
     * 
     * @return string
     */
    function favico_Default()
    {
        return asset_url('images/'.self::FAVICON_DEFAUT);
    }

}