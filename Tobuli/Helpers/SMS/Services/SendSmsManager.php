<?php namespace Tobuli\Helpers\SMS\Services;

abstract class SendSmsManager
{
    abstract protected function sendSingle($receiver_phone, $message_body);

    /**
     * @param $receiver_phone
     * @param $message_body
     */
    public function send($receiver_phone, $message_body)
    {
        $receiver_phone = $this->checkForMultipleNumbers($receiver_phone);
        $message_body = $this->cleanMessageBody($message_body);

        if (is_array($receiver_phone))
            $this->sendMultiple($receiver_phone, $message_body);
        else
            $this->sendSingle($receiver_phone, $message_body);
    }

    /**
     * @param $receiver_phones
     * @param $message_body
     */
    protected function sendMultiple($receiver_phones, $message_body)
    {
        foreach ($receiver_phones as $receiver_phone)
            $this->sendSingle($receiver_phone, $message_body);
    }

    /**
     * @param $numbers
     * @return array
     */
    private function checkForMultipleNumbers($numbers)
    {
        if (is_array($numbers))
            $numbers_array = $numbers;
        else
            $numbers_array = $this->splitByColon($numbers);

        if (count($numbers_array) == 1)
            return $numbers;

        return $numbers_array;
    }

    /**
     * @param $numbers
     * @return array
     */
    private function splitByColon($numbers)
    {
        return array_filter(array_map('trim', explode(';', $numbers)));
    }

    /**
     * @param $body
     * @return string
     */
    private function cleanMessageBody($body)
    {
        $body = html_entity_decode($body);

        return strtr($body, [
            '<br>'  => "\n",
            '\r\n'  => "\n",
            '&deg;' => '',
        ]);
    }
}