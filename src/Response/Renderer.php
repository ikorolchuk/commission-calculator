<?php

declare(strict_types=1);

namespace App\Response;

class Renderer
{
    public function __construct(private readonly DataFormatter $dataFormatter)
    {
    }

    public function render(array $result): void
    {
        foreach ($result as $entry) {
            echo $this->dataFormatter->format($entry);
            echo "\n";
        }
    }
}
