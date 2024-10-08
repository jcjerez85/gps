<?php

namespace Tobuli\Helpers\Payments\Gateways\Paydunya;

class DirectPay extends Checkout
{
    public function creditAccount($payee_account, $amount)
    {
        $payload = array(
          'account_alias' => $payee_account,
          'amount'        => $amount
        );

        $result = Utilities::httpJsonRequest(Setup::getDirectPayCreditUrl(), $payload);

        if (count($result) > 0) {
            switch ($result['response_code']) {
                case 00:
                    $this->status = Paydunya::SUCCESS;
                    $this->response_text = $result["response_text"];
                    $this->description = $result["description"];
                    $this->transaction_id = $result["transaction_id"];
                    return true;
                break;
                default:
                    $this->status = Paydunya::FAIL;
                    $this->response_text = $result["response_text"];
                    $this->response_code = $result["response_code"];
                    return false;
                break;
            }
        } else {
            $this->status = Paydunya::FAIL;
            $this->response_code = 4002;
            $this->response_text = "An Unknown PAYDUNYA Server Error Occured.";

            return false;
        }
    }
}