<?php

declare(strict_types=1);

namespace App\Contract\Service;

interface BinProviderInterface
{
    public function getBin(string $bin): array;
}
