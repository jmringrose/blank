<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register a helper function to use remote SQLite database
        $this->app->singleton('db.sqlite.remote', function ($app) {
            return new class {
                /**
                 * Use the remote SQLite database connection
                 *
                 * @return void
                 */
                public function use(): void
                {
                    Config::set('database.default', 'sqlite_remote');
                }

                /**
                 * Use the local SQLite database connection
                 *
                 * @return void
                 */
                public function useLocal(): void
                {
                    Config::set('database.default', 'sqlite');
                }

                /**
                 * Get the current SQLite connection name
                 *
                 * @return string
                 */
                public function current(): string
                {
                    return Config::get('database.default');
                }

                /**
                 * Check if using remote SQLite connection
                 *
                 * @return bool
                 */
                public function isRemote(): bool
                {
                    return Config::get('database.default') === 'sqlite_remote';
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::prefix('api')->middleware('api')->group(base_path('routes/api.php'));

    }

}
