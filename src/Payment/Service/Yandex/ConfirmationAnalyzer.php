<?php

namespace Decadal\FpParser\Payment\Service\Yandex;

/**
 * Class ConfirmationAnalyzer
 * @package Decadal\FpParser\Payment\Service\Yandex
 */
class ConfirmationAnalyzer
{
    /**
     * @param string $text
     * @return string|null
     */
    public function extractPurse(string $text) : ?string
    {
        //\b - word boundary
        //\d - digit
        //[\s-]* - dividers between digits segments: 1111-1111-1111-111 or 1111 1111 1111 111. Any count of dividers
        // {11,16} count of numbers' sequence to be parsed as purse
        //todo make config of regexp rules
        $purseRegexp = '/\b(\d[\s-]*){11,16}\b/';
        preg_match($purseRegexp, $text, $matches);
        return $matches[0] ?? null;
    }

    /**
     * @param string $rawPurse
     * @return string
     */
    public function sanitizePurse(string $rawPurse)
    {
        //todo make regexp?
        return str_replace([" ","\r\n", "\r", "\n", "\t", "-"], "", $rawPurse);
    }

    /**
     * @param string $text
     * @return string|null
     */
    public function extractAmount(string $text) : ?string
    {
        // each regexp is COMPLETELY FULL description of amount annotation with currency
        // you shouldn't add regexp that can accept just numbers (39495) or currencies that can be cause of mess for parser.
        // good one: 20 руб 15 коп
        // bad one: 20,15
        $amountVariationsRegexp = [
            //mixed notation with currency on tail. Cents are required part
            '/[+\-]?[0-9][0-9 ]{1,12}[\.,][0-9]{2}[\s]*(?:[$₽]|р|р\.)/',
            //mixed notation with currency on head. Cents are required part
            '/(?:[$₽]|р|р\.)[:\-\s]*[\s]*[0-9][0-9 ]{1,12}[\.,][0-9]{2}/',
            //mixed notation with currency on head. Cents aren't required part
            '/(?:[$₽]|р|р\.)[:\-\s]*[\s]*[0-9][0-9 ]{1,12}/',
        ];
        for ($i = 0, $sz = count($amountVariationsRegexp); $i < $sz; $i++) {
            $regexp = $amountVariationsRegexp[$i];
            preg_match($regexp, $text, $matches);
            if(!empty($matches)) {
                //any matching is allowed for amount extraction
                return $matches[0] ?? null;
            }
        }
        return null;
    }

    /**
     * @param string $rawAmount
     * @return int
     */
    public function sanitizeAmount(string $rawAmount) : int
    {
        $rawAmount = preg_replace('[$₽р\s]', '', $rawAmount);
        $rawAmount = str_replace(",", ".", $rawAmount);
        $rawAmount = (float) $rawAmount;
        // convert to cents
        return (int) ($rawAmount * 100);
    }

    /**
     * @param string $text
     * @return string|null
     */
    public function extractCurrency(string $text) : ?string
    {
        //todo make config of regexp rules
        $purseRegexp = '/[$₽р]/';
        preg_match($purseRegexp, $text, $matches);
        return $matches[0] ?? null;
    }

    /**
     * @param string $text
     * @return string|null
     */
    public function extractConfirmationCode(string $text) : ?string
    {
        // it is a small piece of text (suppose that code via sms not a big and complicated)
        // with only a latin symbols
        $purseRegexp = '/(\b|^)([0-9\w]){2,8}([\s\.]|$)/';
        preg_match($purseRegexp, $text, $matches);
        return $matches[0] ?? null;
    }

    /**
     * @param string $rawCode
     * @return string
     */
    public function sanitizeConfirmationCode(string $rawCode)
    {
        $rawCode = preg_replace('/\W/', '', $rawCode);
        return $rawCode;
    }
}
