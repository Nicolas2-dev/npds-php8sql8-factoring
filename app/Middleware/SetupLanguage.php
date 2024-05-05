<?php

namespace App\Middleware;

use Closure;

use Npds\Foundation\Application;


class SetupLanguage
{

    /**
     * La mise en œuvre de l'application.
     *
     * @var \Npds\Foundation\Application
     */
    protected $app;


    /**
     * Créez une nouvelle instance de middleware.
     *
     * @param  \Npds\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Traiter une demande entrante.
     *
     * @param  \Two\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Two\Http\Exception\PostTooLargeException
     */
    public function handle($request, Closure $next)
    {
        $multi_langue = $this->app['config']->get('npds.multi_langue');
        
        $session = $this->app['session'];

        if ($multi_langue && $session->has('user_language')) {
            $this->updateUserLocale($request);
        } else {
            $this->updateLocale($request);
        }

        return $next($request);
    }

    /**
     * Mettez à jour les paramètres régionaux de l'application.
     *
     * @param  \Two\Http\Request  $request
     * @return void
     */
    protected function updateLocale($request)
    {
        $session = $this->app['session'];

        if ($session->has('user_language')) {
            $locale = $session->get('user_language');
        } else {
            $locale = $request->cookie(PREFIX .'user_language', $this->app['config']['app.locale']);

            $session->set('user_language', $locale);
        }

        $this->app['translator']->setLocale($locale);
    }

    /**
     * Mettez à jour les paramètres régionaux de lutilisateur.
     *
     * @param  \Two\Http\Request  $request
     * @return void
     */
    protected function updateUserLocale($request)
    {
        $choice_user_language = $request->input('choice_user_language');

        if (isset($choice_user_language)) {

            $user_cook_duration = $this->app['config']->get('npds.user_cook_duration', 1);

            $languageslist = $this->app['config']->get('languages');

            if ((stristr($languageslist, $choice_user_language)) and ($choice_user_language != ' ')) {
                $this->app['session']->set('user_language', $choice_user_language);

                $this->app['cookie']->queue(PREFIX .'user_language', $choice_user_language, (time() + (3600 * $user_cook_duration)));
            }
        }
    }

}
