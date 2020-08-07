<?php

namespace PurpleMountain\SNSCommunicationRecords;

use Illuminate\FileSystem\FileSystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use PurpleMountain\SNSCommunicationRecords\SNSCommunicationRecords;
use PurpleMountain\SNSCommunicationRecords\Providers\EventServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /** 
     * Put together the path to the config file.
     *
     * @return string
     */
    private function configPath(): string
    {
        return __DIR__.'/../config/snscommunicationrecords.php';
    }

    /** 
     * Get the short name for this package.
     *
     * @return string
     */
    private function shortName(): string
    {
        return 'sns-communication-records';
    }


    /**
     * Bootstrap the package.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleRoutes();
        $this->handleConfigs();

        if (env('APP_ENV') === 'local') {
            $this->handleMigrations();
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }

    /**
     * Register anything this package needs.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'snscommunicationrecords');
        $this->app->register(EventServiceProvider::class);
    }

    /** 
     * Register any migrations.
     *
     * @return void
     */
    private function handleMigrations()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/2020_08_01_130327_create_sns_communication_records_table.php.stub' => database_path('migrations/2020_08_01_130327_create_sns_communication_records_table.php')
        ], $this->shortName() . '-migrations');
    }

    /** 
     * Register any routes this package needs.
     *
     * @return void
     */
    private function handleRoutes()
    {
        Route::group([
            'name' => $this->shortName(),
            'namespace' => 'PurpleMountain\SNSCommunicationRecords\Http\Controllers',
            'middleware' => ['web']
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        Route::group([
            'name' => $this->shortName() . '-api',
            'prefix' => 'api',
            'namespace' => 'PurpleMountain\SNSCommunicationRecords\Http\Controllers\Api',
            'middleware' => ['api']
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /** 
     * Register any config files this package needs.
     *
     * @return void
     */
    private function handleConfigs()
    {
        $this->publishes([
            $this->configPath() => config_path('snscommunicationrecords.php')
        ], 'snscommunicationrecords');
    }
}