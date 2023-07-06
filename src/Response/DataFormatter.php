<?php

declare(strict_types=1);

namespace App\Response;

use App\Contract\Response\DataFormatterInterface;

class DataFormatter implements DataFormatterInterface
{
    public function format(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }
}
