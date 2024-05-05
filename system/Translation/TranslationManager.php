<?php

declare(strict_types=1);

namespace Npds\Translation;

use Npds\Support\Arr;
use Npds\Support\Str;
use Npds\Foundation\Application;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Carbon\CarbonImmutable;


class TranslationManager
{

    /**
     * The Application instance.
     *
     * @var \Npds\Foundation\Application
     */
    protected $app;

    /**
     * The default locale being used by the translator.
     *
     * @var string
     */
    protected $locale;

    /**
     * The know Languages.
     *
     * @var array
     */
    protected $languages = array();

    /**
     * The active Language instances.
     *
     * @var array
     */
    protected $instances = array();

    /**
     * Path.
     *
     * @var array
     */
    protected $path;


    /**
     * Create new Language Manager instance.
     *
     * @param  \Npds\Foundation\Application  $app
     * @return void
     */
    function __construct(Application $app, $locale)
    {
        $this->app = $app;

        $this->locale = $locale;

        // Setup the know Languages.
        $this->languages = $app['config']['languages'];

        // Setup the default path hints.
        $this->path = APPPATH .'Language';
    }

    /**
     * Get instance of Language with domain and code (optional).
     * @param string $domain Optional custom domain
     * @param string $code Optional custom language code.
     * @return Language
     */
    public function instance($locale = null)
    {
        $locale = $locale ?: $this->locale;

        // The ID code is something like
        $id = $locale;

        // Returns the Language domain instance, if it already exists.
        if (isset($this->instances[$id])) return $this->instances[$id];

        return $this->instances[$id] = new Translation($this, $locale);
    }

    /**
     * Get the know Languages.
     *
     * @return string
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the default locale.
     *
     * @param  string  $locale
     * @return void
     */
    public function setLocale($locale)
    {
        // Setup the Framework locale.
        $this->locale = $locale;

        // Setup the Carbon locale.
        Carbon::setLocale($locale);

        CarbonImmutable::setLocale($locale);
        CarbonPeriod::setLocale($locale);
        CarbonInterval::setLocale($locale);

        // Retrieve the full qualified locale from languages list.
        $locale = Str::finish(
            Arr::array_get($this->languages, "{$locale}.locale", 'en_US'), '.utf8'
        );

        // Setup the PHP's Time locale.
        setlocale(LC_TIME, $locale);
    }

}
