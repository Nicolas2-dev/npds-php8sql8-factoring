<?php

declare(strict_types=1);

namespace Npds\View\Middleware;

use Closure;

use Npds\Support\ViewErrorBag;
use Npds\View\Factory as ViewFactory;


class ShareErrorsFromSession
{
    /**
     * The view factory implementation.
     *
     * @var \Npds\View\Factory
     */
    protected $view;

    /**
     * Create a new error binder instance.
     *
     * @param  \Npds\View\Factory  $view
     * @return void
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Npds\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->view->share(
            'errors', $request->session()->get('errors', new ViewErrorBag())
        );

        return $next($request);
    }
}
