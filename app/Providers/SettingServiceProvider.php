<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config; // <-- এটা নতুন
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // settings টেবিল আছে কি না চেক
        if (Schema::hasTable('settings')) {
            $webSetting = DB::table('settings')
                ->where('slug', 'web_setting')
                ->value('contents');

            $decodedSetting = $webSetting ? json_decode($webSetting, true) : [];

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedSetting)) {
                $decodedSetting = []; // JSON ভাঙলে খালি অ্যারে
            }

            view()->share('webSetting', $decodedSetting);
            Config::set('webSetting', $decodedSetting);
        } else {
            // টেবিল না থাকলে সেফ fallback
            view()->share('webSetting', []);
            Config::set('webSetting', []);
        }
    }
}
