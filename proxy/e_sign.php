<?php

$url = $_GET['url'];

$data = file_get_contents('php://input');
$data = json_decode($data, true);

$headers = getallheaders();
$sendHeaders = [];
$whitelistHeaders = ['Content-Type', 'Authorization', 'x-Gateway-APIKey'];

foreach ($headers as $key => $value) {
    if (in_array($key, $whitelistHeaders)) {
        $sendHeaders[] = "$key: $value";
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
// if ($isMethodPost) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
// }
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
    header('Content-Type: application/json');
    echo $response;
}

curl_close($ch);
