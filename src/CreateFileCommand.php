<?php

namespace Linhnh95\Laravel5Layer;


use Linhnh95\Laravel5Layer\Helpers\CommandHelpers;
use Illuminate\Console\Command;

class CreateFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linh-5layer:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create File Of Linh Nguyen Hong';

    /**
     * @var CommandHelpers
     */
    private $commandHelper;

    /**
     * CreateFileCommand constructor.
     *
     * @param CommandHelpers $commandHelper
     */
    public function __construct(CommandHelpers $commandHelper)
    {
        $this->commandHelper = $commandHelper;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->callSilent('linh-5layer:ibusiness', ['name' => $this->argument('name')]);
        $this->callSilent('linh-5layer:idependency', ['name' => $this->argument('name')]);
        $this->callSilent('linh-5layer:business', ['name' => $this->argument('name')]);
        $this->callSilent('linh-5layer:dependency', ['name' => $this->argument('name')]);
        $this->callSilent('linh-5layer:eloquent', ['name' => $this->argument('name')]);
        $this->callSilent('linh-5layer:web-controller', ['name' => $this->argument('name')]);
        $this->callSilent('linh-5layer:api-controller', ['name' => $this->argument('name')]);
        $this->callSilent('linh-5layer:response', ['name' => $this->argument('name')]);
        $requests = [
            'Create' . $this->argument('name') . 'Request',
            'Update' . $this->argument('name') . 'Request',
            'List' . $this->argument('name') . 'Request',
            'Find' . $this->argument('name') . 'Request',
            'Delete' . $this->argument('name') . 'Request'
        ];
        foreach ($requests as $request) {
            $this->callSilent('linh-5layer:request', ['name' => $request]);
        }
        $this->createProviders($this->argument('name'));
        $this->createConfig($this->argument('name'));

        $this->info('Create file success');
    }

    /**
     * @param $name
     */
    private function createProviders($name)
    {
        $stringAddNew = '$this->app->bind(' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Abstraction' . DIRECTORY_SEPARATOR . 'Business' . DIRECTORY_SEPARATOR . 'I' . $name . 'Business::class, ' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Business' . DIRECTORY_SEPARATOR . $name . 'Business::class);' . "\n" . '        $this->app->bind(' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Abstraction' . DIRECTORY_SEPARATOR . 'Dependency' . DIRECTORY_SEPARATOR . 'I' . $name . 'Dependency::class, ' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Dependency' . DIRECTORY_SEPARATOR . 'SQL' . $name . '::class);' . "\n" . '        ';
        $file = $this->commandHelper->getAppPath() . '/Providers/RepositoryServiceProvider.php';
        $contentFile = file_get_contents($file);
        $changeContent = substr($contentFile, strripos($contentFile, '/**BEGIN CONFIG**/'));
        $changeContent = substr($changeContent, 0, strripos($changeContent, '/**END CONFIG**/'));
        $stringReplace = $changeContent . $stringAddNew;
        $afterString = str_replace($changeContent, $stringReplace, $contentFile);
        file_put_contents($file, '');
        file_put_contents($file, $afterString);
    }

    /**
     * @param $name
     */
    private function createConfig($name)
    {
        $fileConfigFile = base_path('config/tables.php');
        $table = $table = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', $name)), '_');
        if (!file_exists($fileConfigFile)) {
            $file = fopen($fileConfigFile, "w");
            $textFile = "<?php \nreturn [\n     //**CONFIG**//\n  " . $table . " => " . $table . ",\n    //**END_CONFIG**//\n];";
            fwrite($file, $textFile);
            fclose($file);
        } else {
            $fileConfig = file_get_contents($fileConfigFile);
            $stringAddNew = $table . " => " . $table . ",\n";
            $changeContent = substr($fileConfig, strripos($fileConfig, '//**CONFIG**//'));
            $changeContent = substr($changeContent, 0, strripos($changeContent, '//**END_CONFIG**//'));
            $stringReplace = $changeContent . $stringAddNew;
            $afterString = str_replace($changeContent, $stringReplace, $fileConfig);
            file_put_contents($fileConfigFile, '');
            file_put_contents($fileConfigFile, $afterString);
        }
    }
}
