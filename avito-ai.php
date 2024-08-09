<?
static $last_execution = 0;
$current_time = time();

if ($current_time - $last_execution < 5) {
    return;
}

$last_execution = $current_time;









function AiChatNow($user_text, $chat_id) {
    global $client;

    $response = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $user_text],
        ],
    ]);

    foreach ($response->choices as $result) {
        $assistant_response = $result->message->content;
    }

    AvitoSubmit($assistant_response, $chat_id); // Отправляем ответ ассистента и chat_id в функцию AvitoSubmit
}











?>