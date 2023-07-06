<?php

declare(strict_types=1);

namespace App\Contract\Service;

interface ComissionRateProviderInterface
{
    public function getRate(bool $isEu): float;
}
