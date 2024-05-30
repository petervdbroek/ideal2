<?php

namespace PetervdBroek\iDEAL2;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use PetervdBroek\iDEAL2\Endpoints\Base;
use PetervdBroek\iDEAL2\Endpoints\Payment;
use PetervdBroek\iDEAL2\Endpoints\PaymentStatus;
use PetervdBroek\iDEAL2\Endpoints\Token;
use PetervdBroek\iDEAL2\Exceptions\ApiException;
use PetervdBroek\iDEAL2\Exceptions\ClientNotSetException;
use PetervdBroek\iDEAL2\Exceptions\EndpointNotSetException;
use PetervdBroek\iDEAL2\Exceptions\InvalidDigestException;
use PetervdBroek\iDEAL2\Exceptions\MerchantIdNotSetException;
use PetervdBroek\iDEAL2\Exceptions\MethodNotSetException;
use PetervdBroek\iDEAL2\Exceptions\NotImplementedException;
use PetervdBroek\iDEAL2\Utils\Signer;

class iDEAL
{
    public const APP = 'IDEAL';

    private string $client;
    private Client $httpClient;
    private string $merchantId;
    private Signer $signer;

    /**
     * @param string $merchantId
     * @param string $client
     * @param string $baseUri
     * @param string $certificateFilePath
     * @param string $privateKeyFilePath
     */
    public function __construct(
        string $merchantId,
        string $client,
        string $baseUri,
        string $certificateFilePath,
        string $privateKeyFilePath,
        string $publicCertificateFilePath,
    ) {
        $this->merchantId = $merchantId;
        $this->client = $client;
        $this->httpClient = new Client(['base_uri' => $baseUri]);
        $this->signer = new Signer($certificateFilePath, $privateKeyFilePath, $publicCertificateFilePath);
    }

    /**
     * @throws MerchantIdNotSetException
     */
    public function getMerchantId(): string
    {
        if ('' === $this->merchantId) {
            throw new MerchantIdNotSetException();
        }

        return $this->merchantId;
    }

    /**
     * @throws ClientNotSetException
     */
    public function getClient(): string
    {
        if ('' === $this->client) {
            throw new ClientNotSetException();
        }

        return $this->client;
    }

    /**
     * @param float $amount
     * @param string $reference
     * @param string $notificationUrl
     * @param string $returnUrl
     * @return Resources\Payment
     * @throws ClientNotSetException
     * @throws EndpointNotSetException
     * @throws GuzzleException
     * @throws MerchantIdNotSetException
     * @throws MethodNotSetException
     * @throws NotImplementedException
     * @throws InvalidDigestException
     * @throws ApiException
     */
    public function createPayment(float $amount, string $reference, string $notificationUrl, string $returnUrl): Resources\Payment
    {
        $payment = new Payment($this);
        $payment->initialize($amount, $reference, $notificationUrl, $returnUrl);
        $responseBody = $this->request($payment);

        return new Resources\Payment(json_decode($responseBody, true));
    }

    /**
     * @param string $paymentId
     * @return Resources\PaymentStatus
     * @throws ClientNotSetException
     * @throws EndpointNotSetException
     * @throws GuzzleException
     * @throws MerchantIdNotSetException
     * @throws MethodNotSetException
     * @throws NotImplementedException
     * @throws InvalidDigestException
     * @throws ApiException
     */
    public function getPaymentStatus(string $paymentId): Resources\PaymentStatus
    {
        $paymentStatus = new PaymentStatus($this);
        $paymentStatus->initialize($paymentId);
        $responseBody = $this->request($paymentStatus);

        return new Resources\PaymentStatus(json_decode($responseBody, true));
    }

    /**
     * @param Base $endpoint
     * @return string
     * @throws ClientNotSetException
     * @throws EndpointNotSetException
     * @throws GuzzleException
     * @throws InvalidDigestException
     * @throws MerchantIdNotSetException
     * @throws MethodNotSetException
     * @throws NotImplementedException
     * @throws ApiException
     */
    private function request(Base $endpoint): string
    {
        $options = $endpoint->getOptions();
        $options['headers']['Signature'] = $this->getSignatureWithRequestTargetAndFilteredHeaders($endpoint);
        $options['headers']['Authorization'] = sprintf("Bearer %s", $this->getToken()->getAccessToken());
        try {
            $response = $this->httpClient->request($endpoint->getMethod(), $endpoint->getEndpoint(), $options);
        } catch (ClientException|ServerException $e) {
            throw new ApiException($e->getResponse()->getBody()->getContents());
        }
        $body = $response->getBody()->getContents();
        $this->signer->verifyResponse($response->getHeaders(), $body);

        return $body;
    }

    /**
     * @param Base $endpoint
     * @return string
     * @throws EndpointNotSetException
     * @throws MethodNotSetException
     * @throws NotImplementedException
     */
    private function getSignatureWithRequestTargetAndFilteredHeaders(Base $endpoint): string
    {
        $headers = $this->filterHeadersToSign($endpoint->getOptions()['headers'], $endpoint->getHeadersToSign());
        $headers['(request-target)'] = sprintf('%s %s', strtolower($endpoint->getMethod()), $endpoint->getEndpoint());

        return $this->signer->getSignature($headers);
    }

    /**
     * @param array $headers
     * @param array $headersToSign
     * @return array
     */
    private function filterHeadersToSign(array $headers, array $headersToSign): array
    {
        return array_filter($headers, function ($k) use ($headersToSign) {
            return in_array($k, $headersToSign);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return Resources\Token
     * @throws ApiException
     * @throws ClientNotSetException
     * @throws EndpointNotSetException
     * @throws GuzzleException
     * @throws MerchantIdNotSetException
     * @throws MethodNotSetException
     */
    private function getToken(): Resources\Token
    {
        $token = new Token($this);
        $options = $token->getOptions();
        $options['headers']['Authorization'] = sprintf("Signature %s", $this->signer->getSignature($options['headers']));
        try {
            $response = $this->httpClient->request($token->getMethod(), $token->getEndpoint(), $options);
        } catch (ClientException|ServerException $e) {
            throw new ApiException($e->getResponse()->getBody()->getContents());
        }

        return new Resources\Token(json_decode($response->getBody()->getContents(), true));
    }
}
