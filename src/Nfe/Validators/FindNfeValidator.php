<?php  

namespace Src\Nfe\Validators;

use Illuminate\Validation\Factory;
use Src\Core\CommandBus\CommandInterface;
use Src\Core\Validation\ValidationException;
use Src\Core\Validation\ValidatorInterface;

class FindNfeValidator implements ValidatorInterface {

    /**
     * @var \Illuminate\Validation\Factory
     */
    private $validator;

    /**
     * @var array
     */
    protected $rules = [
        'access_key' => 'required'
    ];

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param \Src\Core\CommandBus\CommandInterface $command
     * @throws \Src\Core\Validation\ValidationException
     */
    public function validate(CommandInterface $command)
    {
        $validator = $this->validator->make([
            'access_key' => $command->access_key
        ], $this->rules);

        if( ! $validator->passes() )
        {
            throw new ValidationException( $validator->errors() );
        }
    }
}