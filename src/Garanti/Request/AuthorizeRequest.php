<?php

namespace Payconn\Garanti\Request;

use Payconn\Common\HttpClient;
use Payconn\Common\ResponseInterface;
use Payconn\Garanti\Model\Authorize;
use Payconn\Garanti\Response\AuthorizeResponse;
use Payconn\Garanti\Token;

class AuthorizeRequest extends GarantiRequest
{
    public function send(): ResponseInterface
    {
        /** @var Authorize $model */
        $model = $this->getModel();
        /** @var Token $token */
        $token = $this->getToken();
        $amount = strval($model->getAmount() * 100);

        /** @var HttpClient $httpClient */
        $httpClient = $this->getHttpClient();
        $response = $httpClient->request('POST', $model->getBaseUrl(), [
            'form_params' => [
                'secure3dsecuritylevel' => '3d',
                'apiversion' => 'v0.01',
                'terminalprovuserid' => $model->getUserId(),
                'txntype' => $model->getType(),
                'cardnumber' => $model->getCreditCard()->getNumber(),
                'cardexpiredatemonth' => $model->getCreditCard()->getExpireMonth()->format('m'),
                'cardexpiredateyear' => $model->getCreditCard()->getExpireYear()->format('y'),
                'cardcvv2' => $model->getCreditCard()->getCvv(),
                'mode' => $this->getMode(),
                'terminalid' => $token->getTerminalId(),
                'terminaluserid' => $model->getUserId(),
                'terminalmerchantid' => $token->getMerchantId(),
                'txnamount' => $amount,
                'txncurrencycode' => $model->getCurrency(),
                'txninstallmentcount' => $model->getInstallment(),
                'orderid' => $model->getOrderId(),
                'successurl' => $model->getSuccessfulUrl(),
                'errorurl' => $model->getFailureUrl(),
                'customeremailaddress',
                'customeripaddress' => $this->getIpAddress(),
                'secure3dhash' => mb_strtoupper(sha1(
                    $token->getTerminalId().
                    $model->getOrderId().
                    $amount.
                    $model->getSuccessfulUrl().
                    $model->getFailureUrl().
                    $model->getType().
                    $model->getInstallment().
                    $token->getStoreKey().
                    mb_strtoupper(sha1(
                        $token->getPassword().
                        '0'.$token->getTerminalId()
                    ))
                )),
            ],
        ]);

        return new AuthorizeResponse($this->getModel(), [
            'content' => $response->getBody()->getContents(),
        ]);
    }
}
