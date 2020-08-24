<?php  

namespace Src\ArquiveiApi\Handlers;

use Illuminate\Support\Facades\Log;
use Src\ArquiveiApi\ArquiveiApiRepositoryInterface;
use Src\Core\CommandBus\CommandInterface;
use Src\Core\CommandBus\HandlerInterface;

class FindOneArquiveiApiHandler implements HandlerInterface {

    /**
     * @var \Src\ArquiveiApi\ArquiveiApiRepositoryInterface
     */
    private $repository;


    public function __construct(ArquiveiApiRepositoryInterface $repository)
    {
        Log::info('handler called');
        $this->repository = $repository;
    }

    public function handle(CommandInterface $command)
    {
        return $this->findByAccessKey($command);
    }

    protected function findByAccessKey($command)
    {
        return $this->repository->findByAccessKey($command->access_key);
    }
} 