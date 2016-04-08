<?php

namespace Kolovious\MeliSocialite;

use Illuminate\Support\ServiceProvider;

class MeliSocialiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'meli',
            function ($app) use ($socialite) {
                $config = $app['config']['services.meli'];
                return $socialite->buildProvider(MeliSocialite::class, $config);
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
