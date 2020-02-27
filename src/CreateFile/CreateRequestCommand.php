<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 2/27/2020
 * Time: 8:18 PM
 */

namespace Linhnh95\Laravel5Layer\CreateFile;


use Illuminate\Console\GeneratorCommand;

class CreateRequestCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linh-5layer:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Request Class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'linh-5layer request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'request.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $name = $this->getNameInput();
        $replaces = ['Create', 'Update', 'List', 'Find', 'Delete', 'Request'];
        foreach ($replaces as $replace) {
            $name = str_replace($replace, '', $name);
        }
        return $rootNamespace . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $name;
    }
}
