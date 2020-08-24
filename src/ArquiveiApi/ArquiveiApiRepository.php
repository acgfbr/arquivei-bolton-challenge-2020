<?php

namespace Src\ArquiveiApi;

use Exception;
use Src\ArquiveiApi\Exceptions\TooManyRequestsException;

class ArquiveiApiRepository implements ArquiveiApiRepositoryInterface
{

    const ENDPOINT_NFE_RECEIVED = 'https://sandbox-api.arquivei.com.br/v1/nfe/received';
    const ENDPOINT_NFE_EMITTED = 'not_implemented';

    // Guzzle client
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client(['headers' => array(
            'Content-Type' => 'application/json',
            'x-api-id' => 'f96ae22f7c5d74fa4d78e764563d52811570588e',
            'x-api-key' => 'cc79ee9464257c9e1901703e04ac9f86b0f387c2'
        )]);
    }

    /**
     * Parse price of xml
     *
     * @return string
     */
    public function getPriceByXml($xmlNF)
    {
        $xml = simplexml_load_string($xmlNF);

        if (isset($xml->infNFe->total->ICMSTot->vNF)) {
            return ((string) $xml->infNFe->total->ICMSTot->vNF);
        } else {
            return ((string) $xml->NFe->infNFe->total->ICMSTot->vNF);
        }
    }

    /**
     * Get all nfes.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($status, $cursor = null)
    {
        $response = null;
        if ($cursor) {
            $response = $this->client->request('GET', $cursor);
        } else if ($status === 'received') {
            $response = $this->client->request('GET', self::ENDPOINT_NFE_RECEIVED);
        } else if ($status === 'emmited') {
            $response = $this->client->request('GET', self::ENDPOINT_NFE_EMITTED);
        } else {
            throw new Exception('status not configured');
        }

        $headers = $response->getHeaders();
        if ($headers['X-RateLimit-Remaining'][0] == '0') {
            throw new TooManyRequestsException('many requests');
        }

        $response = $response->getBody()->getContents();

        return json_decode($response);
    }

    /**
     * Find a nfe by access key.
     *
     * @param $id
     *
     * @return mixed
     */
    public function findByAccessKey($ak)
    {
        $response = $this->client->request('GET', self::ENDPOINT_NFE_RECEIVED . '?access_key[]=' . $ak, ['http_errors' => false]);

        $headers = $response->getHeaders();
        if ($headers['X-RateLimit-Remaining'][0] == '0') {
            throw new TooManyRequestsException('many requests');
        }

        $response = $response->getBody()->getContents();

        $row = json_decode($response);

        // if api return error 400 for example
        /*
            {#280
                +"status": {#282
                    +"code": 400
                    +"message": "Bad Request"
                }
                +"errors": {#286
                    +"headers": {#285}
                    +"original": {#278
                    +"status": {#279
                        +"code": 400
                        +"message": "Bad Request"
                    }
                    +"error": {#273
                        +"access_key.0": array:1 [
                        0 => "The access_key.0 must be 44 characters."
                        ]
                    }
                    }
                    +"exception": null
                }
            }
        */
        if (isset($row->errors)) {
            return null;
        }

        $row = $row->data[0];

        return $row;
    }
}
