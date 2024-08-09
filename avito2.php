<?php

function SaveLog($chat_id, $item)
    {
        // Сохраняем обработанное сообщение в файл
        $filename = "history/avito_chat_{$chat_id}.log";

        if (file_exists($filename)) {
            $messages = json_decode(file_get_contents($filename), true);
        } else {
            $messages = [];
        }

        array_push($messages, $item);

        $fp = fopen($filename, 'w');
        $formattedJson = json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        fwrite($fp, $formattedJson);
        fclose($fp);
        // Задержка перед обработкой сообщения
    }

function isMessageProcessed($message_id, $chat_id) {
    $filename = "history/avito_chat_{$chat_id}.log";
    if (file_exists($filename)) {
        $file_contents = file_get_contents($filename);
        return strpos($file_contents, $message_id) !== false;
    }
    return false;
}

function callAiChatNow($user_text, $chat_id, $message_id, $user_id, $access_token) {
    $url = 'https://aichatnow.ru/ai_processing.php'; // Замените на URL вашего файла ai_processing.php

    $data = [
        'user_text' => $user_text,
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'user_id' => $user_id,
        'access_token' => $access_token,
    ];

    $ch = curl_init($url);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);


    $response = curl_exec($ch);

    if (!$response) {
        echo 'Ошибка при вызове AI: ' . curl_error($ch);
    } else {
        echo 'AI ответ: ' . $response;
    }

    curl_close($ch);
  
}




$webhook_string =file_get_contents('php://input');

// Декодирование строки вебхука в массив
$webhook_array = json_decode($webhook_string, true);

// Проверка на ошибки при декодировании JSON
// if (json_last_error() == JSON_ERROR_NONE) {
    // Отправляем код 200 OK
   
    $your_user_id = '182510923';
    $item = $webhook_array;


$chat_id = $item['payload']['value']['chat_id'];
$author_id = $item['payload']['value']['author_id'];
$user_id = $item['payload']['value']['user_id'];
$user_text = $item['payload']['value']['content']['text'];
$message_id = $item['payload']['value']['id'];

$folder = $user_id;
$filename = 'access_token.txt';
$file_path = $folder . '/' . $filename;
$access_token = file_get_contents($file_path);



if (!isMessageProcessed($message_id, $chat_id)) {
          SaveLog($chat_id, $item);
             callAiChatNow($user_text, $chat_id, $message_id, $user_id, $access_token);
}
