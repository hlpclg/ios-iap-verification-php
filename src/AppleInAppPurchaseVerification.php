<?php
/**
 * Created By: LeeHom
 * File Name: AppleInAppPurchaseVerification.php
 * Created Date: 2018-08-17 17:20
 */

namespace LeeHom;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AppleInAppPurchaseVerification
{
    // App Version
    const APP_VERSION = '1.0.1';

    // SandBox Verify URL
    const SANDBOX_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';

    // Production Verify URL
    const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';

    // the apple Returned the receipt-data
    private $receiptData;

    // if your IAP is not a subscription,let it empty string(like this:''),else use you own password
    private $password = '';

    // use SandBox for verify or not, true:sandbox false:production
    private $sandbox = true;

    // Verify URL,No need to care
    private $requestUrl;

    /**
     * AppleInAppPurchaseVerification constructor.
     *
     * @param string  $receiptData
     * @param string  $password
     * @param boolean $sandbox true:Production false:Sandbox
     */
    public function __construct($receiptData, $password, $sandbox)
    {
        $this->receiptData = $receiptData;
        $this->password    = $password;
        $this->sandbox     = $sandbox;
        if ($this->sandbox === true) {
            $this->requestUrl = $this::SANDBOX_URL;
        } else {
            $this->requestUrl = $this::PRODUCTION_URL;
        }
    }

    /**
     * encode request param
     *
     * @return array
     */
    private function encodeRequest()
    {
        if ($this->password == '') {
            return ['receipt-data' => $this->receiptData];
        } else {
            return ['receipt-data' => $this->receiptData, 'password' => $this->password];
        }
    }

    /**
     * decode response
     *
     * @param string $response
     * @return mixed
     */
    private function decodeResponse($response)
    {
        return json_decode($response);
    }

    /**
     * initiate validation request
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function makeRequest()
    {
        $httpRequest = new Client();
        try {
            $response = $httpRequest->request('POST', $this->requestUrl, [
                'json' => $this->encodeRequest()
            ]);

            return $response->getBody()->getContents();
        } catch (ClientException $e) {
            return $e->getMessage();
        }
    }

    /**
     * verify the apple validation results
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateReceipt()
    {
        $response        = $this->makeRequest();
        $decodedResponse = $this->decodeResponse($response);
        return $decodedResponse;
    }
}
