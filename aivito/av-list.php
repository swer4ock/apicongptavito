<?php

$access_token = '__token___tesovii';

function getAdvertisementsList($access_token, $per_page = 25, $page = 1, $status = 'active', $category = null) {
    // URL-адрес API Авито
    $url = "https://api.avito.ru/core/v1/items?per_page={$per_page}&page={$page}&status={$status}";

    if ($category !== null) {
        $url .= "&category={$category}";
    }

    // Инициализация cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);

    // Отправка запроса и получение ответа
    $response = curl_exec($ch);

    // Закрытие cURL
    curl_close($ch);

    // Обработка и вывод результата
    if ($response === false) {
        echo "Ошибка получения списка объявлений.";
        return null;
    } else {
        $response_data = json_decode($response, true);
        if (isset($response_data['resources'])) {
            return $response_data['resources'];
        } else {
            echo "Ошибка получения списка объявлений. Ответ API: {$response}";
            return null;
        }
    }
}

$access_token = '___tak_tocken_test';
$advertisements = getAdvertisementsList($access_token, 50, 1, 'active', 111);

if ($advertisements !== null) {
    foreach ($advertisements as $advertisement) {
        echo "ID объявления: {$advertisement['id']}\n";
        echo "Статус объявления: {$advertisement['status']}\n";
        echo "Категория объявления: {$advertisement['category']}\n";
        echo "Ссылка на объявление: {$advertisement['url']}\n";
        echo "\n";
    }
} else {
    echo "Не удалось получить список объявлений.\n";
}
       



?>
