список подписок

<?
//проверка подписок на вебхуки
$access_token = '___api_avito';
$url = 'https://api.avito.ru/messenger/v3/webhook/subscriptions';
//$url = 'https://api.avito.ru/messenger/v3/subscriptions';

$headers = [
    "Content-Type: application/json",
    'Authorization: Bearer ' . $access_token
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

print_r($response);
print_r($httpcode);
// if ($httpcode == 200) {
//     $response_data = json_decode($response, true);
//     // Обработка ответа
// } else {
//     echo "Error: {$response}\n";
// }


?>
