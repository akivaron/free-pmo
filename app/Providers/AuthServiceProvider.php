<?php

namespace App\Providers;

use App\Entities\Users\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Entities\Users\Event' => 'App\Policies\EventPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            Gate::define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

        Gate::define('add_project', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage_project', function ($user, $project) {
            return $user->id == $project->owner_id;
        });

        Gate::define('manage_features', function ($user, $project) {
            return $user->id == $project->owner_id;
        });

        Gate::define('manage_feature', function ($user, $feature) {
            return $user->id == $feature->worker_id;
        });
    }

    /**
     * Fetch the collection of site permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
