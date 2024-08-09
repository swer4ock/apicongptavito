<form method="post">
  <input type="textarea" name="zapros" placeholder="">
  <input type="submit" value="Submit">
</form><div style='width:500px'>
<?

$zapros = $_POST["zapros"];
echo "Это вопрос для ИИ<br>";
echo $zapros;
echo "<br><br>";
    # First, send a request to the text-davinci-003 model
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/engines/davinci-codex/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = array( "prompt" => $zapros, "model" => "", "max_tokens" => 400, "temperature" => 0.5, "stop" => "");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_POST, 1);
    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "Authorization: Bearer api_openai";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
$result = json_decode($result);
// if (!empty($result->error)) {
//     echo 'Error: ' . $result->error->message;
//     exit;
// }
$result = $result->choices[0]->text;


    // # Use the response from the text-davinci-003 model as the prompt for the davinci-codex model
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/engines/text-davinci-003/completions");
       //   curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/engines/davinci-codex/completions");//text-davinci-003 davinci-codex
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     $data = array( "prompt" => $result, "model" => "", "max_tokens" => 2000, "temperature" => 0.5,
         // "object" => "text_completion",
         // "created" => 1673960829,
         // 'choices' => [
         //         'text' => $result
         //     ],
         "stop" => "");


     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
     curl_setopt($ch, CURLOPT_POST, 1);
     $headers = array();
     $headers[] = "Content-Type: application/json";
     $headers[] = "Authorization: Bearer API_OPENAI";
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
     $result2 = curl_exec($ch);
     if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
     }
     curl_close ($ch);

 $result2 = json_decode($result2);
 // if (!empty($result2->error)) {
 //     echo 'Error: ' . $result2->error->message;
 //     exit;
 // }








// echo "Помощник: ";
// echo $result;
echo "<br><br><br><br>";
echo "Ответ: ";
$result2 = $result2->choices[0]->text;
echo $result2;

?>

