<?php

namespace Linhnh95\Laravel5Layer\BaseFile\Command;

use Illuminate\Console\GeneratorCommand;

class CreateDatabaseBuilderCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linh-5layer:base-db-builder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Base DB Builder Class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'linh-5layer base db-builder';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'database-builder.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'Database';
    }
}
