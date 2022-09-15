<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Email;
use App\Models\Site;

use App\Http\Controllers\MailController;

class emailSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public $tries = 3;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = new MailController();

        $emails = Email::where('status', '=', 'pending')->get();
        foreach($emails as $email)
        {
            $mail->html_email($email->id);
        }
    }
}