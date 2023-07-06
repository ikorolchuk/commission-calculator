<?php

declare(strict_types=1);

namespace App\Contract\Service;

interface EuCountryCheckerInterface
{
    public function isEu(string $country): bool;
}
