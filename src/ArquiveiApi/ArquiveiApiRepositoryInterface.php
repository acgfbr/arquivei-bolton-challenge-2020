<?php

namespace Src\ArquiveiApi;

interface ArquiveiApiRepositoryInterface
{
    public function get($status, $cursor = null);

    public function findByAccessKey($ak);

    public function getPriceByXml($xmlNF);
}
