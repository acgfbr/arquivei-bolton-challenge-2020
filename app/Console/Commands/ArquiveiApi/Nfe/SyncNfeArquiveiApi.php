<?php

namespace App\Console\Commands\ArquiveiApi\Nfe;

use App\Jobs\ArquiveiApi\Nfe\SyncNfeArquiveiApiJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncNfeArquiveiApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arquivei:api:sync:nfe {--status=received}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando sincroniza as nfes da api com o banco local.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $status = $this->option('status') ?? null;
        
        Log::info('dispatching');
        
        dispatch((new SyncNfeArquiveiApiJob($status))->onQueue(config('queue.queues.nfe.sync.1')));

        Log::info('dispatched a job to sync queue');
    }
}
