<?php  

namespace Src\Nfe\Commands;

use Src\Core\CommandBus\CommandInterface;

class SaveNfeCommand implements CommandInterface {

    /**
     * @var array
     */
    public $data;

    public function __construct(Array $data)
    {
        $this->data = $data;
    }

    public function __get($property)
    {
        if( isset($this->data[$property]) )
        {
            return $this->data[$property];
        }

        return null;
    }
} 