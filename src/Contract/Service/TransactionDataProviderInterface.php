<?php

declare(strict_types=1);

namespace App\Contract\Service;

interface TransactionDataProviderInterface
{
    public function fetchData(): \Generator;
}
