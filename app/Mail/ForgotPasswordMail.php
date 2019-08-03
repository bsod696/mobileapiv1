<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $msgData;

    public function __construct($msgData)
    {
        $this->msgData = $msgData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = "admin@google.com";
        $name = "System Admin";
        $subject = "Forgot Password Request";
        return $this->view('email.template_send_forgot_pass')
                ->with($this->msgData)
                ->from($address,$name)
                ->replyTo($address,$name)
                ->subject($subject);
       
    }
}
