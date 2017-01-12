<?php namespace Philf\Setting;

use Illuminate\Support\ServiceProvider;
use Philf\Setting\Adapters\File;
use Philf\Setting\interfaces\LaravelFallbackInterface;

class SettingServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Setting::class, function ($app) {
            $configName     = $app['config']['setting::setting.path'];
            return new Setting(new File(), $configName, $app['config']['setting::setting.fallback'] ? new LaravelFallbackInterface() : null);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('setting');
    }

}
