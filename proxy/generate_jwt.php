<?php
function login_ematerai($email, $password) {
    $url = "https://backendservicestg.e-meterai.co.id/api/users/login";

    $data = '{"user":"' . $email . '","password":"' . $password . '"}';

    $headers = array(
        "Content-Type: application/json",
        "Cookie: JSESSIONID=3AFA1665D7322471690945B0001C54CF; __gx=F91BCA3C0C3540C5B2A892ACFE2BE082"
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSLVERSION,CURL_SSLVERSION_TLSv1);
    // Kalau server libcurl & OpenSSL versi lama dan error SSL,
    // kamu bisa nonaktifkan SSL verify (ini untuk development, jangan di production)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    return $response;
}

// Contoh pakai fungsi
$result = login_ematerai('sig.enterprisedev@yopmail.com', 'SIG_emet123!');
echo $result;
?>
