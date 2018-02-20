<?php declare(strict_types=1);

namespace Shov\Registry;

use Illuminate\Support\ServiceProvider;
use Shov\Registry\Storage\FileStorage;

/**
 * The service provider to provide the Registry to the application
 */
class RegistryServiceProvider extends ServiceProvider
{
    /** @var bool */
    public $defer = false;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        /**
         * Here I have hardcoded the File storage for a while
         * TODO: take behaviour from published config
         */
        $this->app->singleton(Registry::class, function ($app) {
            return new Registry(new FileStorage(), new FileStorage());
        });
    }
}