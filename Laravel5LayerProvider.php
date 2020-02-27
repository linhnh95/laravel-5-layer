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
