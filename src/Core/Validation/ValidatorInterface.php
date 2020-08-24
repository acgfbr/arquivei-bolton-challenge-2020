<?php  

namespace Src\Core\Validation;

use Src\Core\CommandBus\CommandInterface;

interface ValidatorInterface {

    public function validate(CommandInterface $command);
} 