<?php

namespace Decadal\FpParser\Payment\Service\Yandex;


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
     * @param string $text
     * @return string|null
     */
    public function extractAmount(string $text) : ?string
    {
        // each regexp is COMPLETELY FULL description of amount annotation
        // you shouldn't add regexp that can accept just numbers (39495) or currencies that can be cause of mess for parser.
        // good one: 20 руб 15 коп
        // bad one: 20,15
        $amountVariationsRegexp = [
            '/\b(\d[\s-]*){11,16}\b/',
            '/\b(\d[\s-]*){11,16}\b/',
            '/\b(\d[\s-]*){11,16}\b/',
            '/\b(\d[\s-]*){11,16}\b/',
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

    public function extractCurrency(string $text) : ?string
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


    public function extractConfirmationCode(string $text) : ?string
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
}
