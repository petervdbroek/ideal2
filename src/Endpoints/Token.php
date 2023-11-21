<?php

namespace PetervdBroek\iDEAL2\Endpoints;

use PetervdBroek\iDEAL2\Exceptions\ClientNotSetException;
use PetervdBroek\iDEAL2\Exceptions\MerchantIdNotSetException;

class Token extends Base
{
    protected string $endpoint = '/xs2a/routingservice/services/authorize/token';
    protected string $method = 'POST';

    /**
     * @return array
     * @throws ClientNotSetException
     * @throws MerchantIdNotSetException
     */
    public function getOptions(): array
    {
        return [
            'headers' => $this->getHeaders(),
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ];
    }

    /**
     * @return array
     * @throws ClientNotSetException
     * @throws MerchantIdNotSetException
     */
    private function getHeaders(): array
    {
        return [
            'App' => $this->iDEAL::APP,
            'Client' => $this->iDEAL->getClient(),
            'Id' => $this->iDEAL->getMerchantId(),
            'Date' => date('c')
        ];
    }
}
