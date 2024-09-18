<?php

namespace App\Jobs;

use CustomFacades\MailHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $email;
    protected $subject;
    protected $body;

    public function __construct(string $email, string $subject, string $body)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle()
    {
        MailHelper::fallback()->send($this->email, $this->subject, $this->body);
    }
}
