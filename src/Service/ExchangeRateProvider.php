<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\Service\ExchangeRateProviderInterface;
use App\Exception\DataProviderException;

class ExchangeRateProvider implements ExchangeRateProviderInterface
{
    public function __construct(
        private readonly string $exchangeRateHostName,
        private readonly string $accessKey = ''
    ) {
    }

    public function getExchangeRate(string $currency): float
    {
        $data = file_get_contents(sprintf("%s?access_key=%s", $this->exchangeRateHostName, $this->accessKey));

        if (!$data) {
            throw new DataProviderException("Unable to load exchange rates!");
        }

        try {  
            $exchangeRate = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }  catch (\JsonException $exception) {  
            throw new DataProviderException($exception->getMessage()); 
        }

        if (!$exchangeRate['success']) {
            throw new DataProviderException(sprintf("Unable to load exchange rates: %s", $exchangeRate['error']['info']));
        }

        return $exchangeRate['rates'][$currency] ?? throw new DataProviderException(sprintf("No exchange rate found for currency: %s", $currency));
    }
}
