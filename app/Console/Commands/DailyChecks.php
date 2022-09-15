<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\ReportController;

class DailyChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all the checks on the system daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $report = new ReportController();
        $report->emailParticipations();

        return 1; 
    }
}
