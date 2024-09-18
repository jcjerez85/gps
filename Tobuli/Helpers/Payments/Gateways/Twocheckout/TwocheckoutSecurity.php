<?php

namespace Tobuli\Helpers\Payments\Gateways\Twocheckout;

class TwocheckoutSecurity
{
    public function hasValidHash(array $input): bool
    {
        $controlHash = $input['HASH'] ?? '';
        unset($input['HASH']);

        $dataString = '';
        $this->getHashBase($dataString, $input);

        return $controlHash === $this->buildHash($dataString);
    }

    private function getHashBase(string &$data, $input)
    {
        foreach ($input as $value) {
            if (is_array($value) || is_object($value)) {
                $this->getHashBase($data, $value);
            } else {
                $data .= strlen($value) . $value;
            }
        }
    }

    private function buildHash(string $data): string
    {
        $b = 64; // byte length for md5
        $key = TwocheckoutConfig::getSecretKey();

        if (strlen($key) > $b) {
            $key = pack('H*', md5($key));
        }

        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $kIpad = $key ^ $ipad;
        $kOpad = $key ^ $opad;

        return md5($kOpad . pack('H*', md5($kIpad . $data)));
    }
}