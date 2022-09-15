<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\BillingController;

class RunBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the background process to generate the billing';

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
        $today = date('d');
        
        if($today == "01")
        {
            $billing = new BillingController();
            $billing->calculate(2);
        }
        else
        {
            echo "Not due to run today" . PHP_EOL;
        }

        return 1; 
    }
}
