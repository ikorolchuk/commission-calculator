<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\Service\ComissionRateProviderInterface;

class ComissionRateProvider implements ComissionRateProviderInterface
{
    private const EU_COMMISSION_RATE = 0.01;
    private const NON_EU_COMMISSION_RATE = 0.02;

    public function getRate(bool $isEu): float
    {
        return $isEu ? self::EU_COMMISSION_RATE : self::NON_EU_COMMISSION_RATE;
    }
}
