<?php

namespace W1p\LumenYunxin;

use Illuminate\Support\ServiceProvider;

class YunXinServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/yunxin.php' => config('yunxin.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('YunXin', function () {
            $appKey = config('yunxin.app_key');
            $appSecret = config('yunxin.app_secret');
            return new Entrance($appKey, $appSecret);
        });
    }
}
