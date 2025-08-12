<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SearchPerformed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $query,
        public string $type,
        public int $resultsCount,
        public int $responseTimeMs,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}
}