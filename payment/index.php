<?php
//$pk = 'pk_live_51M3meeDZjfnMZsuCxTk7WjFUbOoo8UslUQlw8TE6eZLnqquitZYDqKvo9QJcZ2dLb9xu6IQvFlcSiREwUNL6l1Jw00Mq6qFxEu';
//$sk = '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';
//$pk = 'pk_test_51M3meeDZjfnMZsuC9dA2TGn5OpE9G0j2By5pmmq5MaLwU78TsIMR93dLCmega2WCQKiKXyD0LVeQzEGKo95n9NgO00jx3j3PHt';
//$sk = 'sk_test_51M3meeDZjfnMZsuCjI59PecoHJG1Lw0joGRrK99q9Mhu71Ckm9SqePnLCnNFLvTyoMTjUNGXeFvA4J0q9nDRdJqp009jQGypWN';

if (isset($_GET['stripe-make-payment'])) {
    include(__DIR__ . '/stripe-make-payment.php');
    die();
}
if (isset($_GET['stripe-get-payment-form'])) {
    include(__DIR__ . '/stripe-payment-form.php');
    die();
}
if (isset($_GET['paypal_checkout'])) {
    include(__DIR__ . '/paypal-checkout.php');
    die();
}