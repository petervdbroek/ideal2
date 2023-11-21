<?php

namespace PetervdBroek\iDEAL2\Resources;

use DateTime;
use Exception;

class Payment extends Base
{
    public const SUCCESS = 'SettlementCompleted';
    public const CANCELLED = 'Cancelled';
    public const EXPIRED = 'Expired';
    public const OPEN = 'Open';
    public const ERROR = 'Error';

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->response['CommonPaymentData']['PaymentId'];
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->response['Links']['RedirectUrl']['Href'];
    }

    /**
     * @return string
     */
    public function getPaymentStatus(): string
    {
        return $this->response['CommonPaymentData']['PaymentStatus'];
    }

    /**
     * @return string
     */
    public function getDebtorName(): string
    {
        return $this->response['CommonPaymentData']['DebtorInformation']['Name'];
    }

    /**
     * @return string
     */
    public function getDebtorBIC(): string
    {
        return $this->response['CommonPaymentData']['DebtorInformation']['Agent'];
    }

    /**
     * @return string
     */
    public function getDebtorIBAN(): string
    {
        return $this->response['CommonPaymentData']['DebtorInformation']['Account']['Identification'];
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->getPaymentStatus() === self::SUCCESS;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->getPaymentStatus() === self::EXPIRED;
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->getPaymentStatus() === self::CANCELLED;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->getPaymentStatus() === self::OPEN;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->getPaymentStatus() === self::ERROR;
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getExpiryDateTime(): DateTime
    {
        return new DateTime($this->response['CommonPaymentData']['ExpiryDateTimestamp']);
    }
}
