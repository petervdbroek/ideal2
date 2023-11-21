<?php

namespace PetervdBroek\iDEAL2\Endpoints;

use PetervdBroek\iDEAL2\Exceptions\EndpointNotSetException;
use PetervdBroek\iDEAL2\Exceptions\MethodNotSetException;
use PetervdBroek\iDEAL2\Exceptions\NotImplementedException;
use PetervdBroek\iDEAL2\iDEAL;
use PetervdBroek\iDEAL2\Utils\Signer;
use Ramsey\Uuid\Uuid;

class Base
{
    protected string $endpoint = '';
    protected string $method = '';
    protected string $requestId = '';
    protected iDEAL $iDEAL;

    public function __construct(iDEAL $iDEAL)
    {
        $uuid = Uuid::uuid4();
        $this->requestId = $uuid->toString();
        $this->iDEAL = $iDEAL;
    }

    /**
     * @return string
     * @throws EndpointNotSetException
     */
    public function getEndpoint(): string
    {
        if ($this->endpoint === '') {
            throw new EndpointNotSetException();
        }
        return $this->endpoint;
    }

    /**
     * @return string
     * @throws MethodNotSetException
     */
    public function getMethod(): string
    {
        if ($this->method === '') {
            throw new MethodNotSetException();
        }
        return $this->method;
    }

    /**
     * Will be implemented in child class
     * @throws NotImplementedException
     */
    public function getOptions(): array
    {
        throw new NotImplementedException();
    }

    /**
     * @return string[]
     */
    public function getHeadersToSign(): array
    {
        return ['Digest', 'X-Request-ID', 'MessageCreateDateTime'];
    }

    /**
     * @return string
     */
    protected function getBody(): string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getDigest(): string
    {
        return Signer::getDigest($this->getBody());
    }
}
