<?php

namespace App\Providers;

//use Illuminate\Contracts\Foundation\Application;
//use Illuminate\Support\Facades\Auth;
use App\Libs\Setting\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use PHPUnit\TextUI\Application;
use Tymon\JWTAuth\JWTGuard;

//use Tymon\JWTAuth\JWTGuard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('setting', function () {
            return new Setting();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::channel('db_query')->info(json_encode($query));
            });
        }
    }
}
