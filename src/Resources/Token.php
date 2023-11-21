<?php

namespace PetervdBroek\iDEAL2\Resources;

class Token extends Base
{
    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->response['access_token'];
    }
}
