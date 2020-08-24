<?php  

namespace Src\Core\CommandBus; 

interface CommandBusInterface {

    public function execute(CommandInterface $command);
} 