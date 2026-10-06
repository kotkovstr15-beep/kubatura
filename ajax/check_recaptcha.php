<?php
$recaptcha_key = '6LfVZt0UAAAAACd1XIU4sO67Gq2PRUaqogJUbVwK';
$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
$recaptcha_params = [
    'secret' => $recaptcha_key,
    'response' => $_POST['recaptchaResponse'],
    'remoteip' => $_SERVER['REMOTE_ADDR'],
];

$ch = curl_init($recaptcha_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $recaptcha_params);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
//if (!empty($response)) {
//    $decoded_response = json_decode($response);
//}
echo $response;