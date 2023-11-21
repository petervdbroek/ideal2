<?php

namespace PetervdBroek\iDEAL2\Endpoints;

class PaymentStatus extends Base
{
    protected string $endpoint = '/xs2a/routingservice/services/ob/pis/v3/payments/{paymentId}/status';
    protected string $method = 'GET';

    /**
     * @param string $paymentId
     * @return void
     */
    public function initialize(string $paymentId): void
    {
        $this->endpoint = str_replace('{paymentId}', $paymentId, $this->endpoint);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'headers' => $this->getHeaders(),
        ];
    }

    /**
     * @return array
     */
    private function getHeaders(): array
    {
        return [
            'Digest' => $this->getDigest(),
            'X-Request-ID' => $this->requestId,
            'MessageCreateDateTime' => date('c'),
        ];
    }
}
