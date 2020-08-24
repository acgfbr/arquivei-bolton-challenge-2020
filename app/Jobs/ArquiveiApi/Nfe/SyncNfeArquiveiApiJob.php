<?php

namespace App\Jobs\ArquiveiApi\Nfe;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Src\ArquiveiApi\Commands\FindAllArquiveiApiCommand;
use Src\ArquiveiApi\Exceptions\TooManyRequestsException;
use Src\Nfe\Commands\SaveNfeCommand;

class SyncNfeArquiveiApiJob implements ShouldQueue
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

    private $status;
    private $cursor;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $status = null, string $cursor = null)
    {
        $this->status = $status;
        $this->cursor = $cursor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bus = app('Src\Core\CommandBus\CommandBus');
        try {
            $command = new FindAllArquiveiApiCommand(['status' => $this->status, 'cursor' => $this->cursor]);

            $response = $bus->execute($command);

            if ($response instanceof TooManyRequestsException) {
                // retenta em 1 min
                return $this->release(60);
            }

            if (isset($response->data) && is_array($response->data) && count($response->data) > 0) {
                dispatch((new SyncNfeArquiveiApiJob($this->status, $response->page->next))->onQueue(config('queue.queues.nfe.sync.1')));
                foreach ($response->data as $row) {
                    $command = new SaveNfeCommand(['xml' => $row->xml, 'access_key' => $row->access_key]);
                    $bus->execute($command);
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        Log::info('executing core command');
    }
}
