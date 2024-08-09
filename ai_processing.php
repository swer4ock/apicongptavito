<?php

require_once __DIR__ . '/vendor/autoload.php';
use OpenAI\Client;
$openai_api_key = '_api_openai'; // 
$client = OpenAI::client($openai_api_key);

function get_chat_information($user_id, $chat_id, $access_token) {
    $url = "https://api.avito.ru/messenger/v2/accounts/{$user_id}/chats/{$chat_id}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$access_token}"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    $response_data = json_decode($response, true);

    // Проверяем, есть ли элемент 'title' в ответе
    if (isset($response_data['context']['value']['title'])) {
        return $response_data['context']['value']['title'];
    } else {
        return ''; // Возвращаем пустую строку, если элемент 'title' отсутствует
    }
}



function send_data_to_avito_ai($user_text, $chat_id, $message_id, $item_title, $access_token, $user_id) {
    $url = 'https://aichatnow.ru/avito-aigpt.php'; // Замените на URL вашей страницы avito-ai.php

    $data = [
        'user_text' => $user_text,
        'user_id' => $user_id,
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'item_title' => $item_title,
        'access_token' => $access_token,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    if (!$response) {
        echo 'Ошибка при отправке данных на avito-ai.php: ' . curl_error($ch);
    } else {
        echo 'Ответ от avito-ai.php: ' . $response;
    }

    curl_close($ch);
}

// $data = [
//     "user_text" => "Здравствуйте. Интересует производство обогревателей. Можно узнать поподробнее об бизнесе?",
//     "chat_id" => "u2i-oceAgkA8g5YRWC7QnLBAdg",
//     "message_id" => "sdf",
//     "user_id" => '182510923',
// ];

$data = json_decode(file_get_contents('php://input'), true);

$user_text = $data['user_text'];
$chat_id = $data['chat_id'];
$message_id = $data['message_id'];
$user_id = $data['user_id'];
$access_token = $data['access_token'];

// Исправлено: теперь вызываем функцию get_chat_information() с параметрами
$item_title = get_chat_information($user_id, $chat_id, $access_token);

if ($item_title !== '') {
    send_data_to_avito_ai($user_text, $chat_id, $message_id, $item_title, $access_token, $user_id);

    // Получаем содержимое вебхука
    $webhook_content = $data;

    // Исправлено: json_encode() для корректного сохранения данных вебхука в файл
    $log_content = json_encode($webhook_content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Открываем файл для записи (если файла нет, он будет создан)
    $log_file = fopen('history/avito_chat_test.log', 'a+');

    // Записываем содержимое вебхука в файл
    fwrite($log_file, $log_content . PHP_EOL);

    // Закрываем файл
    fclose($log_file);

}



?>