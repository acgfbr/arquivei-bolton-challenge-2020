<?php  

namespace Src\Nfe; 


interface NfeRepositoryInterface {

    public function findNfeByAccessKey(string $accessKey);

    public function save(Nfe $nfe);
}