<?php

namespace PetervdBroek\iDEAL2\Resources;

class Base
{
    protected array $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }
}
