<?php  

namespace Src\Core\CommandBus; 

use Illuminate\Support\ServiceProvider;
use Src\ArquiveiApi\ArquiveiApiRepository;
use Src\ArquiveiApi\ArquiveiApiRepositoryInterface;
use Src\Nfe\NfeRepository;
use Src\Nfe\NfeRepositoryInterface;

class CommandBusServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('Src\Core\CommandBus\CommandBus', function($app)
        {
            return new CommandBus( $app, new CommandNameInflector );
        });
        
        $this->app->bind(NfeRepositoryInterface::class, NfeRepository::class);
        $this->app->bind(ArquiveiApiRepositoryInterface::class, ArquiveiApiRepository::class);
    }
} 