<?php

namespace App\Providers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
		\App\Models\Topic::observe(\App\Observers\TopicObserver::class);
        \App\Models\Link::observe(\App\Observers\LinkObserver::class);

        \Carbon\Carbon::setLocale('zh');
    }

    /**
     * Register any application services.
     *
     * @return void
     */

    public function register()
    {
        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }

        // transformer 的三种返回格式
	    app('Dingo\Api\Transformer\Factory')->setAdapter(function ($app) {
		    $fractal = new \League\Fractal\Manager;
		    // 自定义的和fractal提供的
//		    $serializer = new \League\Fractal\Serializer\DataArraySerializer();
		    $serializer = new \League\Fractal\Serializer\ArraySerializer();
//		     $serializer = new \League\Fractal\Serializer\JsonApiSerializer();
		    $fractal->setSerializer($serializer);
		    return new \Dingo\Api\Transformer\Adapter\Fractal($fractal);
	    });

        \API::error(function (ModelNotFoundException $exception) {
			abort(404);
        });

        \API::error(function (AuthorizationException $exception) {
			abort(403);
        });
    }
}
