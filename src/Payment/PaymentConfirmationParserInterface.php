<?php

namespace Decadal\FpParser\Payment;

use Decadal\FpParser\Payment\Model\PaymentConfirmationModel;

interface PaymentConfirmationParserInterface
{
    /**
     * main goal - extract confirmation code. Ignore all failures according to secondary info
     * @param string $text
     * @return PaymentConfirmationModel
     */
    public function parse(string $text) : PaymentConfirmationModel;

    /**
     * ensure that all parts of confirmation model will be parsed completely
     * @param string $text
     * @return PaymentConfirmationModel
     */
    public function parseStrict(string $text) : PaymentConfirmationModel;
}
