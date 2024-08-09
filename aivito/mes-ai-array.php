<?php

function find_product($query, $filename) {
    $file_content = file_get_contents($filename);
    $products = explode("\n", $file_content);

    foreach ($products as $product) {
        if (stripos($product, $query) !== false) {
            return $product;
        }
    }

    return null;
}

function process_client_message($message) {
    $keywords = [
        'фасовка' => 'fasovka.txt',
        'бизнес' => 'gotovii-biznes.txt',
    ];

    foreach ($keywords as $keyword => $filename) {
        if (stripos($message, $keyword) !== false) {
            return find_product($keyword, $filename);
        }
    }

    return 'Извините, мы не смогли найти подходящий товар или услугу.';
}

$client_message = 'Клиент запросил информацию о gotovii-biznes';
$response = process_client_message($client_message);
echo $response;
print_r($response);



function get_chat_information($user_id, $chat_id, $access_token) {
    $url = "https://api.avito.ru/messenger/v2/accounts/{$user_id}/chats/{$chat_id}";

    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer {$access_token}\r\n",
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return null;
    }

    return json_decode($response, true);
}

function get_item_url_from_json($json_data) {
    if (isset($json_data['context']['value']['url'])) {
        return $json_data['context']['value']['url'];
    }

    return null;
}

$access_token = '__acess__';
$user_id = '___id_account';
$chat_id = '_nomer_chata';

$chat_information = get_chat_information($user_id, $chat_id, $access_token);

if ($chat_information !== null) {
    echo "Информация о чате:\n";
    $json_data = $chat_information; // Замените $chat_information на ваш массив данных
	$item_url = get_item_url_from_json($json_data);

	if ($item_url !== null) {
	    echo "URL: " . $item_url;
	} else {
	    echo "URL не найден";
	}
} else {
    echo "Не удалось получить информацию о чате.\n";
}







?>
