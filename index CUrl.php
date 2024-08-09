<form method="post">
  <input type="textarea" name="zapros" placeholder="">
  <input type="submit" value="Submit">
</form>


<?php
Echo "<div style='width:500px'>" ;
$zapros = $_POST["zapros"];
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //$input = $_POST['zapros'];
    // делаем что-то с $input
if(isset($_POST["zapros"])){

    // Обработка данных формы


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = array( "prompt" => $zapros, "model" => "text-davinci-003", "max_tokens" => 1800, "stop" => "" );

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
// curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"prompt\":\"пожожди, такое чувстао бкдто тебе нужен мой код подверждения, тебе то зачем?\",\"max_tokens\":2048,\"stop\":\"\"}"); //

curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = "Authorization: Bearer ___api_openai";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);

// Decode JSON response
$response = json_decode($result);

// Check if there is any error
if (!empty($response->error)) {
    echo 'Error: ' . $response->error->message;
    exit;
}

Echo $zapros;
Echo "<br><br>";
echo $response->choices[0]->text;
Echo "</div>" ;
}else{echo "пусто";}
?>