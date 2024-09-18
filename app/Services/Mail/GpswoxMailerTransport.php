<?php

namespace App\Services\Mail;

use Curl;
use Illuminate\Mail\Transport\Transport;
use Swift_Attachment;
use Swift_Image;
use Swift_Mime_SimpleMessage;
use Swift_MimePart;

class GpswoxMailerTransport extends Transport
{
    // const BASE_URL = 'http://mailsender.gpswox.com';
    const BASE_URL = 'http://mailsender.com';

    private $client;
    private $multipartBoundary;

    public function __construct(Curl $client, string $apiKey)
    {
        $this->client = $client;
        $this->client->options['CURLOPT_RETURNTRANSFER'] = true;
        $this->client->headers['Authorization'] = $apiKey;

        $this->multipartBoundary = '-------------' . uniqid();
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $data = $this->getData($message);

        $attachments = $this->getAttachments($message);

        if (count($attachments) > 0) {
            $data['attachments'] = $attachments;
        }

        // this nonsense is done because obsolete Swift messages do not allow to get full path of files,
        // which makes impossible to attach the files normally
        $data = $this->generateMultipartContents($data, $this->multipartBoundary);

        $response = $this->post($data);

        if (is_callable('sendPerformed')) {
            $this->sendPerformed($message);
        }

        if (is_callable('numberOfRecipients')) {
            return $this->numberOfRecipients($message);
        }

        return $response;
    }

    private function generateMultipartContents(array $data, string $boundary, string $prefix = ''): string
    {
        $contents = '';

        foreach ($data as $key => $datum) {
            $name = $prefix ? $prefix . '[' . $key . ']' : $key;

            if (isset($datum['__file'])) {
                $contents .= "--" . $boundary . "\r\n";
                // "filename" attribute is not essential; server-side scripts may use it
                $contents .= 'Content-Disposition: form-data; name="' . $name . '";' .
                    ' filename="' . $datum['filename'] . '"' . "\r\n";
                // this is, again, informative only; good practice to include though
                if (isset($datum['type'])) {
                    $contents .= 'Content-Type: ' . $datum['type'] . "\r\n";
                }
                // this end-line must be here to indicate end of headers
                $contents .= "\r\n";
                // the file itself (note: there's no encoding of any kind)
                $contents .= $datum['contents'] . "\r\n";
            } elseif (is_array($datum)) {
                $contents .= $this->generateMultipartContents($datum, $boundary, $name);
            } else {
                $contents .= "--" . $boundary . "\r\n";
                $contents .= 'Content-Disposition: form-data; name="' . $name . '"';
                $contents .= "\r\n\r\n";
                $contents .= "$datum\r\n";
            }
        }

        if (!$prefix) {
            $contents .= "--" . $boundary . "--\r\n";
        }

        return $contents;
    }

    private function getData(Swift_Mime_SimpleMessage $message): array
    {
        $data = [
            'subject' => $message->getSubject(),
            'body' => $this->getContents($message),
        ];

        if ($from = $this->getFrom($message)) {
            $data['from'] = $from;
        }

        if ($replyTo = $this->getReplyTo($message)) {
            $data['reply_to'] = $replyTo;
        }

        $recipients = $this->getRecipients($message);

        $data['to'] = $recipients['to'];

        if (isset($recipients['cc'])) {
            $data['cc'] = $recipients['cc'];
        }

        if (isset($recipients['bcc'])) {
            $data['bcc'] = $recipients['bcc'];
        }

        return $data;
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getRecipients(Swift_Mime_SimpleMessage $message): array
    {
        $setter = function (array $addresses) {
            $recipients = [];

            foreach ($addresses as $email => $name) {
                $recipients[] = $email;
            }

            return $recipients;
        };

        $recipients = [];
        $recipients['to'] = $setter($message->getTo());

        if ($cc = $message->getCc()) {
            $recipients['cc'] = $setter($cc);
        }

        if ($bcc = $message->getBcc()) {
            $recipients['bcc'] = $setter($bcc);
        }

        return $recipients;
    }

    private function getFrom(Swift_Mime_SimpleMessage $message)
    {
        $from = $message->getFrom();

        if (is_array($from)) {
            foreach ($message->getFrom() as $email => $name) {
                return ['address' => $email, 'name' => $name];
            }
        }

        return null;
    }

    private function getReplyTo(Swift_Mime_SimpleMessage $message)
    {
        $replyTo = $message->getReplyTo();

        if (is_array($replyTo)) {
            foreach ($replyTo as $email => $name) {
                return ['address' => $email, 'name' => $name];
            }
        }

        if (is_string($replyTo)) {
            return ['address' => $replyTo];
        }

        return null;
    }

    private function getContents(Swift_Mime_SimpleMessage $message): string
    {
        foreach ($message->getChildren() as $attachment) {
            if ($attachment instanceof Swift_MimePart) {
                return $attachment->getBody();
            }
        }

        if (empty($content) || strpos($message->getContentType(), 'multipart') !== false) {
            return $message->getBody();
        }

        return '';
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function getAttachments(Swift_Mime_SimpleMessage $message): array
    {
        $attachments = [];

        foreach ($message->getChildren() as $attachment) {
            if ($attachment instanceof Swift_Attachment || $attachment instanceof Swift_Image) {
                $attachments[] = [
                    '__file'    => true,
                    'filename'  => $attachment->getFilename(),
                    'type'      => $attachment->getBodyContentType(),
                    'contents'  => $attachment->getBody(),
                ];
            }
        }

        return $attachments;
    }

    private function post($payload)
    {
        $this->client->headers['Content-Type'] = 'multipart/form-data; boundary=' . $this->multipartBoundary;
        $this->client->headers['Content-Length'] = strlen($payload);

        $response = $this->client->post(self::BASE_URL . '/api/send-email', $payload, 'multipart/form-data');

        $statusCode = $response->headers['Status-Code'];
        $message = $response->body;

        if ($statusCode === '200') {
            return $message;
        }

        if ($statusCode === '422') {
            $message = $this->format422Response($message);
        }

        throw new \RuntimeException($message);
    }

    private function format422Response(string $json): string
    {
        $data = json_decode($json, true);

        if (!$data || empty($data['data'])) {
            return $json;
        }

        $message = '';

        foreach ($data['data'] as $field) {
            $message .= implode('', $field);
        }

        return $message;
    }
}