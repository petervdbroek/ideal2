<?php

namespace PetervdBroek\iDEAL2\Utils;

use PetervdBroek\iDEAL2\Exceptions\InvalidDigestException;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;

class Signer
{
    private OpenSSLCertificate $certificate;
    private OpenSSLAsymmetricKey $privateKey;

    /**
     * @param $certificateFilePath
     * @param $privateKeyFilePath
     */
    public function __construct($certificateFilePath, $privateKeyFilePath)
    {
        $this->certificate = openssl_x509_read(file_get_contents($certificateFilePath));
        $this->privateKey = openssl_get_privatekey(file_get_contents($privateKeyFilePath));
    }

    /**
     * @param string $body
     * @return string
     */
    public static function getDigest(string $body): string
    {
        return "SHA-256=" . base64_encode(hash('sha256', $body, true));
    }

    /**
     * @param array $headers
     * @return string
     */
    public function getSignature(array $headers): string
    {
        $signString = $this->getSignString($headers);
        $headersToSign = strtolower(implode(' ', array_keys($headers)));

        return sprintf(
            'keyId="%s", algorithm="SHA256withRSA", headers="%s", signature="%s"',
            $this->getFingerprint(),
            $headersToSign,
            $this->getSignedString($signString)
        );
    }

    /**
     * @param array $headers
     * @param string $body
     * @throws InvalidDigestException
     */
    public function verifyResponse(array $headers, string $body): void
    {
        $this->verifyDigest($headers, $body);
        $this->verifySignature($headers);
    }

    /**
     * @param array $headers
     * @return string
     */
    private function getSignString(array $headers): string
    {
        $signString = "";
        foreach ($headers as $key => $header) {
            $signString .= sprintf("%s: %s\n", strtolower($key), trim($header));
        }

        return trim($signString);
    }

    /**
     * @return string
     */
    private function getFingerprint(): string
    {
        return openssl_x509_fingerprint($this->certificate);
    }

    /**
     * @param $signString
     * @return string
     */
    private function getSignedString($signString): string
    {
        $binary = "";
        openssl_sign($signString, $binary, $this->privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($binary);
    }

    /**
     * @param array $headers
     * @param string $body
     * @return void
     * @throws InvalidDigestException
     */
    private function verifyDigest(array $headers, string $body): void
    {
        if ($headers['Digest'][0] !== self::getDigest($body)) {
            throw new InvalidDigestException();
        }
    }

    /**
     * @param array $headers
     * @return void
     */
    private function verifySignature(array $headers): void
    {
        // TODO implement when public certificate is available
    }
}
