<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\Service\SourceFileNameProviderInterface;
use App\Exception\DataProviderException;

class SourceFileNameProvider implements SourceFileNameProviderInterface
{
    public function __construct(private readonly array $cmdArgs)
    {
    }

    public function getFileName(): string
    {
        return $this->cmdArgs[1] ?? throw new DataProviderException("Input file name was not provided!");
    }
}
