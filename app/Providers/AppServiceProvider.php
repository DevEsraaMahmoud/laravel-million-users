<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
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
        // Set default string length to avoid MySQL utf8mb4 key length issues
        Schema::defaultStringLength(191);
        
        Vite::prefetch(concurrency: 3);

        // Enable database query logging in development
        if ($this->app->environment('local', 'development')) {
            $this->enableQueryLogging();
        }

        // Global exception handler for unhandled exceptions
        $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class)
            ->renderable(function (\Throwable $e) {
                if ($this->app->environment('production')) {
                    Log::error('Unhandled exception', [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            });
    }

    /**
     * Enable database query logging in development.
     */
    private function enableQueryLogging(): void
    {
        DB::listen(function (QueryExecuted $query) {
            // Log slow queries (queries taking more than 100ms)
            if ($query->time > 100) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                ]);
            }

            // Log all queries in development (can be disabled via config)
            if (config('app.log_queries', true)) {
                Log::debug('Database query executed', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                ]);
            }
        });
    }
}
