<?php

namespace App\Jobs;

use Mail;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessMailing implements ShouldQueue
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $listeNotif= Notification::where('status','=',0)->get();

        foreach($listeNotif as $notif)
        {
            $status=1;
            ProcessMailing::sendmail($notif->courrier_code,$notif->email,$notif->content,$notif->object);
        }
    }
    
    public static function sendmail($courrier_code,$email,$text,$sujet="MTFP-SYGEC"){
      Mail::raw($text, function ($message) use ($email,$text,$sujet) {
            $message->from('sygecmtfpbenin@gmail.com', 'MTFP-SYGEC');
            $message->to($email);
            $message->subject($sujet);
        });

      if (!Mail::failures()) {
        Notification::where("courrier_code","=",$courrier_code)->where("email","=",$email)->update(["status" =>1,"comments" =>'SUCCESS','sended_at'=>date("Y-m-d h:m:i")]);
      }
      else
      {
        Notification::where("courrier_code","=",$courrier_code)->where("email","=",$email)->update(["status" =>2,"comments" =>'FAILED','sended_at'=>date("Y-m-d h:m:i")]);
      }
   }
}
