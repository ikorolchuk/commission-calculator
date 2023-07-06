<?php

use App\ComissionCalculator;
use App\Response\DataFormatter;
use App\Response\Renderer;
use App\Service\BinProvider;
use App\Service\ComissionRateProvider;
use App\Service\EuCountryChecker;
use App\Service\ExchangeRateProvider;
use App\Service\SourceFileNameProvider;
use App\Service\TransactionDataProvider;
use Symfony\Component\Dotenv\Dotenv;

require_once './vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

try {
    $sourceFile = (new SourceFileNameProvider($argv))->getFileName();
    $dataProvider = new TransactionDataProvider($sourceFile);

    $binProvider = new BinProvider($_ENV['BIN_LIST_API_HOST']);
    $exchangeReateProvider = new ExchangeRateProvider($_ENV['EXCHANGE_RATE_API_HOST'], $_ENV['EXCHANGE_RATE_API_KEY']);
    $comissionRateProvider = new ComissionRateProvider();

    $comissionCalculator = new ComissionCalculator(
        $dataProvider, 
        new EuCountryChecker(), 
        $binProvider, 
        $exchangeReateProvider,
        $comissionRateProvider
    );
    $result = $comissionCalculator->calculate();
    (new Renderer(new DataFormatter))->render($result);
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}
