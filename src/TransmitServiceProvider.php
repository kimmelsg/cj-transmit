<?php

namespace NavJobs\Transmit;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;

class TransmitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/config/transmit.php' => config_path('transmit.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../resources/config/transmit.php', 'transmit');

        $this->app->bind(Fractal::class, function () {

            $manager = new Manager();

            $fractal = new Fractal($manager);

            $config = $this->app['config']->get('transmit');

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
