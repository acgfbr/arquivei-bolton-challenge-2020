<?php  

namespace Src\Core\CommandBus;

class CommandNameInflector {

    public function getHandler(CommandInterface $command)
    {
        return str_replace('Command', 'Handler', get_class($command));
    }
} 