<?php
$access_token = 'gaa1-uOBTCCQ16rK9F76ow1TY8J5EYb1KYTeT2-K';
$user_id = '182510923';
$item_id = '2824807826';

$url = "https://api.avito.ru/core/v1/accounts/{$user_id}/items/{$item_id}/";

$headers = [
    'Authorization: Bearer ' . $access_token
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpcode == 200) {
    $response_data = json_decode($response, true);
    // Обработка данных об объявлении
    echo "URL: " . $response_data['url'] . "\n";
    echo "Status: " . $response_data['status'] . "\n";
    echo "Start time: " . $response_data['start_time'] . "\n";
    echo "Finish time: " . $response_data['finish_time'] . "\n";
    echo "Autoload item ID: " . $response_data['autoload_item_id'] . "\n";

    if (!empty($response_data['vas'])) {
        echo "Applied VAS services:\n";
        foreach ($response_data['vas'] as $vas) {
            // Обработка данных об услуге VAS
        }
    }


} else {
    echo "Error: {$response}\n";
}
?>
