<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Mail;

use App\Models\Email;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller 
{


   public function html_email($email)
   {
        $email = Email::find($email);

        $data = array('name'=>$email->send_to, 'content'=>$email->content);

        Mail::send('mail.html', $data, function($message) use ($email) {
            $message->to($email->send_email, $email->send_to)->subject
                ($email->subject);
            $message->from(env("MAIL_FROM_ADDRESS", "noreply@nextrack.tech"),'Nextrack');
        });

        $email->status = "sent";
        $email->save();

      echo "HTML Email Sent. Check your inbox.";
   }

   
   public function attachment_email($email) 
   {
        $email = Email::find($email);
        $data = array('name'=>$email->send_to, 'content'=>$email->content);

        Mail::send('mail.html', $data, function($message) use ($email) {
            $message->to($email->send_email, $email->send_to)->subject
                ($email->subject);
                
                //TODO - will need an email attachment model, then loop through all the attachments and add them to the email.
                foreach($email->Email_Attachments as $attachment)
                {
                    $message->attach('/var/www/html/nextrack/public/uploads/' . $attachment->filename);
                }
                
                $message->from(env("MAIL_FROM_ADDRESS", "noreply@nextrack.com"),'Nextrack');
        });

      echo "HTML Email Sent. Check your inbox.";
   }
   

}