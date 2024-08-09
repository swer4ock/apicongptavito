<?php
//подключаемся получаем acess токен
$client_id = '___api_clien';
$client_secret = '_api_secret';

$token_url = 'https://api.avito.ru/token';

$headers = [
    'Content-Type: application/x-www-form-urlencoded'
];

$post_fields = [
    'grant_type' => 'client_credentials',
    'client_id' => $client_id,
    'client_secret' => $client_secret
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpcode == 200) {
    $response_data = json_decode($response, true);
    $access_token = $response_data['access_token'];
    echo "Access token: {$access_token}\n";

    // Сохраняем access_token в текстовый файл
    $file_path = 'access_token.txt';
    file_put_contents($file_path, $access_token);
} else {
    echo "Error: {$response}\n";
}
?>
