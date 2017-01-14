<?php

namespace App\Providers;

use App\Form;
use App\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->bootGoogleCustomSocialite();

        // Model Observers
        // TODO: Move to App\Observers directory on 5.3 upgrade

        Response::deleted(function ($response){
            $response->reviews()->delete();
        });

        Form::deleted(function ($form){
            $form->responses()->each(function ($response, $key){
                $response->delete();
            });
            $form->users()->detach();
        });
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


    private function bootGoogleCustomSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'google', // extending default google with new full token functionality
            function ($app) use ($socialite) {
                $config = $app['config']['services.google'];
                return $socialite->buildProvider('App\Providers\GoogleCustomProvider', $config);
            }
        );
    }
}
