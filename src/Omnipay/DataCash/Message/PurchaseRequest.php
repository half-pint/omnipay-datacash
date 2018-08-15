<?php

namespace Omnipay\DataCash\Message;

use DOMDocument;
use SimpleXMLElement;
use Omnipay\Common\Message\AbstractRequest;

/**
 * DataCash Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://mars.transaction.datacash.com/Transaction';
    protected $testEndpoint = 'https://testserver.datacash.com/Transaction';

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function setVerify($value)
    {
        return $this->setParameter('verify',$value);
    }

    public function getVerify()
    {
        return $this->getParameter('verify');
    }

    public function getMerchantUrl()
    {
        return $this->getParameter('merchant_url');
    }

    public function setMerchantUrl($value)
    {
        return $this->setParameter('merchant_url', $value);
    }

    public function getPurchaseDesc()
    {
        return $this->getParameter('purchase_desc');
    }

    public function setPurchaseDesc($value)
    {
        return $this->setParameter('purchase_desc', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'card', 'transactionId');
        $this->getCard()->validate();

        $data = new SimpleXMLElement('<Request/>');

        $data->Authentication->client = $this->getMerchantId();
        $data->Authentication->password = $this->getPassword();

        $data->Transaction->TxnDetails->amount = $this->getAmount();
        $data->Transaction->TxnDetails->amount->addAttribute('currency', $this->getCurrency());
        $data->Transaction->TxnDetails->merchantreference = $this->getTransactionId();

        $data->Transaction->CardTxn->Card->pan = $this->getCard()->getNumber();
        $data->Transaction->CardTxn->Card->expirydate = $this->getCard()->getExpiryDate('m/y');

        $data->Transaction->TxnDetails->ThreeDSecure->verify = $this->getVerify();
        $data->Transaction->TxnDetails->ThreeDSecure->merchant_url =  $this->getMerchantUrl();
        $data->Transaction->TxnDetails->ThreeDSecure->purchase_datetime = date("Ymd H:i:s");
        $data->Transaction->TxnDetails->ThreeDSecure->purchase_desc = $this->getPurchaseDesc();

        $data->Transaction->CardTxn->method = 'auth';

        return $data;
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @param SimpleXMLElement $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     */
    public function sendData($data)
    {
        // post to DataCash
        $xml = $data->saveXML();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $xml)->send();

        return $this->response = new Response($this, $httpResponse->getBody());
    }
}
