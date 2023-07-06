<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\Service\EuCountryCheckerInterface;

class EuCountryChecker implements EuCountryCheckerInterface
{
    public function isEu(string $country): bool
    {
        return match ($country) {
            'AT',
            'BE',
            'BG',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GR',
            'HR',
            'HU',
            'IE',
            'IT',
            'LT',
            'LU',
            'LV',
            'MT',
            'NL',
            'PO',
            'PT',
            'RO',
            'SE',
            'SI',
            'SK' => true,
            default => false
        };
    }
}
