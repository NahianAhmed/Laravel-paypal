<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Transaction;

class PaymentController extends Controller
{
    public function execute(){
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AS5sbpA5u-OqBhXhfMZ4MB8tXflkDiGPuv5A65JzXhZnV-ch04aDjHx_ubakEX_Wgk8QW8Ih5B-FtYn6',     // ClientID
                'EGv24uF4Loz1QRiLS_ZQgvkdZ0ebYkjUjodmJABr3iYQ_xXTdAlIHyjU1GZSoF1THFuLWsiPxUPxlBYX'      // ClientSecret
            )
    );

     $paymentId = request('paymentId');
     $payment = Payment::get($paymentId, $apiContext);

     $execution = new PaymentExecution();
     $execution->setPayerId(request('PayerID'));

     $transaction = new Transaction();
     $amount = new Amount();
     $details = new Details();

     $details->setShipping(2.2)
         ->setTax(1.3)
         ->setSubtotal(17.50);


     $amount->setCurrency('USD');
     $amount->setTotal(21);
     $amount->setDetails($details);
     $transaction->setAmount($amount);

     $execution->addTransaction($transaction);

     $result = $payment->execute($execution, $apiContext);

        return $result;
    }
}
