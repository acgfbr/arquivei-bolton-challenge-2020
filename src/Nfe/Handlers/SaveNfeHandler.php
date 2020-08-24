<?php  

namespace Src\Nfe\Handlers;

use Exception;
use Illuminate\Support\Facades\Log;
use Src\ArquiveiApi\ArquiveiApiRepositoryInterface;
use Src\Core\CommandBus\CommandInterface;
use Src\Core\CommandBus\HandlerInterface;
use Src\Nfe\Nfe;
use Src\Nfe\NfeRepositoryInterface;

class SaveNfeHandler implements HandlerInterface {

    /**
     * @var \Src\Nfe\NfeRepositoryInterface
     */
    private $nfeRepository;
    /**
     * @var \Src\ArquiveiApi\ArquiveiApiRepositoryInterface
     */
    private $arquiveiApi;


    public function __construct(NfeRepositoryInterface $nfeRepository, ArquiveiApiRepositoryInterface $arquiveiApi)
    {
        Log::debug(__CLASS__  . ' called');
        $this->nfeRepository = $nfeRepository;
        $this->arquiveiApi = $arquiveiApi;
    }

    public function handle(CommandInterface $command)
    {
        return $this->save($command);
    }

    protected function save($command)
    {
        $exist = $this->nfeRepository->findNfeByAccessKey($command->access_key);
        if($exist){
            return $exist;
        }
        
        $nfeInstance = new Nfe();
        $nfeInstance->xml = base64_decode($command->xml);
        $nfeInstance->total_value = $this->arquiveiApi->getPriceByXml($nfeInstance->xml);
        $nfeInstance->access_key = $command->access_key;

        return $this->nfeRepository->save($nfeInstance);
    }
} 