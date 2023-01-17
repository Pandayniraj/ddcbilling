<?php

namespace App\Jobs;

use App\Models\Contact;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendBulkEmailContact;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendBulkEmailToContact extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    // public $timeout = 300; // default is 60sec.
    public $timeout = 7200; // 2 hours 

    protected $lead;
    protected $request;
    protected $mail_to;
    protected $fields;
    protected $details;
    protected $contacts;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details,$contacts)
    {
        // $this->lead = $lead;
        // $this->request = $request;
        // $this->mail_to = $mail_to;
        // $this->fields = $fields;
         $this->details = $details;
         $this->contacts = $contacts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {

        // $contact = $this->contact;
        // $request = $this->request;
        // $mail_to = $this->mail_to;
        // $fields = $this->fields;
        // $mail_from = env('APP_EMAIL');
        // try {
        //     $mailer->send('emails.send-bulk-email-contact', ['contact' => $contact, 'request' => $request], function ($message) use ($request, $mail_to, $fields, $contact,$mail_from) {
        //         $message->subject($request['subject']);
        //         $message->from($mail_from, env('APP_COMPANY'));
        //         $message->to($mail_to, $contact->full_name);
        //         if ($fields != '') {
        //             $message->attach('sent_attachments/'.$fields['file']);
        //         }
        //     });
        // } catch (\Exception $e) {
        // }

            // $contacts = \App\Models\Contact::where('email_1', 'like', '%_@__%.__%')->get();

         // if ($request['contact_id'] != '') {
         //        $contacts = \App\Models\Contact::where('email_1', 'like', '%_@__%.__%')->where('enabled', '1')->where('id', $request['contact_id'])->get();
         //    } elseif($request['client_id'] != '') {
         //        $contacts = \App\Models\Contact::where('email_1', 'like', '%_@__%.__%')->where('enabled', '1')->where('client_id', $request['client_id'])->get();
         //    }else{
         //        $contacts = \App\Models\Contact::where('email_1', 'like', '%_@__%.__%')->where('enabled', '1')->get();
         //    }
        
            $contacts = $this->contacts;
            foreach ($contacts->chunk(4) as $k => $v) {
                    try{
                    $email = new SendBulkEmailContact($this->details);
                    Mail::to($v[0]->email_1 ?: [])->send($email);
                    Mail::to($v[1]->email_1 ?: [])->send($email);
                    Mail::to($v[2]->email_1 ?: [])->send($email);
                    Mail::to($v[3]->email_1 ?: [])->send($email);
                    } catch(Exception $exception) {
                        // do something with $exception that contains the error message
                    }

            
            }
    }
}