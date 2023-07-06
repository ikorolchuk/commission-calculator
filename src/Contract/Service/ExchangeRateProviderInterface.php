<?php

declare(strict_types=1);

namespace App\Contract\Service;

interface ExchangeRateProviderInterface
{
    public function getExchangeRate(string $currency): float;
}
