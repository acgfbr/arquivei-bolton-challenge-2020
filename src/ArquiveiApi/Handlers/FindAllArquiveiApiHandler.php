<?php  

namespace Src\ArquiveiApi\Handlers;

use Illuminate\Support\Facades\Log;
use Src\ArquiveiApi\ArquiveiApiRepositoryInterface;
use Src\Core\CommandBus\CommandInterface;
use Src\Core\CommandBus\HandlerInterface;

class FindAllArquiveiApiHandler implements HandlerInterface {

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
        return $this->findAll($command);
    }

    protected function findAll($command)
    {
        return $this->repository->get($command->status,$command->cursor);
    }
} 