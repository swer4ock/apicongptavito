<?
///подписываемся на вебхуки
$ch = curl_init("https://api.avito.ru/messenger/v3/webhook");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer ____api_avito",
]);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "url" => "https://aichatnow.ru/avito2.php",
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

print_r($result);



?>
