<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Billing
 *
 * @author Istat Toukea
 */
abstract class CinetPayBilling {

    const URI_CINETPAY_PROD = 'api.cinetpay.com/v1/';
    const URI_CINETPAY_DEV = 'api.sandbox.cinetpay.com/v1/';
    const METHOD_CHECK_PAY_STATUS = 'checkPayStatus';

    protected $apikey, $url_api;

    public function CinetPayBilling($apikey, $prod = true) {
        $this->apikey = $apikey;
        $this->url_api = $prod ? self::URI_CINETPAY_PROD : self::URI_CINETPAY_DEV;
    }

//put your code here
    public final function notifyPayment($cpm_trans_id) {
        $http = new SimpleHttpQuery();
        $http->addParam("method", self::METHOD_CHECK_PAY_STATUS);
        $http->addParam("apikey", $this->apikey);
        $http->addParam("cpm_trans_id", $cpm_trans_id);
        $response = $http->doPost($this->url_api);
        $transactionArray = json_decode($response, true);
        $transaction = new CinetPayTransaction($transactionArray);
        if ($transaction->isConfirmed()) {
            return $this->onPaymentConfirmed($transaction);
        } else if ($transaction->isWaiting()) {
            return $this->onPaymentWaiting($transaction);
        } else {
            return $this->onPaymentEchec($transaction);
        }
    }

    public abstract function onPaymentConfirmed($transaction);

    public abstract function onPaymentWaiting($transaction);

    public abstract function onPaymentEchec($transaction);
}
