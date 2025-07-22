<?php

$url = 'https://fileuploadstg.e-meterai.co.id/uploaddoc2';

$isMethodPost = $_SERVER['REQUEST_METHOD'] === 'POST';

$isUploadDoc = false;
$searchUploadDoc = "uploaddoc";
if (strpos($url, $searchUploadDoc) !== false) {
    $isUploadDoc = true;
}

$data = file_get_contents('php://input');

if ($isUploadDoc) {
    $fileName = $_FILES['file']['name'];
    $filePath = $_FILES['file']['tmp_name'];
    $fileType = $_FILES['file']['type'];

    $newFilePath = str_replace(basename($filePath), $fileName, $filePath);

    rename($filePath, $newFilePath);

    $data = array(
        'file' => '@' . $newFilePath,
    );

    foreach ($_POST as $key => $value) {
        $data[$key] = $value;
    }
}

$headers = getallheaders();
$sendHeaders = [];
$whitelistHeaders = ['Content-Type', 'Authorization'];

foreach ($headers as $key => $value) {
    if (strpos($value, 'multipart/form-data') !== false) {
        $sendHeaders[] = "Content-Type: multipart/form-data";
    } else if (in_array($key, $whitelistHeaders)) {
        $sendHeaders[] = "$key: $value";
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
if ($isMethodPost) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
}
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
    header('Content-Type: application/json');
    echo $response;
}

curl_close($ch);
