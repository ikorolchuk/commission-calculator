<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\Service\BinProviderInterface;
use App\Exception\DataProviderException;

class BinProvider implements BinProviderInterface
{
    public function __construct(private readonly string $binListApiHost)
    {
    }

    public function getBin(string $bin): array
    {
        $binResult = file_get_contents(sprintf("%s%s", $this->binListApiHost, $bin));
        return $binResult 
            ? json_decode($binResult, true) 
            : throw new DataProviderException(sprintf("Information for BIN %s was not found!", $bin));
    }
}
