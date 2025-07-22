<?php

$url = $_GET['url'];

$headers = getallheaders();
$sendHeaders = [];
$whitelistHeaders = ['Content-Type', 'Authorization'];

foreach ($headers as $key => $value) {
    if (in_array($key, $whitelistHeaders)) {
        $sendHeaders[] = "$key: $value";
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(array(
        'error' => true,
        'message' => curl_error($ch),
    ));
} else {
    http_response_code(curl_getinfo($ch, CURLINFO_HTTP_CODE));
    header('Content-Type: application/pdf');
    echo $response;
}

curl_close($ch);
