<?php

namespace Src\Nfe\Handlers;

use App\Jobs\ArquiveiApi\Nfe\SyncOneNfeArquiveiApiJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Src\ArquiveiApi\ArquiveiApiRepositoryInterface;
use Src\Core\CommandBus\CommandInterface;
use Src\Core\CommandBus\HandlerInterface;
use Src\Nfe\Commands\FindNfeCommand;
use Src\Nfe\Exceptions\NfeNotFoundException;
use Src\Nfe\NfeRepositoryInterface;
use Src\Nfe\Validators\FindNfeValidator;
use Throwable;

class FindNfeHandler implements HandlerInterface
{

    /**
     * @var \Src\Nfe\NfeRepositoryInterface
     */
    private $nfeRepository;
    /**
     * @var \Src\ArquiveiApi\ArquiveiApiRepositoryInterface
     */
    private $arquiveiApi;

    public function __construct(FindNfeValidator $validator, NfeRepositoryInterface $nfeRepository, ArquiveiApiRepositoryInterface $arquiveiApi)
    {
        Log::debug(__CLASS__ . ' called');
        $this->nfeRepository = $nfeRepository;
        $this->arquiveiApi = $arquiveiApi;
        $this->validator = $validator;
    }

    public function handle(CommandInterface $command)
    {
        $this->validate($command);
        return $this->find($command);
    }

    protected function validate($command)
    {
        $this->validator->validate($command);
    }

    protected function find($command)
    {
        $access_key = $command->access_key;

        try {
            // primeiro procura no cache(redis), se não existir já cacheia buscando no banco local (sqlite)
            $nfeObject = Cache::rememberForever('nfe_' . $access_key, function () use ($access_key) {
                return $this->nfeRepository->findNfeByAccessKey($access_key);
            });
            // se nao existir no banco local e no cache eu busco na api da arquivei
            if (!$nfeObject) {
                $nfeObject = $this->arquiveiApi->findByAccessKey($access_key);

                // se nao existir na api da arquivei, eu somente retorno pro front end uma exception tratata no controller
                if (!$nfeObject) {
                    throw new NfeNotFoundException('nfe not found in our database and in arquivei api');
                }

                // connection sync pra nao ir pra queue e sim na mesma thread que dispara o job
                dispatch((new SyncOneNfeArquiveiApiJob($access_key))->onConnection('sync')->onQueue(config('queue.queues.nfe.sync.1')));

                $bus = app('Src\Core\CommandBus\CommandBus');

                // chamo este comando novamente (recursividade), se nao existir vai cair aqui 1x somente
                $command = new FindNfeCommand(['access_key' => $access_key]);
                return $bus->execute($command);
            }

            return $nfeObject;
        } catch (Throwable $e) {
            if ($e instanceof NfeNotFoundException) {
                return $e->getError();
            }
        }
    }
}
