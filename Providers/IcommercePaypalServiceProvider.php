<?php

namespace Modules\Icommercepaypal\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Icommercepaypal\Events\Handlers\RegisterIcommercePaypalSidebar;

class IcommercePaypalServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterIcommercePaypalSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('paypalconfigs', array_dot(trans('icommercepaypal::paypalconfigs')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('Icommercepaypal', 'permissions');
        $this->publishConfig('Icommercepaypal', 'settings');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Icommercepaypal\Repositories\PaypalconfigRepository',
            function () {
                $repository = new \Modules\Icommercepaypal\Repositories\Eloquent\EloquentPaypalconfigRepository(new \Modules\Icommercepaypal\Entities\Paypalconfig());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Icommercepaypal\Repositories\Cache\CachePaypalconfigDecorator($repository);
            }
        );
// add bindings

    }
}
