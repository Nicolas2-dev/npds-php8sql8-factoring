<?php

namespace Modules\TwoCluterParadise\Providers;

use Two\Auth\Access\GateInterface as Gate;
use Two\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the module.
     *
     * @var array
     */
    protected $policies = array(
        'Modules\TwoCluterParadise\Models\SomeModel' => 'Modules\TwoCluterParadise\Policies\ModelPolicy',
    );


    /**
     * Register any module authentication / authorization services.
     *
     * @param  \Two\Auth\Access\GateInterface  $gate
     * @return void
     */
    public function boot(Gate $gate)
    {
        $this->registerPolicies($gate);

        //
    }
}
