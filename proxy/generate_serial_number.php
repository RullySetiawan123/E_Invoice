<?php
// Pastikan hanya menerima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
    exit;
}

// Ambil input JSON dari body request
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

// Authorization token (bisa juga ambil dari $_POST jika diinginkan)
$bearerToken = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$targetUrl = 'https://stampv2stg.e-meterai.co.id/chanel/stampv2';

// Inisialisasi cURL
$ch = curl_init($targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . $bearerToken
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

// Eksekusi cURL
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Cek error
if (curl_errno($ch)) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// Kirim respons dari e-Meterai ke client
http_response_code($httpcode);
header('Content-Type: application/json');
echo $response;
