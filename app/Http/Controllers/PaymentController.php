<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Transaction;

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\RedirectUrls;


class PaymentController extends Controller
{
    public function create()
    {


        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AS5sbpA5u-OqBhXhfMZ4MB8tXflkDiGPuv5A65JzXhZnV-ch04aDjHx_ubakEX_Wgk8QW8Ih5B-FtYn6',     // ClientID
                'EGv24uF4Loz1QRiLS_ZQgvkdZ0ebYkjUjodmJABr3iYQ_xXTdAlIHyjU1GZSoF1THFuLWsiPxUPxlBYX'      // ClientSecret
            )
        );





        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
           $item1->setName('Ground Coffee 40 oz')
                    ->setCurrency('USD')
                    ->setQuantity(1)
                    ->setSku("123123") // Similar to `item_number` in Classic API
                    ->setPrice(7.5);


        $item2 = new Item();
          $item2->setName('Granola bars')
                 ->setCurrency('USD')
                 ->setQuantity(5)
                 ->setSku("321321") // Similar to `item_number` in Classic API
                 ->setPrice(2);

        $itemList = new ItemList();
        $itemList->setItems(array($item1, $item2));



        $details = new Details();
        $details->setShipping(1.2)
                ->setTax(1.3)
                ->setSubtotal(17.50);


    $amount = new Amount();
    $amount->setCurrency("USD")
              ->setTotal(20)
              ->setDetails($details);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
                ->setItemList($itemList)
                ->setDescription("Payment description")
                ->setInvoiceNumber(uniqid());


   $redirectUrls = new RedirectUrls();
   $redirectUrls->setReturnUrl("http://localhost/paypal/execute-payment")
                ->setCancelUrl("http://localhost/paypal/cancel");

   $payment = new Payment();
   $payment ->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));


      $payment->create($apiContext);


     return  redirect($payment->getApprovalLink()) ;





   }


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
