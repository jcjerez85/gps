<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Mail subject
     *
     * @var string
     */
    public $mailSubject;

    /**
     * Mail body
     *
     * @var string
     */
    public $body;

    /**
     * Mail attaches
     *
     * @var array
     */
    public $attaches;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $body, array $attaches = [])
    {
        $this->mailSubject = $subject;
        $this->body = $body;
        $this->attaches = $attaches;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this
            ->subject($this->mailSubject)
            ->view('front::Emails.template');

        if (!empty($this->attaches)) {
            foreach ($this->attaches as $attach) {
                $message->attach($attach);
            }
        }

        return $message;
    }
}
