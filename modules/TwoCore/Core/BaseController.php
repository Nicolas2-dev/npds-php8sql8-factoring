<?php
/**
 * Two - BaseController
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace Modules\TwoCore\Core;

use Two\Support\Str;

use Two\Http\Request;
use BadMethodCallException;
use Two\Routing\Controller;
use Two\Support\Facades\View;
use Two\Foundation\AliasLoader;
use Two\Support\Facades\Config;
use Two\Support\Facades\Language;
use Erusev\Parsedown\Components\Block;

use Two\Contracts\RenderableInterface;
use Two\Foundation\Bus\DispatchesJobsTrait;
use Modules\TwoCore\Support\Facades\Metatag;
use Modules\TwoCore\Events\CounterUpdateEvent;
use Modules\TwoCore\Events\RefererUpdateEvent;
use Two\Foundation\Validation\ValidatesRequestsTrait;
use Two\Foundation\Auth\Access\AuthorizesRequestsTrait;


class BaseController extends Controller
{
    use DispatchesJobsTrait, AuthorizesRequestsTrait, ValidatesRequestsTrait;

    /**
     * L'instance de demande actuelle.
     *
     * @var \Two\Http\Request
     */
    protected $request;

    /**
     * L'action actuellement appelée.
     *
     * @var string
     */
    protected $action;

    /**
     * Les paramètres d'appel en cours.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Le thème actuellement utilisé.
     *
     * @var string
     */
    protected $theme;

    /**
     * La mise en page actuellement utilisée.
     *
     * @var string
     */
    protected $layout = 'Default';

    /**
     * Vrai lorsque le rendu automatique est actif.
     *
     * @var bool
     */
    protected $autoRender = false;

    /**
     * Vrai lorsque la mise en page automatique est active.
     *
     * @var bool
     */
    protected $autoLayout = true;

    /**
     * Le chemin d'accès à la vue pour les vues de ce contrôleur.
     *
     * @var string
     */
    protected $viewPath;

    /**
     * Les variables de vue.
     *
     * @var array
     */
    protected $viewData = array();

    /**
     * 
     */
    protected $pdst = 1;

    /**
     * 
     */
    private $radminsuper;

    /**
     * 
     */
    private $admintest = false;
    
    /**
     * 
     */
    private $super_admintest = false;

    /**
     * 
     */
    private $aid;

    /**
     * 
     */
    protected $f_meta_nom;

    /**
     * 
     */
    protected $hlpfile;

    /**
     * 
     */
    protected $short_menu_admin;

    /**
     * 
     */
    protected $admin_head;


    /**
     * Méthode exécutée avant toute action.
     *
     * @return void
     */
    protected function initialize(Request $request)
    {
        // Configurez le thème utilisé par défaut, s'il n'est pas déjà défini.
        if (is_null($this->theme)) {
            $this->theme = Config::get('app.theme', 'Themes/Bootstrap');
        }

        $this->appThemeRegister($this->theme);

        $theme = $this->getTheme();

        if ($theme === false) {
            return;
        }

        // Un thème est configuré pour ce contrôleur.
        else if (! Str::contains($theme, '/')) {
            $theme = 'Themes/' .$theme;
        }

        // Register Metatags
        Metatag::register();

        //
        // Metalang::charg_metalang();

        View::overridesFrom($theme);

        //View::share('pdst', $this->pdst);

        Config::set('themes.current', $theme);
    }

    /** 
     * 
     */
    private function appThemeRegister($theme)
    {
        app()->bindShared('two_theme', function ($app) use ($theme)
        {
            $namespace = ('Themes\\'.$this->theme.'\Library\Theme');

            return $namespace::instance($app, $theme, $app['two_metalang'], $app['two_language'], $app['two_user']);
        });

        // // Register the Facades.
        $loader = AliasLoader::getInstance();

        $loader->alias('two_theme', 'Modules\TwoThemes\Support\Facades\Theme');

        View::share('theme', app('two_theme'));
    }

    /**
     * Exécuter une action sur le contrôleur.
     *
     * @param string  $method
     * @param array   $params
     * @param \Two\Http\Request  $request
     * @return mixed
     */
    public function callAction($method, array $parameters, Request $request)
    {
        $this->request = $request;
        $this->action  = $method;

        $this->parameters = $parameters;

        //
        $this->initialize($request);

        $response = call_user_func_array(array($this, $method), $parameters);

        if (is_null($response) && $this->autoRender()) {
            $response = $this->createView();
        }

        // render metatag via variable metatags dans theme
        //View::share('metatags', Metatag::metas(false));

        // Mise à jours des conters hits browser os 
        event(new CounterUpdateEvent($request));

        // mise à jour des référer
        event(new RefererUpdateEvent($request));

        return $this->processResponse($response);
    }

    /**
     * Traiter une réponse d'action du contrôleur.
     *
     * @param  mixed   $response
     * @return mixed
     */
    protected function processResponse($response)
    {
        if (! $response instanceof RenderableInterface) {
            return $response;
        }

        // La réponse est une implémentation RenderableInterface.
        else if (! empty($view = $this->resolveLayoutView()) && $this->autoLayout()) {
            return View::make($view, $this->viewData)->with('content', $response);
        }

        return $response;
    }

    /**
     * Obtient un nom de vue localisé pour la mise en page actuellement utilisée.
     *
     * @return string
     */
    protected function resolveLayoutView()
    {
        if (empty($layout = $this->getLayout())) {
            return false;
        }

        $direction = Language::direction();

        if ($direction == 'rtl') {
            $view = $this->resolveViewFromTheme('Layouts/RTL/' .$layout);

            if (View::exists($view)) {
                return $view;
            }
        }

        return $this->resolveViewFromTheme('Layouts/' .$layout);
    }

    /**
     * Obtient un nom de vue qualifié pour la mise en page implicite ou donnée.
     *
     * @param  string  $view
     * @return string
     */
    protected function resolveViewFromTheme($view)
    {
        if (empty($theme = Config::get('themes.current', $this->getTheme()))) {
            return $view;
        }

        // Un thème est spécifié pour le rendu automatique.
        else if (! Str::contains($theme, '/')) {
            return sprintf('Themes/%s::%s', $theme, $view);
        }

        return sprintf('%s::%s', $theme, $view);
    }

    /**
     * Créez une instance de vue pour le nom de vue implicite (ou spécifié).
     *
     * @param  array  $data
     * @param  string|null  $view
     * @return \Two\View\View
     */
    protected function createView(array $data = array(), $view = null)
    {
        if (is_null($view)) {
            $view = ucfirst($this->action);
        }

        if (!is_null($this->pdst)) {
            $pdst = $this->pdst;
        } else {
            $pdst = -1;
        }

        $pdst = app('two_theme')->pdstBlock($pdst);

        View::share('pdst', $pdst);

        $view = sprintf('%s/%s', $this->resolveViewPath(), $view);

        return View::make($view, array_merge($this->viewData, $data));
    }

    /**
     * Obtient un chemin d'accès View qualifié.
     *
     * @return string
     * @throws \BadMethodCallException
     */
    protected function resolveViewPath()
    {
        if (isset($this->viewPath)) {
            return $this->viewPath;
        }

        $path = str_replace('\\', '/', static::class);

        if (preg_match('#^(.+)/Controllers/(.*)$#', $path, $matches) === 1) {
            list (, $basePath, $viewPath) = $matches;

            if ($basePath != 'App') {
                $viewPath = sprintf('%s::%s', $basePath, $viewPath);
            }

            return $this->viewPath = $viewPath;
        }

        throw new BadMethodCallException('Invalid controller namespace');
    }

    /**
     * Ajoutez une paire clé/valeur aux données de la vue.
     *
     * Bound data will be available to the view as variables.
     *
     * @param  string|array  $one
     * @param  string|array  $two
     * @return BaseController
     */
    public function set($one, $two = null)
    {
        if (is_array($one)) {
            $data = is_array($two) ? array_combine($one, $two) : $one;
        } else {
            $data = array($one => $two);
        }

        $this->viewData = $data + $this->viewData;

        return $this;
    }

    /**
     * Active ou désactive le mode conventionnel de rendu automatique de Two.
     *
     * @param bool|null  $enable
     * @return bool
     */
    public function autoRender($enable = null)
    {
        if (is_null($enable)) {
            return $this->autoRender;
        }

        $this->autoRender = (bool) $enable;

        return $this;
    }

    /**
     * Active ou désactive le mode conventionnel d'application des fichiers de mise en page de Two.
     *
     * @param bool|null  $enable
     * @return bool
     */
    public function autoLayout($enable = null)
    {
        if (is_null($enable)) {
            return $this->autoLayout;
        }

        $this->autoLayout = (bool) $enable;

        return $this;
    }

    /**
     * Renvoie l'instance Request actuelle.
     *
     * REMARQUE : ces informations sont disponibles après l'appel de l'action.
     *
     * @return \Two\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Renvoie l'action appelée en cours
     *
     * REMARQUE : ces informations sont disponibles après l'appel de l'action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Renvoie les paramètres d'appel en cours
     *
     * REMARQUE : ces informations sont disponibles après l'appel de l'action.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Renvoie le thème actuel.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Renvoie la mise en page actuelle.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Renvoie les données de la vue actuelle.
     *
     * @return string
     */
    public function getViewData()
    {
        return $this->viewData;
    }
}
