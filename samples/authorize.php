<?php

require_once __DIR__.'/../vendor/autoload.php';

use Payconn\Common\CreditCard;
use Payconn\Garanti;
use Payconn\Garanti\Currency;
use Payconn\Garanti\Model\Authorize;
use Payconn\Garanti\Token;

$token = new Token('30691297', '7000679', '123qweASD/', '12345678');
$creditCard = new CreditCard('4282209004348015', '22', '08', '123');
$authorize = new Authorize();
$authorize->setTestMode(true);
$authorize->setCreditCard($creditCard);
$authorize->setCurrency(Currency::TRY);
$authorize->setAmount(100);
$authorize->setInstallment(1);
$authorize->setSuccessfulUrl('https://webhook.site/d123fc90-9aa6-42b5-b83c-846ecf739edc');
$authorize->setFailureUrl('https://webhook.site/d123fc90-9aa6-42b5-b83c-846ecf739edc');
$authorize->generateOrderId();
$response = (new Garanti($token))->authorize($authorize);
if ($response->isRedirection()) {
    echo $response->getRedirectForm();
}
