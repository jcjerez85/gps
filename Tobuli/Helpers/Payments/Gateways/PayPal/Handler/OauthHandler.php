<?php
/**
 * API handler for OAuth Token Request REST API calls
 */

namespace Tobuli\Helpers\Payments\Gateways\PayPal\Handler;

use Tobuli\Helpers\Payments\Gateways\PayPal\Common\PayPalUserAgent;
use Tobuli\Helpers\Payments\Gateways\PayPal\Core\PayPalConstants;
use Tobuli\Helpers\Payments\Gateways\PayPal\Core\PayPalHttpConfig;
use Tobuli\Helpers\Payments\Gateways\PayPal\Exception\PayPalConfigurationException;
use Tobuli\Helpers\Payments\Gateways\PayPal\Exception\PayPalInvalidCredentialException;
use Tobuli\Helpers\Payments\Gateways\PayPal\Exception\PayPalMissingCredentialException;

/**
 * Class OauthHandler
 */
class OauthHandler implements IPayPalHandler
{
    /**
     * Private Variable
     *
     * @var \Tobuli\Helpers\Payments\Gateways\PayPal\Rest\ApiContext $apiContext
     */
    private $apiContext;

    /**
     * Construct
     *
     * @param \Tobuli\Helpers\Payments\Gateways\PayPal\Rest\ApiContext $apiContext
     */
    public function __construct($apiContext)
    {
        $this->apiContext = $apiContext;
    }

    /**
     * @param PayPalHttpConfig $httpConfig
     * @param string                    $request
     * @param mixed                     $options
     * @return mixed|void
     * @throws PayPalConfigurationException
     * @throws PayPalInvalidCredentialException
     * @throws PayPalMissingCredentialException
     */
    public function handle($httpConfig, $request, $options)
    {
        $config = $this->apiContext->getConfig();

        $httpConfig->setUrl(
            rtrim(trim($this->_getEndpoint($config)), '/') .
            (isset($options['path']) ? $options['path'] : '')
        );

        $headers = array(
            "User-Agent"    => PayPalUserAgent::getValue(PayPalConstants::SDK_NAME, PayPalConstants::SDK_VERSION),
            "Authorization" => "Basic " . base64_encode($options['clientId'] . ":" . $options['clientSecret']),
            "Accept"        => "*/*"
        );
        $httpConfig->setHeaders($headers);

        // Add any additional Headers that they may have provided
        $headers = $this->apiContext->getRequestHeaders();
        foreach ($headers as $key => $value) {
            $httpConfig->addHeader($key, $value);
        }
    }

    /**
     * Get HttpConfiguration object for OAuth API
     *
     * @param array $config
     *
     * @return PayPalHttpConfig
     * @throws \Tobuli\Helpers\Payments\Gateways\PayPal\Exception\PayPalConfigurationException
     */
    private static function _getEndpoint($config)
    {
        if (isset($config['oauth.EndPoint'])) {
            $baseEndpoint = $config['oauth.EndPoint'];
        } elseif (isset($config['service.EndPoint'])) {
            $baseEndpoint = $config['service.EndPoint'];
        } elseif (isset($config['mode'])) {
            switch (strtoupper($config['mode'])) {
                case 'SANDBOX':
                    $baseEndpoint = PayPalConstants::REST_SANDBOX_ENDPOINT;
                    break;
                case 'LIVE':
                    $baseEndpoint = PayPalConstants::REST_LIVE_ENDPOINT;
                    break;
                default:
                    throw new PayPalConfigurationException('The mode config parameter must be set to either sandbox/live');
            }
        } else {
            // Defaulting to Sandbox
            $baseEndpoint = PayPalConstants::REST_SANDBOX_ENDPOINT;
        }

        $baseEndpoint = rtrim(trim($baseEndpoint), '/') . "/v1/oauth2/token";

        return $baseEndpoint;
    }
}
