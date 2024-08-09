<?

$json_string = file_get_contents('kl.json');
$data = json_decode($json_string, TRUE); // true для доступа к данным в виде ассоциативного массива
// foreach($data as $key => $value) {
//     echo $key . ' = ' . $value . '<br>';
// }

// $data = json_decode($json_string);
foreach($data as $row)
{
    if($row['Кол-во (шт.)'] == 0){
        echo $row['CAI']." - Нет в наличии";
    }
    else{
        // echo $row['CAI']." - В наличии";
    }
}


// создаем переменную для адреса API Avito 
$avitoApiURL = "https://api.avito.ru/";  
 
// подключаемся к API Avito 
$cliente = curl_init($avitoApiURL); 

// экранирование запроса 
$findValue = urlencode("\кварц");

// отправляем запрос API Avito для поиска объявлений
$query = array("q" => $findValue);

curl_setopt($cliente, CURLOPT_URL, $avitoApiURL."/search?".http_build_query($query)); 
curl_setopt($cliente, CURLOPT_RETURNTRANSFER, true);
 
// получаем ответ из API Avito 
$response = curl_exec($cliente);
 
// преобразуем полученный JSON в ассоциативный массив 
$response = json_decode($response, true);
 
// проверяем наличие результатов 
if(isset($response['results']) && count($response['results']) > 0) {
  
  // получаем ID первого удовлитворяющего условию объявления
  $idItem = $response['results'][0]['id'];
  
  // отправляем запрос на снятие объявления с публикации
  $urlUnpublishItem = $avitoApiURL . "items/" . $idItem . "/unpublish";
 
  $clienteUnpub = curl_init($urlUnpublishItem);  
  curl_setopt($clienteUnpub, CURLOPT_CUSTOMREQUEST, 'POST');  
  curl_setopt($clienteUnpub, CURLOPT_RETURNTRANSFER, true);
 
  $responseUnpub = curl_exec($clienteUnpub);
 
  // проверяем успешность снятия с публикации
  if($responseUnpub == "ok") {
    echo "Объявление с ID " . $idItem . " успешно снято с публикации";
  }  
 
  // освобождаем ресурсы 
  curl_close($clienteUnpub);
}
 
// освобождаем ресурсы 
curl_close($cliente);

?>









