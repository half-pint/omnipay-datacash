<?php

namespace Omnipay\DataCash\Message;

use SimpleXMLElement;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * DataCash Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getPaRes()
    {
        return $this->getParameter('pa_res');
    }

    public function setPaRes($value)
    {
        return $this->setParameter('pa_res', $value);
    }

    public function getDatacashRef()
    {
        return $this->getParameter('datacash_ref');
    }

    public function setDatacashRef($value)
    {
        return $this->setParameter('datacash_ref', $value);
    }

    public function getData()
    {
        $data = new SimpleXMLElement('<Request/>');

        $data->Authentication->client = $this->getMerchantId();
        $data->Authentication->password = $this->getPassword();

        $data->Transaction->HistoricTxn->method = 'threedsecure_authorization_request';
        $data->Transaction->HistoricTxn->reference = $this->getDatacashRef();

        $data->Transaction->HistoricTxn->pares_message = $this->getPaRes();

        return $data;
    }

    public function sendData($data)
    {
        // post to DataCash
        $xml = $data->saveXML();

        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $xml)->send();

        return $this->response = new Response($this, $httpResponse->getBody());
    }
}
