<?php
function getProtocol2() {
    if (isset($_SERVER['HTTPS'])
        && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    ) {
            return 'https';
    }

    return 'http';
}

function site_url2() {
    return getProtocol2() . '://' . $_SERVER['HTTP_HOST'];
}

$config = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../cache/microservicessites_id' . $_SERVER["HTTP_HOST"] . '.json'), true);
$pk = isset($config["stripe_public_key"])?$config["stripe_public_key"]:'pk_test_51MhXApIIDiNeA0jAjA6oMLQw0Vj7G1CweNw6aiXlRi8xxsijjPAlx6OGuWrjupI6gAnVmJEfTRuS6xCDbd8IIByv001R2h559G';
$sk = isset($config["stripe_secret_key"])?$config["stripe_secret_key"]:'sk_test_51MhXApIIDiNeA0jAde7sTDOQpRWk2emdS0pwWg50nVPHlk0HWtCsNEIP3cLk4b5TuHo58nQEh8dc7gUJPgYLP9m700Pd6ZcJP4';
$client_id = isset($config['paypal_client_id'])?$config['paypal_client_id']:'ARgDQvu8oXGoXETQG1egnvqAMB9ozxoT40EveyBSC4IddEZwqM0FQuRUzNXwUTGaGy5-nzBk1vDNf1JF';