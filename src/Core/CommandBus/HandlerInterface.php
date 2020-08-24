<?php 

namespace Src\Core\CommandBus;

interface HandlerInterface {

    public function handle(CommandInterface $command);
}
 