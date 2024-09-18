<?php

namespace LaravelFCM\Sender;

use LaravelFCM\Request\TopicRequest;
use Psr\Http\Message\ResponseInterface;

class FCMTopic extends HTTPSender
{

    const CREATE = 'create';
    const SUBSCRIBE = 'subscribe';
    const UNSUBSCRIBE = 'unsubscribe';

    /** @var string */
    private $add_subscription_url = 'https://iid.googleapis.com/iid/v1:batchAdd';
    /** @var string */
    private $remove_subscription_url = 'https://iid.googleapis.com/iid/v1:batchRemove';

    /**
     * Create a topic.
     *
     * @param string $topicId
     * @param string $registrationId
     * @param string|null $serverKey (optional) The server key
     * @param string|null $senderId  (optional) The sender Id
     *
     * @return bool
     */
    public function createTopic($topicId, $registrationId, string $serverKey = null, string $senderId = null)
    {
        $request = new TopicRequest(self::CREATE, $topicId, [], $serverKey, $senderId);
        if (is_array($registrationId)) {
            return null;
        }
        $response = $this->client->request('post', $this->url . $registrationId . '/rel/topics/' . $topicId, $request->build());


        if ($this->isValidResponse($response)) {
            return true;
        }
        return false;
    }

    /**
     * Add subscription to a topic.
     *
     * @param string $topicId
     * @param array|string $recipientsTokens
     * @param string|null $serverKey (optional) The server key
     * @param string|null $senderId  (optional) The sender Id
     * @return bool
     */
    public function subscribeTopic($topicId, $recipientsTokens, string $serverKey = null, string $senderId = null)
    {
        $request = new TopicRequest(self::SUBSCRIBE, $topicId, $recipientsTokens, $serverKey, $senderId);
        $response = $this->client->request('post', $this->add_subscription_url, $request->build());

        if ($this->isValidResponse($response)) {
            return true;
        }
        return false;
    }

    /**
     * Remove subscription from a topic.
     *
     *
     * @param string $topicId
     * @param array|string $recipientsTokens
     * @param string|null $serverKey (optional) The server key
     * @param string|null $senderId  (optional) The sender Id
     * @return bool
     */
    public function unsubscribeTopic($topicId, $recipientsTokens, string $serverKey = null, string $senderId = null)
    {
        $request = new TopicRequest(self::UNSUBSCRIBE, $topicId, $recipientsTokens, $serverKey, $senderId);
        $response = $this->client->request('post', $this->remove_subscription_url, $request->build());

        if ($this->isValidResponse($response)) {
            return true;
        }
        return false;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    public function isValidResponse(ResponseInterface $response)
    {
        return $response->getStatusCode() === 200;
    }
}
