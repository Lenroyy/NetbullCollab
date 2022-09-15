<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\ThingsboardController;

class SensorChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all the histories that need to have sensor data retrieved';

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
        $thingsboard = new ThingsboardController();
        $thingsboard->checkReadings();

        return 1; 
    }
}
