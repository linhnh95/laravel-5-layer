<?php

namespace Linhnh95\Laravel5Layer;


use Linhnh95\Laravel5Layer\Helpers\CommandHelpers;
use Illuminate\Console\Command;

class CreateStructureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linh-5layer:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Structure Of Linh Nguyen Hong';

    /**
     * @var CommandHelpers
     */
    private $commandHelper;

    /**
     * CreateStructureCommand constructor.
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
        if ($this->confirm('Your default structure will be changed, some files may lost while changing directories, are you sure to run?')) {
            $appDirectory = $this->commandHelper->getAppPath();
            $existFilesToMove = $this->commandHelper->existFilesToMove();
            $baseAppNamespace = $this->commandHelper->getAppNamespace();
            $bootstrapApp = $this->commandHelper->getBasePath() . '/bootstrap/app.php';

            //Make folder structure
            if (!is_dir($appDirectory)) {
                mkdir($appDirectory);
            }
            $this->commandHelper->parseAndMakeDirRecursive($appDirectory, $this->getFolderStructure());

            //Move exist files to new folder & rename Namespace
            foreach ($existFilesToMove as $source => $destination) {
                $sourceFullDir = $appDirectory . '/' . $source;
                $destinationFullDir = $appDirectory . '/' . $destination;
                $this->commandHelper->copyFolderAndFilesRecursive($sourceFullDir, $destinationFullDir);
                $this->commandHelper->removeFolderAndFilesRecursive($sourceFullDir);
                $this->commandHelper->renameNamespaceRecursive($destinationFullDir, $baseAppNamespace . $source, $baseAppNamespace . str_replace('/', '\\', $destination));
            }

            //Modify bootstrap/app.php
            foreach ($existFilesToMove as $source => $destination) {
                $this->commandHelper->renameNamespaceRecursive($bootstrapApp, $baseAppNamespace . str_replace('/', '\\', $source), $baseAppNamespace . str_replace('/', '\\', $destination));
            }

            //Create Base File
            $basePath = __DIR__ . '/BaseFile';
            $this->commandHelper->copyFileFromFileBaseRecursive($this->getFileForStructure(), $appDirectory, $basePath);

            //$this->changeAppConfig();
            $this->changeCacheConfig();

            $this->info('Success initialization.');
        }
    }

    /**
     * @return array
     */
    private function getFolderStructure()
    {
        return [
            'Abstraction' => [
                'Business' => [],
                'Dependency' => []
            ],
            'Business' => [],
            'Common' => [
                'Database' => [],
                'Redis' => [],
                'Traits' => [],
                'Helpers' => [],
                'Logging' => []
            ],
            'Dependency' => [],
            'Models' => [],
            'Http' => [
                'Controllers' => [
                    'Api' => [],
                    'Web' => []
                ],
                'Response' => [],
                'Requests' => []
            ],
            'Providers' => []
        ];
    }

    /**
     * List File Base Create When Init
     *
     * @return array
     */
    private function getFileForStructure()
    {
        $basePath = __DIR__ . '/BaseFile';
        return [
            'Abstraction' => [
                'Business' => [
                    'AInterfaceBusiness.php' => $basePath . '/Abstraction/Business/AInterfaceBusiness.php'
                ],
                'Dependency' => [
                    'AInterface.php' => $basePath . '/Abstraction/Dependency/AInterface.php'
                ]
            ],
            'Business' => [
                'ABusiness.php' => $basePath . '/Business/ABusiness.php'
            ],
            'Common' => [
                'Database' => [
                    'Builder.php' => $basePath . '/Common/Database/Builder.php',
                    'CacheQueryBuilder.php' => $basePath . '/Common/Database/CacheQueryBuilder.php'
                ],
                'Helpers' => [
                    'JWTHelpers.php' => $basePath . '/Common/Helpers/JWTHelpers.php',
                    'QueryHelpers.php' => $basePath . '/Common/Helpers/QueryHelpers.php',
                    'RedisHelper.php' => $basePath . '/Common/Helpers/RedisHelper.php',
                    'RSAHelpers.php' => $basePath . '/Common/Helpers/RSAHelpers.php'
                ],
                'Redis' => [
                    'CacheRedisManager.php' => $basePath . '/Common/Redis/CacheRedisManager.php',
                    'SessionRedisManager.php' => $basePath . '/Common/Redis/SessionRedisManager.php'
                ],
                'Traits' => [
                    'JsonResponseTrait.php' => $basePath . '/Common/Traits/JsonResponseTrait.php'
                ],
            ],
            'Dependency' => [
                'ASQLQuery.php' => $basePath . '/Dependency/ASQLQuery.php'
            ],
            'Exceptions' => [
                'AException.php' => $basePath . '/Exceptions/AException.php',
                'ServerException.php' => $basePath . '/Exceptions/ServerException.php',
                'ValidationException.php' => $basePath . '/Exceptions/ValidationException.php',
            ],
            'Http' => [
                'Requests' => [
                    'AFormRequest.php' => $basePath . '/Http/Requests/AFormRequest.php',
                ]
            ],
            'Providers' => [
                'RepositoryServiceProvider.php' => $basePath . '/Providers/RepositoryServiceProvider.php',
            ]
        ];
    }

    private function changeAppConfig()
    {
        $appFile = $this->commandHelper->getBasePath() . '/config/app.php';
        $appConfig = file_get_contents($appFile);
        if(!strpos($appConfig, 'App\Providers\RepositoryServiceProvider::class')){
            $providerConfig = strstr($appConfig, 'providers');
            $providerConfig = strstr(ucfirst($providerConfig), 'providers');
            $providerConfig = trim(substr($providerConfig, 0, stripos($providerConfig, '],')));
            $stringReplace = $providerConfig . "\n" . '        App\Providers\RepositoryServiceProvider::class,';
            $afterString = str_replace($providerConfig, $stringReplace, $appConfig);
            file_put_contents($appFile, '');
            file_put_contents($appFile, $afterString);
        }
    }

    private function changeCacheConfig()
    {
        $cacheFile = $this->commandHelper->getBasePath() . '/config/cache.php';
        $cacheConfig = file_get_contents($cacheFile);
        $storeConfig = substr($cacheConfig, strripos($cacheConfig, 'stores'));
        $storeConfig = substr($storeConfig, 0, strripos($storeConfig, '],'));
        $stringReplace = $storeConfig . "    'request' => [\n            'driver' => 'array'\n        ]\n";
        $afterString = str_replace($storeConfig, $stringReplace, $cacheConfig);
        file_put_contents($cacheFile, '');
        file_put_contents($cacheFile, $afterString);
    }
}
