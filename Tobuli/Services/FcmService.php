<?php

namespace Tobuli\Services;

use FCM;
use Illuminate\Database\Eloquent\Model;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Tobuli\Entities\FcmToken;
use Tobuli\Entities\FcmTokenableInterface;

class FcmService
{
    public function setFcmToken(FcmTokenableInterface $tokenable, string $fcmToken)
    {
        $token = $tokenable->fcmTokens()->firstOrNew(['token' => $fcmToken]);
        $token->save();
    }

    /**
     * @param Model&FcmTokenableInterface $tokenable
     * @param $title
     * @param $body
     * @param array $data
     * @return void
     */
    public function send($tokenable, $title, $body, array $data = [], $type = null)
    {
        if (!$tokenable instanceof FcmTokenableInterface) {
            return;
        }

        $tokens = $tokenable->fcmTokens->pluck('token')->toArray();

        if (!$tokens) {
            return;
        }

        $payload = array_merge($data, ['title' => $title, 'content' => $body]);

        return $this->sendToTokens($tokens, $title, $body, $payload, 0, $type);
    }

    public function sendToTokens($tokens, $title, $body, $payloadData = null, $retries = 0, $type = null)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $option = $optionBuilder->build();

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder
            ->setBody($body)
            // ->setSound(config('fcm.sound'))
            //->setChannelId(config('fcm.channel_id'));
            ->setSound($type)
            ->setChannelId($type);
        $notification = $notificationBuilder->build();

        $dataBuilder = new PayloadDataBuilder();

        if (!is_null($payloadData)) {
            $dataBuilder->addData($payloadData);
        }

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $dataBuilder->build());

        if ($errorToken = $downstreamResponse->tokensWithError()) {
            $errorToken = array_keys($errorToken, 'MismatchSenderId');
            FcmToken::whereIn('token', $errorToken)->delete();
        }

        if ($deleteTokens = $downstreamResponse->tokensToDelete()) {
            FcmToken::whereIn('token', $deleteTokens)->delete();
        }

        if ($downstreamResponse->tokensToModify()) {
            foreach ($downstreamResponse->tokensToModify() as $old_token => $new_token) {
                FcmToken::where('token', $old_token)->update(['token' => $new_token]);
            }
        }

        if ($retryTokens = $downstreamResponse->tokensToRetry() && $retries < 2) {
            if (true === $retryTokens) {
                $retryTokens = $tokens;
            }

            return $this->sendToTokens($retryTokens, $title, $body, $payloadData, ++$retries, $type);
        }

        return $downstreamResponse;
    }
}
