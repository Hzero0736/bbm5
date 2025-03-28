<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function (User $user) {
            return $user->roles()->where('name', 'Admin')->exists();
        });

        Gate::define('isSectionHead', function (User $user) {
            return $user->roles()->where('name', 'Section Head')->exists();
        });

        Gate::define('isStaff', function (User $user) {
            return $user->roles()->where('name', 'Staff')->exists();
        });
    }
}
