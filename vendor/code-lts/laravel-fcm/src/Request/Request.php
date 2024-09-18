<?php

namespace LaravelFCM\Request;

use LaravelFCM\Message\Topics;
use LaravelFCM\Message\Options;
use LaravelFCM\Message\PayloadData;
use LaravelFCM\Message\PayloadNotification;

class Request extends BaseRequest
{
    /**
     * @internal
     *
     * @var string|array
     */
    protected $to;

    /**
     * @internal
     *
     * @var Options|null
     */
    protected $options;

    /**
     * @internal
     *
     * @var PayloadNotification|null
     */
    protected $notification;

    /**
     * @internal
     *
     * @var PayloadData|null
     */
    protected $data;

    /**
     * @internal
     *
     * @var Topics|null
     */
    protected $topic;

    /**
     * Request constructor.
     *
     * @param string|array             $to
     * @param Options|null             $options
     * @param PayloadNotification|null $notification
     * @param PayloadData|null         $data
     * @param Topics|null              $topic
     * @param string|null              $serverKey (optional) The server key
     * @param string|null              $senderId  (optional) The sender Id
     */
    public function __construct($to, Options $options = null, PayloadNotification $notification = null, PayloadData $data = null, Topics $topic = null, string $serverKey = null, string $senderId = null)
    {
        parent::__construct($serverKey, $senderId);

        $this->to = $to;
        $this->options = $options;
        $this->notification = $notification;
        $this->data = $data;
        $this->topic = $topic;
    }

    /**
     * Build the body for the request.
     *
     * @return array
     */
    protected function buildBody()
    {
        $message = [
            'to' => $this->getTo(),
            'registration_ids' => $this->getRegistrationIds(),
            'notification' => $this->getNotification(),
            'data' => $this->getData(),
        ];

        $message = array_merge($message, $this->getOptions());

        // remove null entries
        return array_filter($message);
    }

    /**
     * get to key transformed.
     *
     * @return array|null|string
     */
    protected function getTo()
    {
        $to = is_array($this->to) ? null : $this->to;

        if ($this->topic && $this->topic->hasOnlyOneTopic()) {
            $to = $this->topic->build();
        }

        return $to;
    }

    /**
     * get registrationIds transformed.
     *
     * @return array|null
     */
    protected function getRegistrationIds()
    {
        return is_array($this->to) ? $this->to : null;
    }

    /**
     * get Options transformed.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = $this->options ? $this->options->toArray() : [];

        if ($this->topic && !$this->topic->hasOnlyOneTopic()) {
            // We know it is an array because of the hasOnlyOneTopic check
            $options = array_merge($options, (array) $this->topic->build());
        }

        return $options;
    }

    /**
     * get notification transformed.
     *
     * @return array|null
     */
    protected function getNotification()
    {
        return $this->notification ? $this->notification->toArray() : null;
    }

    /**
     * get data transformed.
     *
     * @return array|null
     */
    protected function getData()
    {
        return $this->data ? $this->data->toArray() : null;
    }
}
