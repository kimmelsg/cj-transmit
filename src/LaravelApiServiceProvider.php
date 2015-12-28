<?php

namespace NavJobs\LaravelApi;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;

class LaravelApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/config/laravel-api.php' => config_path('laravel-api.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../resources/config/laravel-api.php', 'laravel-api');

        $this->app->bind(Fractal::class, function () {

            $manager = new Manager();

            $fractal = new Fractal($manager);

            $config = $this->app['config']->get('laravel-api');

            if (!empty($config['default_serializer'])) {
                $fractal = $this->setDefaultSerializer($fractal, $config['default_serializer']);
            }

            return $fractal;
        });
    }

    /**
     * Set the default serializer.
     *
     * @param $fractal
     * @param string|\League\Fractal\Serializer\SerializerAbstract $serializer
     *
     * @return mixed
     */
    protected function setDefaultSerializer($fractal, $serializer)
    {
        if ($serializer instanceof SerializerAbstract) {
            return $fractal->serializeWith($serializer);
        }

        return $fractal->serializeWith(new $serializer());
    }
}
