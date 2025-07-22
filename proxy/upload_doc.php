<?php
// Konfigurasi URL tujuan
$targetUrl = 'https://fileuploadstg.e-meterai.co.id/uploaddoc2';

// Pastikan file diunggah
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK || !isset($_POST['token'])) {
    http_response_code(400);
    echo json_encode([
        'statusCode' => '01',
        'status' => 'failed',
        'message' => 'Missing file or token'
    ]);
    exit;
}

// Simpan file sementara
$uploadedFile = $_FILES['file'];
$tempPath = $uploadedFile['tmp_name'];
$fileName = basename($uploadedFile['name']);
$destination = __DIR__ . "/temp_upload/" . $fileName;

// Pastikan folder temp_upload/ tersedia
if (!is_dir(__DIR__ . "/temp_upload")) {
    mkdir(__DIR__ . "/temp_upload", 0777, true);
}

if (!move_uploaded_file($tempPath, $destination)) {
    http_response_code(500);
    echo json_encode([
        'statusCode' => '01',
        'status' => 'failed',
        'message' => 'Failed to move uploaded file'
    ]);
    exit;
}

// Siapkan payload cURL
$postFields = [
    'token' => $_POST['token'],
    'file' => new CURLFile($destination)
];

// Authorization token (bisa juga ambil dari $_POST jika diinginkan)
$bearerToken = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

// Siapkan header
$headers = [];
if ($bearerToken) {
    $headers[] = 'Authorization: ' . $bearerToken;
}

// Kirim request ke server tujuan
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Sesuaikan jika pakai SSL valid
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode([
        'statusCode' => '99',
        'status' => 'failed',
        'message' => 'cURL Error: ' . curl_error($ch)
    ]);
} else {
    http_response_code($httpCode);
    echo $response;
}

curl_close($ch);

// (Opsional) hapus file sementara
@unlink($destination);
