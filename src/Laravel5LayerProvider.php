<?php

namespace Linhnh95\Laravel5Layer;

use Illuminate\Support\ServiceProvider;

class Laravel5LayerProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerHelpers();

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateFile\CreateApiControllerCommand::class,
                CreateFile\CreateBusinessCommand::class,
                CreateFile\CreateDependencyCommand::class,
                CreateFile\CreateEloquentCommand::class,
                CreateFile\CreateIBusinessCommand::class,
                CreateFile\CreateIDependencyCommand::class,
                CreateFile\CreateRequestCommand::class,
                CreateFile\CreateResponseCommand::class,
                CreateFile\CreateWebControllerCommand::class,
                BaseFile\CreateFileBaseCommand::class,
                BaseFile\Command\CreateABusinessCommand::class,
                BaseFile\Command\CreateAExceptionCommand::class,
                BaseFile\Command\CreateAFormRequestCommand::class,
                BaseFile\Command\CreateASQLCommand::class,
                BaseFile\Command\CreateDatabaseBuilderCommand::class,
                BaseFile\Command\CreateDatabaseCacheCommand::class,
                BaseFile\Command\CreateExceptionServerCommand::class,
                BaseFile\Command\CreateExceptionValidateCommand::class,
                BaseFile\Command\CreateHelperJWTCommand::class,
                BaseFile\Command\CreateHelperPriceCommand::class,
                BaseFile\Command\CreateHelperQueryCommand::class,
                BaseFile\Command\CreateHelperRedisCommand::class,
                BaseFile\Command\CreateHelperRSACommand::class,
                BaseFile\Command\CreateIBusinessCommand::class,
                BaseFile\Command\CreateIDependencyCommand::class,
                BaseFile\Command\CreateProviderCommand::class,
                BaseFile\Command\CreateRedisCacheCommand::class,
                BaseFile\Command\CreateRedisSessionCommand::class,
                BaseFile\Command\CreateTraitJsonCommand::class
            ]);
        }
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register custom helpers
     */
    protected function registerHelpers()
    {

    }
}
