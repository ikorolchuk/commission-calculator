<?php

declare(strict_types=1);

namespace App\Contract\Response;

interface DataFormatterInterface
{
    public function format(float $amount): float;
}
