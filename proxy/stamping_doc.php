<?php
// CORS opsional (hapus jika tidak perlu diakses dari browser langsung)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Batasi hanya ke POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
    exit;
}

// Ambil JSON dari body
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

// Authorization token (bisa juga ambil dari $_POST jika diinginkan)
$bearerToken = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

// Endpoint tujuan
$url = 'https://stampservicestg.e-meterai.co.id/keystamp/adapter/docSigningZ';

// Inisialisasi cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . $bearerToken
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
// Jika kamu pakai self-signed HTTPS dan ingin skip SSL verification, aktifkan ini:
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Cek error
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Return respons dari server e-meterai
http_response_code($httpcode);
echo $response;
