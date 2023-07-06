<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\Service\TransactionDataProviderInterface;
use App\Exception\DataProviderException;

class TransactionDataProvider implements TransactionDataProviderInterface
{
    public function __construct(private readonly string $sourceFilename)
    {
    }
    
    public function fetchData(): \Generator
    {
        $file = fopen($this->sourceFilename, 'r');
        if (!$file) {
            throw new DataProviderException(sprintf("File with the name %s was not found!", $this->sourceFilename));
        }
        while (($line = fgets($file)) !== false) {
            yield json_decode($line, true);
        }
        fclose($file);
    }
}
