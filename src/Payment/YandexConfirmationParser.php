<?php


namespace Decadal\FpParser\Payment;

use Decadal\FpParser\Payment\Model\PaymentConfirmationModel;
use Decadal\FpParser\Payment\Service\Yandex\ConfirmationAnalyzer;
use Decadal\FpParser\Payment\Service\Yandex\PurseValidator;

class YandexConfirmationParser implements PaymentConfirmationParserInterface
{
    /**
     * @var PurseValidator
     */
    private $yandexPurseValidator;

    /**
     * @var ConfirmationAnalyzer
     */
    private $analyzer;

    /**
     * YandexConfirmationParser constructor.
     * @param PurseValidator $validator
     * @param ConfirmationAnalyzer $analyzer
     */
    public function __construct(PurseValidator $validator, ConfirmationAnalyzer $analyzer)
    {
        $this->yandexPurseValidator = $validator;
        $this->analyzer = $analyzer;
    }

    /**
     * @param string $text
     * @return PaymentConfirmationModel
     * @throws \Exception
     */
    public function parse(string $text): PaymentConfirmationModel
    {
        $model = new PaymentConfirmationModel();
        $rawPurse = $this->analyzer->extractPurse($text);
        if($rawPurse) {
            $purse = $this->extractValidPurse($rawPurse);
            if($purse) {
                $model->setRecipient($purse);
                //yandex purse is the most composite an can be cause to incorrect amount or confirmation code extraction
                $text = preg_replace($rawPurse, '', $text);
            }
        }
        //amount is more determinate and specified part of the confirmation than code (rare case but it can be like sdkk$i12$)
        $rawAmount = $this->analyzer->extractAmount($text);
        if($rawAmount) {
            $amount = $this->sanitizeAmount($rawAmount);
            $model->setAmount($amount);
            $text = preg_replace($rawAmount, '', $text);
        }
        $rawCode = $this->analyzer->extractConfirmationCode($text);
        if(!$rawCode) {
            throw new \Exception("Couldn't find acceptance code");
        }
        $code = $this->sanitizeCode($rawCode);
        $model->setConfirmationCode($code);
        return $model;
    }

    /**
     * @param string $text
     * @return PaymentConfirmationModel
     * @throws \Exception
     */
    public function parseStrict(string $text): PaymentConfirmationModel
    {
        $model = new PaymentConfirmationModel();
        $rawPurse = $this->analyzer->extractPurse($text);
        if(!$rawPurse) {
            throw new \Exception("Couldn't find recipient in the text");
        }
        $purse = $this->extractValidPurse($rawPurse);
        if(!$purse) {
            throw new \Exception(sprintf("Couldn't validate recipient: %s", $rawPurse));
        }
        $model->setRecipient($purse);
        //yandex purse is the most composite an can be cause to incorrect amount or confirmation code extraction
        $text = preg_replace($rawPurse, '', $text);

        //amount is more determinate and specified part of the confirmation than code (rare case but it can be like sdkk$i12$)
        $rawAmount = $this->analyzer->extractAmount($text);
        if(!$rawAmount) {
            throw new \Exception("Couldn't find amount in the text");
        }
        $rawCurrency = $this->analyzer->extractCurrency($rawAmount);
        if(!$rawCurrency) {
            throw new \Exception("Couldn't find currency in the text: no possibility to find difference between amount and code");
        }
        $amount = $this->sanitizeAmount($rawAmount);
        if(!$amount) {
            throw new \Exception("Couldn't sanitize amount");
        }
        $model->setAmount($amount);
        $text = preg_replace($rawAmount, '', $text);
        $rawCode = $this->analyzer->extractConfirmationCode($text);
        if(!$rawCode) {
            throw new \Exception("Couldn't find acceptance code");
        }
        $code = $this->sanitizeCode($rawCode);
        if(!$code) {
            throw new \Exception("Couldn't sanitize code");
        }
        $model->setConfirmationCode($code);
        return $model;
    }


    private function sanitizeAmount(string $amount) : ?int
    {

    }

    private function sanitizeCode(string $code) : ?string
    {

    }

    private function sanitizePurse(string $purse) : string
    {

    }

    /**
     * @param string $rawPurse
     * @return string|null
     */
    private function extractValidPurse(string $rawPurse) : ? string
    {
        $purse = $this->sanitizePurse($rawPurse);
        if(!$this->yandexPurseValidator->validate($purse)) {
            return null;
        }
        return $purse;
    }

}
