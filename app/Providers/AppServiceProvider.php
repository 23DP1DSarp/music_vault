<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
        return config('app.frontend_url') .
               '/email/verify/' .
               $notifiable->getKey() . '/' .
               sha1($notifiable->getEmailForVerification());
        });

        DB::listen(function ($query) {
        logger($query->sql);
        logger($query->bindings);
        logger($query->time);
    });
    }
}
