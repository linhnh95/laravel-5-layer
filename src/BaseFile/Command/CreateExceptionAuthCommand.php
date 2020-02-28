<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 2/28/2020
 * Time: 11:28 AM
 */

namespace Linhnh95\Laravel5Layer\BaseFile\Command;

use Illuminate\Console\GeneratorCommand;

class CreateExceptionAuthCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linh-5layer:base-ex-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Base Ex Auth Class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'linh-5layer base ex-auth';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'exception-auth.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . DIRECTORY_SEPARATOR . 'Exceptions';
    }
}
