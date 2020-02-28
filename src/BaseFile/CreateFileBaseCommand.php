<?php

namespace Linhnh95\Laravel5Layer\BaseFile;


use Linhnh95\Laravel5Layer\Helpers\CommandHelpers;
use Illuminate\Console\Command;

class CreateFileBaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linh-5layer:base-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Base File Of Linh Nguyen Hong';

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
        ini_set('max_execution_time', 180000);
        ini_set('memory_limit', '-1');

        $this->callSilent('linh-5layer:base-business', ['name' => 'ABusiness']);
        $this->callSilent('linh-5layer:base-exception', ['name' => 'AException']);
        $this->callSilent('linh-5layer:base-formrequest', ['name' => 'AFormRequest']);
        $this->callSilent('linh-5layer:base-sql', ['name' => 'ASQLQuery']);
        $this->callSilent('linh-5layer:base-db-builder', ['name' => 'Builder']);
        $this->callSilent('linh-5layer:base-db-cache', ['name' => 'CacheQueryBuilder']);
        $this->callSilent('linh-5layer:base-ex-server', ['name' => 'ServerException']);
        $this->callSilent('linh-5layer:base-ex-validate', ['name' => 'ValidationException']);
        $this->callSilent('linh-5layer:base-help-jwt', ['name' => 'JWTHelpers']);
        $this->callSilent('linh-5layer:base-help-price', ['name' => 'PriceHelpers']);
        $this->callSilent('linh-5layer:base-help-query', ['name' => 'QueryHelpers']);
        $this->callSilent('linh-5layer:base-help-redis', ['name' => 'RedisHelper']);
        $this->callSilent('linh-5layer:base-help-rsa', ['name' => 'RSAHelpers']);
        $this->callSilent('linh-5layer:base-ibusiness', ['name' => 'AInterfaceBusiness']);
        $this->callSilent('linh-5layer:base-idependency', ['name' => 'AInterface']);
        $this->callSilent('linh-5layer:base-provider', ['name' => 'RepositoryServiceProvider']);
        $this->callSilent('linh-5layer:base-redis-cache', ['name' => 'CacheRedisManager']);
        $this->callSilent('linh-5layer:base-redis-session', ['name' => 'SessionRedisManager']);
        $this->callSilent('linh-5layer:base-trait-json', ['name' => 'JsonResponseTrait']);

        $this->info('Create file base success');
    }
}
