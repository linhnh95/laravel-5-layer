<?php

namespace Linhnh95\Laravel5Layer\CreateFile;


use Illuminate\Console\GeneratorCommand;

class CreateDependencyCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linh-5layer:dependency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Dependency Class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'linh-5layer dependency';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'dependency.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . DIRECTORY_SEPARATOR . 'Dependency';
    }

    /**
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $nameBase = $this->qualifyClass($this->getNameInput());
        $nameInput = 'SQL' . $this->getNameInput();
        $name = $this->qualifyClass($nameInput);
        $path = $this->getPath($name);
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($nameInput)) {
            $this->error($this->type . ' already exists!');
            return false;
        }
        $this->makeDirectory($path);
        if (method_exists($this, 'sortImports')) {
            $this->files->put($path, $this->sortImports($this->buildClass($nameBase)));
        } else {
            $this->files->put($path, $this->buildClass($nameBase));
        }
        $this->info($this->type . ' created successfully.');
    }
}

