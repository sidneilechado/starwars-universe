<?php

namespace App\Jobs;

use App\Contracts\Statistics\StatisticsComputationServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ComputeSearchStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly StatisticsComputationServiceInterface $statisticsComputationService
    ) {}

    public function handle(): void
    {
        $this->statisticsComputationService->computeAndCacheStatistics();
    }
}