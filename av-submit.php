написать текст
<?php
// Замените на свой токен доступа
$access_token = 'VDny_xsuS5m5-inh2jXmPgttNviGsq9uDFHjR6Ez';

// Замените на значения user_id и chat_id
$user_id = '182510923'; // это наш ИД СКЛАД116
$chat_id = 'u2i-eWcUo~UphqHrody5sgfWxg';

// Текст сообщения
$message_text = 'проверка связи!';

// URL-адрес API Авито
$url = "https://api.avito.ru/messenger/v1/accounts/{$user_id}/chats/{$chat_id}/messages";

// Формирование данных для отправки
$data = [
    'message' => [
        'text' => $message_text
    ],
    'type' => 'text'
];

// Инициализация cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Отправка запроса и получение ответа
$response = curl_exec($ch);

// Закрытие cURL
curl_close($ch);

// Обработка и вывод результата
if ($response === false) {
    echo "Ошибка отправки сообщения.";
} else {
    $response_data = json_decode($response, true);
    if (isset($response_data['id'])) {
        echo "Сообщение успешно отправлено. ID сообщения: {$response_data['id']}";
    } else {
        echo "Ошибка отправки сообщения. Ответ API: {$response}";
    }
}
?>
