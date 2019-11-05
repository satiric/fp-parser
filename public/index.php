<?php

use Decadal\FpParser\Payment\Service\Yandex\ConfirmationAnalyzer;
use Decadal\FpParser\Payment\Service\Yandex\PurseValidator;
use Decadal\FpParser\Payment\YandexConfirmationParser;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'on');


// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';


$sms = <<<HEREDOC
Пароль: 9059
Спишется 44,53р.
Перевод на счет 410011615826967
HEREDOC;

$validator = new PurseValidator();
$analyzer = new ConfirmationAnalyzer();
$parser = new YandexConfirmationParser($validator, $analyzer);

$model = $parser->parseStrict($sms);
echo PHP_EOL."to: ".$model->getRecipient().PHP_EOL;
echo PHP_EOL."amount: ".$model->getAmount().PHP_EOL;
echo PHP_EOL."code: ".$model->getConfirmationCode().PHP_EOL;
