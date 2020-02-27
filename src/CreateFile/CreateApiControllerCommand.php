<?php

namespace Linhnh95\Laravel5Layer\CreateFile;


use Illuminate\Console\GeneratorCommand;

class CreateApiControllerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linh-5layer:api-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Api Controller Class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'linh-5layer api controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Api';
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);
        $stub = $this->replaceVariable($stub);
        return $stub;
    }

    /**
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $nameBase = $this->qualifyClass($this->getNameInput());
        $nameInput = $this->getNameInput() . 'Controller';
        $name = $this->qualifyClass($nameInput);
        $path = $this->getPath($name);
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($nameInput)) {
            $this->error($this->type . ' already exists!');
            return false;
        }
        $this->makeDirectory($path);
        $this->files->put($path, $this->sortImports($this->buildClass($nameBase)));
        $this->info($this->type . ' created successfully.');
    }

    /**
     * @param $stub
     *
     * @return mixed
     */
    protected function replaceVariable($stub)
    {
        $variable = lcfirst($this->getNameInput());
        $stub = str_replace('{{variable}}', $variable, $stub);
        return $stub;
    }
}
