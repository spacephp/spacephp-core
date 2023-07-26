<?php
require_once(__DIR__ . '/helper.php');
$data = json_decode(trim(file_get_contents("php://input")), true);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
curl_setopt($ch, CURLOPT_POST, 1);
$params = http_build_query([
    "amount" => $data['amount'] * 100,
    "currency" => "usd",
    "automatic_payment_methods[enabled]" => "false",
    "return_url" => "https://imgpluz.com",
    "confirm" => "true",
    "receipt_email" => $data['email'],
    "metadata[merchant_site]" => $data['merchant_site'],
    "metadata[order_id]" => $data['order_id'],
    "payment_method" => $data['payment_method_id'],
    "shipping" => isset($data["shipping"])?$data['shipping']:null
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, $sk . ':');
$result=curl_exec ($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
curl_close ($ch);
header('Content-Type: application/json');
echo $result;
die();