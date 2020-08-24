<?php

namespace App\Jobs\ArquiveiApi\Nfe;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Src\ArquiveiApi\Commands\FindOneArquiveiApiCommand;
use Src\ArquiveiApi\Exceptions\TooManyRequestsException;
use Src\Nfe\Commands\SaveNfeCommand;

class SyncOneNfeArquiveiApiJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    private $access_key;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $access_key)
    {
        $this->access_key = $access_key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bus = app('Src\Core\CommandBus\CommandBus');

        $command = new FindOneArquiveiApiCommand(['access_key' => $this->access_key]);
        $response = $bus->execute($command);

        if ($response instanceof TooManyRequestsException) {
            // retenta em 1 min
            return $this->release(60);
        }

        if (isset($response) && is_object($response)) {
            $row = $response;
            $command = new SaveNfeCommand(['xml' => $row->xml, 'access_key' => $row->access_key]);
            $bus->execute($command);
        }

        Log::info('executing core command');
    }
}
