<?php

namespace Decadal\FpParser\Payment\Model;

class PaymentConfirmationModel
{
    /**
     * @var int|null
     */
    protected $amount;
    /**
     * @var string|null
     */
    protected $recipient;
    /**
     * @var string|null
     */
    protected $confirmationCode;

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     */
    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    /**
     * @param string|null $recipient
     */
    public function setRecipient(?string $recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string|null
     */
    public function getConfirmationCode(): ?string
    {
        return $this->confirmationCode;
    }

    /**
     * @param string|null $confirmationCode
     */
    public function setConfirmationCode(?string $confirmationCode): void
    {
        $this->confirmationCode = $confirmationCode;
    }

}
