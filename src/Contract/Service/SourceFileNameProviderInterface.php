<?php

declare(strict_types=1);

namespace App\Contract\Service;

interface SourceFileNameProviderInterface
{
    public function getFileName(): string;
}
