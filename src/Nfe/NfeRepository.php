<?php  

namespace Src\Nfe; 


class NfeRepository implements NfeRepositoryInterface {

    /**
     * @var Nfe
     */
    private $nfe;

    public function __construct(Nfe $nfe)
    {
        $this->nfe = $nfe;
    }

    public function findNfeByAccessKey(string $accessKey):?Nfe{
        return $this->nfe->where('access_key',$accessKey)->first();
    }
    
    public function save(Nfe $nfe)
    {

        return $nfe->save();
    }
} 