<?php

declare(strict_types=1);

namespace App;

use App\Contract\Service\BinProviderInterface;
use App\Contract\Service\ComissionRateProviderInterface;
use App\Contract\Service\EuCountryCheckerInterface;
use App\Contract\Service\ExchangeRateProviderInterface;
use App\Contract\Service\TransactionDataProviderInterface;
use App\Exception\DataProviderException;

class ComissionCalculator
{
    private const BASE_CURRENCY = 'EUR';

    public function __construct(
        private readonly TransactionDataProviderInterface $dataProvider,
        private readonly EuCountryCheckerInterface $euCountryChecker,
        private readonly BinProviderInterface $binProvider,
        private readonly ExchangeRateProviderInterface $exchangeRateProvider,
        private readonly ComissionRateProviderInterface $comissionRateProvider,
    ) {

    }

    public function calculate(): array
    {
        $result = [];
        try {
            foreach ($this->dataProvider->fetchData() as $transaction) {
                $bin = $this->binProvider->getBin($transaction['bin']);

                $isEu = $this->euCountryChecker->isEu($bin['country']['alpha2']);

                $rate = $this->exchangeRateProvider->getExchangeRate($transaction['currency']);

                if ($transaction['currency'] == self::BASE_CURRENCY || $rate == 0) {
                    $amountFixed = $transaction['amount'];
                }

                if ($transaction['currency'] != self::BASE_CURRENCY || $rate > 0) {
                    $amountFixed = $transaction['amount'] / $rate;
                }

                $result[] = $amountFixed * $this->comissionRateProvider->getRate($isEu);
            }
        } catch (DataProviderException $e) {
            throw new \Exception($e->getMessage());
        }

        return $result;
    }
}
