<?php

namespace Modules\Gym\Console\Commands;

use Illuminate\Console\Command;
use Modules\Gym\Services\GymService;

class AutoCloseGymVisitsCommand extends Command
{
    protected $signature = 'gym:auto-close-visits';

    protected $description = 'Auto close active gym visits older than four hours';

    public function handle(GymService $gymService): int
    {
        $closedVisits = $gymService->autoCloseVisits();

        $this->info("Auto-closed {$closedVisits} gym visit(s).");

        return self::SUCCESS;
    }
}
