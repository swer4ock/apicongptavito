<?php
require_once __DIR__ . '/vendor/autoload.php';
use OpenAI\Client;
$openai_api_key = '_api_openai'; // 
$client = OpenAI::client($openai_api_key);
$processed_messages = [];
$data = [];
// здесь мы начнем интеграцию новой функции или доламывать старую мы добились наконец разбить входящие данные в два массива но кто их будет перебирать или еще как то незнаю... пока все очень хрупко но работает скоро начнем установку


// Установка файла для записи ошибок и вывода
$log_file_path = 'history/avito_chat_errors.log';

// Включение буферизации вывода
ob_start();

// Перенаправление ошибок в файл
ini_set('error_log', $log_file_path);
ini_set('log_errors', true);

function processMessage($message, $directory_name_category, $text_default)
{
    // foreach ($keywords as $keyword => $filename) {
    //     if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/ui', $message)) {
    //         return $keyword;
    //     }
    // }
    $directory_name_category = transliterate($directory_name_category);
    global $client;
    $knowledge_base = json_decode(file_get_contents($directory_name_category . '/knowledge_base.json'), true);
    $prompt = 'ИИ выбери ключевое слово в этом вопросе:' . $message . '. Конец вопроса.';

    // $prompt = 'Это ИИ продавец' . $text_default . 'Конец текста ИИ продавца; Это ID клиента chat_id ' . $chat_id . ' Конец ID клиента; Это база данных: ' . $knowledge_base . " Конец базы данных; Вопрос пользователя:" . $message . '. конец вопроса пользователя.';


    $data = [
        'model' => 'text-davinci-003',
        'prompt' => $prompt,
        'max_tokens' => 50,
        'n' => 1,
        'stop' => null,
        'temperature' => 0.2,
    ];

    $response = $client->completions()->create($data);
    $category = $response['choices'][0]['text'];

    return $category;
}



function generateAnswer($question, $chat_id)
{
    global $client;
    $prompt = $question;
    
    $data = [
        'model' => 'text-davinci-003',
        'prompt' => $prompt . 'Конец вопроса!',
        'max_tokens' => 1200,
        'n' => 1,
        'stop' => null,
        'temperature' => 0.9,
    ];

    $result = $client->completions()->create($data);
    $answer = $result['choices'][0]['text'];
    return $answer;
}



function getAnswer($category, $directory_name_category)
{ 
    $directory_name_category = transliterate($directory_name_category);
    $knowledge_base = json_decode(file_get_contents($directory_name_category . '/knowledge_base.json'), true);

    if (array_key_exists($category, $knowledge_base)) {
        return file_get_contents($knowledge_base[$category]['file']);
    }

    return false;
}

function transliterate($text) {
    $translit_table = array(
        'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd',
        'Е' => 'e', 'Ё' => 'e', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i',
        'Й' => 'y', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
        'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't',
        'У' => 'u', 'Ф' => 'f', 'Х' => 'n', 'Ц' => 'ts', 'Ч' => 'ch',
        'Ш' => 'sh', 'Щ' => 'shch', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '',
        'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    );

    return str_replace(array_keys($translit_table), array_values($translit_table), $text);
}

function process_client_message($message) {
    $keywords = [
        'Производство' => 'franshiza',
        // 'порошок' => 'perkar',
        'Администратор' => 'vakansiya',
        'Авитолог' => 'avitolog',
        'тележка' => 'telegka',
        'Банка' => 'vakansiya',
    ];

    foreach ($keywords as $keyword => $directory) {
        if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/ui', $message)) {
            $file_content = file_get_contents($directory . '.txt');
            return [
                'file_content' => $file_content,
                'keyword' => $keyword,
            ];
        }
    }

    return null;
}


function saveAnswer($category, $answer, $chat_id, $directory_name_category, $user_text)
{
    
    global $data;
    $category = str_replace("\n", " ", $category);
    $category = trim(preg_replace('/\s+/', ' ', $category));
    $directory = transliterate($directory_name_category);
    $knowledge_base = json_decode(file_get_contents($directory . '/knowledge_base.json'), true);
 
    // if ($directory === null) {
    //     if (!file_exists($directory)) {
    //         mkdir($directory, 0755, true);
    //     }
    // }

    $filename = $directory . '/' . transliterate($category) . '_' . time() . '.txt';
    file_put_contents($filename, $answer);

    $knowledge_base[$category] = [
        'file' => $filename,
        'user_text' => $user_text,
        'timestamp' => time(),
        'chat_id' => $chat_id

    ];

    file_put_contents  ($directory . '/knowledge_base.json', json_encode($knowledge_base, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}





function find_product($keyword, $filename) {
    if (file_exists($filename)) {
        return file_get_contents($filename);
    }
}






function AiChatNow($user_text, $chat_id, $message_id, $system_text = null, $access_token = null, $user_id = null) {
    global $client, $processed_messages; // добавьте $keywords

    if (in_array($message_id, $processed_messages)) {
        return;
    }

    $processed_messages[] = $message_id;
    
    if ($system_text !== null) {

        $client_message_info = process_client_message($system_text);
        $message = $client_message_info['file_content'];
        $directory_name_category = $client_message_info['keyword'];


        if ($message === null) {
            return; // Завершаем выполнение, если process_client_message возвращает null
        }
        
        $category = processMessage($user_text, $directory_name_category, $message); // передаем $keywords
        $answer = getAnswer($category, $directory_name_category);

        if ($answer === false) {
            $answer = generateAnswer($message . ' ' . $user_text, $chat_id);
            saveAnswer($category, $answer, $chat_id, $directory_name_category, $user_text); // передаем $keywords
        }

        $assistant_response = $answer;
        AvitoSubmit($assistant_response, $chat_id, $access_token, $user_id);
    }

    try {

    } catch (Exception $e) {
        echo "Ошибка при получении ответа от AI: " . $e->getMessage();
    }
}






function getChatHistory($chat_id) {
    $filename = "history/avito_chat_{$chat_id}.log";
    $history = [];
    
    if (file_exists($filename)) {
        $messages = json_decode(file_get_contents($filename), true);

        if ($messages !== null && $messages !== false) {
            foreach ($messages as $message) {
                $role = ($message['payload']['value']['author_id'] == '182510923') ? 'system' : 'user';
                $content = $message['payload']['value']['content']['text'];
                $history = $content;
            }
        }
    }

    return $history;
}


function AvitoSubmit($assistant_response, $chat_id, $access_token, $user_id) {
   
    // $user_id = '182510923'; // это наш ИД СКЛАД116

    // URL-адрес API Авито
    $url = "https://api.avito.ru/messenger/v1/accounts/{$user_id}/chats/{$chat_id}/messages";

    // Формирование данных для отправки
    $data = [
        'message' => [
            'text' => $assistant_response
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
}

function check_message_read_status($user_id, $message_id, $access_token) {
    // Запрос списка чатов с непрочитанными сообщениями
    $chats_url = 'https://api.avito.ru/messenger/v2/accounts/' . $user_id . '/chats?unread_only=true';
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $chats_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $chats_response = curl_exec($ch);
    curl_close($ch);
    // $chats_data = json_decode($chats_response, true);
    // $pretty_chats_data = json_encode($chats_data, JSON_PRETTY_PRINT);
    // echo $pretty_chats_data;

    $unread_chats = [];
    if ($chats_response) {
        $chats_data = json_decode($chats_response, true);
        if (isset($chats_data['chats'])) {
            foreach ($chats_data['chats'] as $chat) {
                $unread_chats[] = $chat['id'];
                // Если message_id соответствует chat_id, вернуть true
                if ($message_id == $chat['id']) {
                    return true;
                }
            }
        }
    }
    return false; // Возвращаем false, если сообщение было прочитано или если статус прочтения не определен
}





if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); 
    if (isset($data['user_text']) && isset($data['chat_id']) && isset($data['message_id'])) {
            $access_token = $data['access_token'];
            sleep(10);
            $is_read = check_message_read_status($data['user_id'], $data['chat_id'], $access_token);

            if ($is_read == true) {
                $item_title = isset($data['item_title']) ? $data['item_title'] : null;
                AiChatNow($data['user_text'], $data['chat_id'], $data['message_id'], $data['item_title'], $access_token, $data['user_id'] );
            }

        
    }
}
    // if (check_message_read_status('182510923','u2i-lnsDNrFiW49bGSDGpN9adQ', 'zZWQdhrfTJmbeebEYXzBcgWjAYeA0Ff6_aWFWSMV') == true) {
    //     echo 'true';
    // } else {
    //     echo 'false';
    // }



// Получаем содержимое вебхука
$webhook_content = file_get_contents('php://input');

// Открываем файл для записи (если файла нет, он будет создан)
$log_file = fopen('history/avito_chat_test2.log', 'a+');

// Записываем содержимое вебхука в файл
fwrite($log_file, $webhook_content . PHP_EOL);

// Закрываем файл
fclose($log_file);


// Получение содержимого буфера и его очистка
$output = ob_get_contents();
ob_clean();

// Запись содержимого буфера и ошибок в файл
if (!empty($output)) {
    file_put_contents($log_file_path, $output . PHP_EOL, FILE_APPEND);
}

?>
















<?php

// //создает кучу папок и читает следуя по массиву и достаточно суховато как автоответчик но в целом все работает
// require_once __DIR__ . '/vendor/autoload.php';
// use OpenAI\Client;
// $openai_api_key = 'api_open'; // 
// $client = OpenAI::client($openai_api_key);
// $processed_messages = [];
// // здесь мы начнем интеграцию новой функции или доламывать старую мы добились наконец разбить входящие данные в два массива но кто их будет перебирать или еще как то незнаю... пока все очень хрупко но работает скоро начнем установку



// function processMessage($message, $keywords)
// {
//     foreach ($keywords as $keyword => $filename) {
//         if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/ui', $message)) {
//             return $keyword;
//         }
//     }

//     global $client;
//     $prompt = "Текст: '$message'\nКатегория:";

//     $data = [
//         'model' => 'text-davinci-003',
//         'prompt' => $prompt,
//         'max_tokens' => 10,
//         'n' => 1,
//         'stop' => null,
//         'temperature' => 0.5,
//     ];

//     $response = $client->completions()->create($data);
//     $category = $response['choices'][0]['text'];

//     return $category;
// }



// function generateAnswer($question)
// {
//     global $client;
//     $prompt = "Вопрос: '$question'\nОтвет:";
    
//     $data = [
//         'model' => 'text-davinci-003',
//         'prompt' => $prompt,
//         'max_tokens' => 1200,
//         'n' => 1,
//         'stop' => null,
//         'temperature' => 0.5,
//     ];

//     $result = $client->completions()->create($data);
//     $answer = $result['choices'][0]['text'];
//     return $answer;
// }



// function getAnswer($category)
// {
//     $knowledge_base = json_decode(file_get_contents('knowledge_base.json'), true);

//     if (array_key_exists($category, $knowledge_base)) {
//         return file_get_contents($knowledge_base[$category]['file']);
//     }

//     return false;
// }

// function transliterate($text) {
//     $translit_table = array(
//         'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
//         'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
//         'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
//         'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
//         'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
//         'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
//         'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
//         'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
//         'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
//         'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
//         'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
//         'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
//         'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
//         'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
//     );

//     return str_replace(array_keys($translit_table), array_values($translit_table), $text);
// }

// function process_client_message($message) {
//     $keywords = [
//         'Производство' => 'franshiza',
//         'порошок' => 'perkar',
//         'администратор' => 'vakansiya',
//     ];

//     foreach ($keywords as $keyword => $directory) {
//         if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/ui', $message)) {
//             $file_content = file_get_contents($directory . '.txt');
//             return "{$file_content}";
//         }
//     }

//     return null;
// }

// function saveAnswer($category, $answer, $keywords)
// {
//     $knowledge_base = json_decode(file_get_contents('knowledge_base.json'), true);

//     $category = str_replace("\n", " ", $category);
//     $category = trim(preg_replace('/\s+/', ' ', $category));

//     $directory = null;
//     foreach ($keywords as $keyword => $dir) {
//         if (strpos($category, $keyword) !== false) {
//             $directory = $dir;
//             break;
//         }
//     }
//     if ($directory === null) {
//         $directory = transliterate($category);
//         if (!file_exists($directory)) {
//             mkdir($directory, 0755, true);
//         }
//     }

//     $filename = $directory . '/' . transliterate($category) . '_' . time() . '.txt';
//     file_put_contents($filename, $answer);

//     $knowledge_base[$category] = [
//         'file' => $filename,
//         'timestamp' => time(),
//     ];

//     file_put_contents('knowledge_base.json', json_encode($knowledge_base, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
// }





// function find_product($keyword, $filename) {
//     if (file_exists($filename)) {
//         return file_get_contents($filename);
//     }
// }

// старый вариант
// function AiChatNow($user_text, $chat_id, $message_id, $system_text = null) {
//     global $client, $processed_messages;

//     // Если сообщение уже было обработано, пропустите его
//     if (in_array($message_id, $processed_messages)) {
//         return;
//     }

//     // Добавьте message_id в список обработанных сообщений
//     $processed_messages[] = $message_id;
    
//     // Получаем историю чата
   
    
//     // Если есть системный текст, добавьте его перед пользовательским текстом
//     if ($system_text !== null) {

//         $message = process_client_message($system_text);
        
            
//             $category = processMessage($user_text);

//             $answer = getAnswer($category);

//             if ($answer === false) {
//                 $answer = generateAnswer($message . ' ' . $user_text);
//                 saveAnswer($category, $answer);
//             }

//             $assistant_response = $answer;


//             AvitoSubmit($assistant_response, $chat_id); // Отправляем ответ ассистента и chat_id в функцию AvitoSubmit
//     }

 
//     try {

//     } catch (Exception $e) {
//         echo "Ошибка при получении ответа от AI: " . $e->getMessage();
//     }
// }


// function AiChatNow($user_text, $chat_id, $message_id, $system_text = null) {
//     global $client, $processed_messages, $keywords; // добавьте $keywords

//     if (in_array($message_id, $processed_messages)) {
//         return;
//     }

//     $processed_messages[] = $message_id;
    
//     if ($system_text !== null) {
//         $message = process_client_message($system_text);

//         if ($message === null) {
//             return; // Завершаем выполнение, если process_client_message возвращает null
//         }
        
//         $category = processMessage($user_text, $keywords); // передаем $keywords
//         $answer = getAnswer($category);

//         if ($answer === false) {
//             $answer = generateAnswer($message . ' ' . $user_text);
//             saveAnswer($category, $answer, $keywords); // передаем $keywords
//         }

//         $assistant_response = $answer;
//         AvitoSubmit($assistant_response, $chat_id);
//     }

//     try {

//     } catch (Exception $e) {
//         echo "Ошибка при получении ответа от AI: " . $e->getMessage();
//     }
// }






// function getChatHistory($chat_id) {
//     $filename = "history/avito_chat_{$chat_id}.log";
//     $history = [];
    
//     if (file_exists($filename)) {
//         $messages = json_decode(file_get_contents($filename), true);

//         if ($messages !== null && $messages !== false) {
//             foreach ($messages as $message) {
//                 $role = ($message['payload']['value']['author_id'] == '182510923') ? 'system' : 'user';
//                 $content = $message['payload']['value']['content']['text'];
//                 $history[] = [
//                     'role' => $role,
//                     'content' => $content
//                 ];
//             }
//         }
//     }

//     return $history;
// }


// function AvitoSubmit($assistant_response, $chat_id) {
//     $access_token = '0vo93cJwR2uWocfTbQUEmAjSmEAyiYQxuhKKqY4m';
//     $user_id = '182510923'; // это наш ИД СКЛАД116

//     // URL-адрес API Авито
//     $url = "https://api.avito.ru/messenger/v1/accounts/{$user_id}/chats/{$chat_id}/messages";

//     // Формирование данных для отправки
//     $data = [
//         'message' => [
//             'text' => $assistant_response
//         ],
//         'type' => 'text'
//     ];

//     // Инициализация cURL
//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         'Content-Type: application/json',
//         'Authorization: Bearer ' . $access_token
//     ]);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

//     // Отправка запроса и получение ответа
//     $response = curl_exec($ch);

//     // Закрытие cURL
//     curl_close($ch);

//     // Обработка и вывод результата
//     if ($response === false) {
//         echo "Ошибка отправки сообщения.";
//     } else {
//         $response_data = json_decode($response, true);
//         if (isset($response_data['id'])) {
//             echo "Сообщение успешно отправлено. ID сообщения: {$response_data['id']}";
//         } else {
//             echo "Ошибка отправки сообщения. Ответ API: {$response}";
//         }
//     }
// }

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $data = json_decode(file_get_contents('php://input'), true);
//     if (isset($data['user_text']) && isset($data['chat_id']) && isset($data['message_id'])) {
//         $item_title = isset($data['item_title']) ? $data['item_title'] : null;
//         AiChatNow($data['user_text'], $data['chat_id'], $data['message_id'], $data['item_title']);
//     }
// }


// // Получаем содержимое вебхука
// $webhook_content = file_get_contents('php://input');

// // Открываем файл для записи (если файла нет, он будет создан)
// $log_file = fopen('history/avito_chat_test.log', 'a+');

// // Записываем содержимое вебхука в файл
// fwrite($log_file, $webhook_content . PHP_EOL);

// // Закрываем файл
// fclose($log_file);



?>




<?php

// просто отвечает без сохранение на сколько я помню возможно пользуется еще историей но это не наверняка, а так же здесь есть глюк что он все равно всем отвечает зато читает информацию если попали в нужный масив
// require_once __DIR__ . '/vendor/autoload.php';
// use OpenAI\Client;
// $openai_api_key = 'api_open'; // 
// $client = OpenAI::client($openai_api_key);
// $processed_messages = [];

// // function process_client_message($message) {
// //     $keywords = [
// //         'фасовки' => 'fasovka.txt',
// //         'Производство' => 'franshiza.txt',
// //     ];

// //     foreach ($keywords as $keyword => $filename) {
// //         if (stripos($message, $keyword) !== false) {
// //             $file_content = file_get_contents($filename);
// //             return "{$file_content}";
// //         }
// //     }
    
// //     return null; // Возвращаем null, если ни одно из ключевых слов не найдено
// function process_client_message($message) {
//     $keywords = [
//         'Производство' => 'franshiza.txt',
//         'Услуги' => 'yslug.txt',
//         'Обогреватель' => 'yslug.txt',
//         'Фасовочный' => 'yslug.txt',
        
//     ];

//     foreach ($keywords as $keyword => $filename) {
//         // Используем регулярные выражения для поиска полного совпадения слова в строке
//         if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/ui', $message)) {
//             $file_content = file_get_contents($filename);
//             return "{$file_content}";
//         }
//     }

//     return null; // Возвращаем null, если ни одно из ключевых слов не найдено
// }




// function find_product($keyword, $filename) {
//     if (file_exists($filename)) {
//         return file_get_contents($filename);
//     }
// }


// function AiChatNow($user_text, $chat_id, $message_id, $system_text = null) {
//     global $client, $processed_messages;

//     // Если сообщение уже было обработано, пропустите его
//     if (in_array($message_id, $processed_messages)) {
//         return;
//     }

//     // Добавьте message_id в список обработанных сообщений
//     $processed_messages[] = $message_id;
    
//     // Получаем историю чата
   
    
//     // Если есть системный текст, добавьте его перед пользовательским текстом
//     $message = process_client_message($system_text);
//     if ($message !== null) {

        
        
//          // Добавляем текущее сообщение пользователя в историю
//          // $chat_history[] = ['role' => 'system', 'content' => $message];
//          // $chat_history = getChatHistory($chat_id);

//         //  $chat_history[] = ['role' => 'user', 'content' => $user_text];

//         //  $response = $client->chat()->create([
//         //             'model' => 'gpt-3.5-turbo',
//         //             'messages' => $chat_history,
//         //         ]);

//         // foreach ($response->choices as $result) {
//         //     $assistant_response = $result->message->content;
//         // }

//             $result = $client->completions()->create([
//                 'model' => 'text-davinci-003',
//                 'prompt' => ' Сергей '.$message. ' Клиент '. $user_text,
//                 'max_tokens' => 1200,
//                 'temperature' => 1
//             ]);

//             $assistant_response = $result['choices'][0]['text'];
//             AvitoSubmit($assistant_response, $chat_id); // Отправляем ответ ассистента и chat_id в функцию AvitoSubmit
//     }

 
//     try {

//     } catch (Exception $e) {
//         echo "Ошибка при получении ответа от AI: " . $e->getMessage();
//     }
// }



// function getChatHistory($chat_id) {
//     $filename = "history/avito_chat_{$chat_id}.log";
//     $history = [];
    
//     if (file_exists($filename)) {
//         $messages = json_decode(file_get_contents($filename), true);

//         if ($messages !== null && $messages !== false) {
//             foreach ($messages as $message) {
//                 $role = ($message['payload']['value']['author_id'] == '182510923') ? 'system' : 'user';
//                 $content = $message['payload']['value']['content']['text'];
//                 $history[] = [
//                     'role' => $role,
//                     'content' => $content
//                 ];
//             }
//         }
//     }

//     return $history;
// }


// function AvitoSubmit($assistant_response, $chat_id) {
//     $access_token = '0vo93cJwR2uWocfTbQUEmAjSmEAyiYQxuhKKqY4m';
//     $user_id = '182510923'; // это наш ИД СКЛАД116

//     // URL-адрес API Авито
//     $url = "https://api.avito.ru/messenger/v1/accounts/{$user_id}/chats/{$chat_id}/messages";

//     // Формирование данных для отправки
//     $data = [
//         'message' => [
//             'text' => $assistant_response
//         ],
//         'type' => 'text'
//     ];

//     // Инициализация cURL
//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         'Content-Type: application/json',
//         'Authorization: Bearer ' . $access_token
//     ]);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

//     // Отправка запроса и получение ответа
//     $response = curl_exec($ch);

//     // Закрытие cURL
//     curl_close($ch);

//     // Обработка и вывод результата
//     if ($response === false) {
//         echo "Ошибка отправки сообщения.";
//     } else {
//         $response_data = json_decode($response, true);
//         if (isset($response_data['id'])) {
//             echo "Сообщение успешно отправлено. ID сообщения: {$response_data['id']}";
//         } else {
//             echo "Ошибка отправки сообщения. Ответ API: {$response}";
//         }
//     }
// }

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $data = json_decode(file_get_contents('php://input'), true);
//     if (isset($data['user_text']) && isset($data['chat_id']) && isset($data['message_id'])) {
//         $item_title = isset($data['item_title']) ? $data['item_title'] : null;
//         AiChatNow($data['user_text'], $data['chat_id'], $data['message_id'], $data['item_title']);
//     }
// }


// // Получаем содержимое вебхука
// $webhook_content = file_get_contents('php://input');

// // Открываем файл для записи (если файла нет, он будет создан)
// $log_file = fopen('history/avito_chat_test.log', 'a+');

// // Записываем содержимое вебхука в файл
// fwrite($log_file, $webhook_content . PHP_EOL);

// // Закрываем файл
// fclose($log_file);


?>