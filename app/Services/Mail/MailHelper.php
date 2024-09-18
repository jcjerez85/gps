<?php namespace App\Services\Mail;

use App\Mail\BaseMail;
use CustomFacades\Appearance;
use Illuminate\Support\Facades\Mail;
use Swift_SendmailTransport as MailTransport;
use GuzzleHttp\Exception\ClientException;

class MailHelper
{
    protected $fallback;

    protected function recipients($to)
    {
        if (empty($to))
            return null;

        if ( ! is_array($to))
            $to = explode(';', $to);

        $to = array_map('trim', $to);

        $to = array_filter($to, function($value){
            return ! empty($value);
        });

        return $to;
    }

    public function fallback(bool $value = true)
    {
        $this->fallback = $value;

        return $this;
    }

    public function send($to, $subject, $body, $attaches = [])
    {
        $recipients = $this->recipients($to);

        if (empty($recipients))
            return [
                'status' => false,
                'error'  => 'Empty recipients'
            ];

        $messeage = new BaseMail($subject, $body, $attaches);
        $messeage->to($recipients);

        if ($from = Appearance::getSetting('noreply_email')) {
            $name = Appearance::getSetting('from_name');
            $messeage->from($from, empty($name) ? null : $name);
        }

        try
        {
            Mail::send($messeage);
        }
        catch (ClientException $e) {
            $error = $e->getMessage();

            $response = $e->getResponse();

            if ( $response && $response->getStatusCode() == 422 )
                $this->fallback = FALSE;
        }
        catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!empty($error) && $this->fallback) {
            $backupMailer = Mail::getSwiftMailer();

            Mail::setSwiftMailer( new \Swift_Mailer( new MailTransport()) );

            try
            {
                Mail::send($messeage);
            }
            catch (\Exception $e) {
                $error = $e->getMessage();
            }

            Mail::setSwiftMailer( $backupMailer );
        }

        return [
            'status' => empty($error),
            'error'  => empty($error) ? NULL : $error
        ];
    }
}