<?php

namespace PetervdBroek\iDEAL2\Banks;

use PetervdBroek\iDEAL2\iDEAL;

class RabobankiDEAL extends iDEAL
{
    /**
     * Endpoints array
     * @var array
     */
    protected array $baseUri = ['prod' => 'https://ideal.rabobank.nl', 'test' => 'https://routingservice-rabo.awltest.de'];

    /**
     * Client, used in token requests
     * @var string
     */
    protected string $client = 'RaboiDEAL';
}
