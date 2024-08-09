 <?php



// define( 'WP_USE_THEMES', true );


// require __DIR__ . '/wp-blog-header.php';

?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <style>
    .scrolling-div {
        height: 200px; /* Установите желаемую высоту */
        overflow-y: scroll; /* Включите скролл для вертикальной прокрутки */
      }
          .scrolling-div::-webkit-scrollbar {
        width: 12px;
      }

      .scrolling-div::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 10px;
      }
      .blockKarkas {background-color: #fde1e2; box-shadow: 0px 10px 20px 0px rgba(58, 92, 227, 0.5); border-radius: 10px; padding: 20px; box-shadow: 0px 0px 10px 2px #00cce6; border-radius: 10px;box-shadow: 0px 0px 10px 2px #00cce6; border-radius: 10px; background-color: #f5f5f5; }

       a { font-size: 15px; font-weight: bold; }

    </style>
 </head>
 <body>

<style type="text/css">
#example .new { opacity: 0; }
#example .div_opacity {
    -webkit-transition: opacity .0000005s steps(1, start);
    -moz-transition: opacity .0000005s steps(1, start);
    -ms-transition: opacity .0000005s steps(1, start);
    -o-transition: opacity .0000005s steps(1, start);
    transition: opacity .0000005s steps(1, start);
    opacity: 1;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
 $.fn.animate_Text = function() {
  var string = this.text();
  return this.each(function(){
   var $this = $(this);
   $this.html(string.replace(/./g, '<span class="new">$&</span>'));
   $this.find('span.new').each(function(i, el){
    setTimeout(function(){ $(el).addClass('div_opacity'); }, 20 * i);
   });
  });
 };
 $('#example').show();
 $('#example').animate_Text();
});
</script>

<?
      require 'vendor/autoload.php';

      use Monolog\Level;
      use Monolog\Logger;
      use Monolog\Handler\StreamHandler;



      // create a log channel
      $log = new Logger('AiChatNow.Ru');
      $log->pushHandler(new StreamHandler('path/to/ai.log', Level::Debug));
      // $fileBlock = 'path/to/your2.log';
      // $fileBlock = file_get_contents($fileBlock); // Отправить два пакета
      $file = 'path/to/ai.log';
      $content = file_get_contents($file);
      // попытка поочистки данных от не нужного мусора из файла юрлог
      //$content = preg_replace('/\[\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}\+\d{2}:\d{2}\]/', '', $content); //прислал бот на удаление скообок и датт
      //$content = preg_replace("/[\[\]]/", "", $content);
      file_put_contents($file, $content);

  // echo "<div class='scrolling-div'>".$content."</div>";

?>



      <div class="container">
  <div class="row ">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4><a href="http://AiChatNow.Ru">AiChatNow.Ru</a></h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="card">
                <div class="card-header">
                  Contact List
                </div>
                <div class="card-body">
                  <ul class="list-group">
                    <li class="list-group-item"><p class=""><a href="indexai.php">Искуственный интелект</a></p></li>
                    <li class="list-group-item"><p><a href="picasso.php">Пикассо</a></p></li>

                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-9">
              <div class="card">
                <div class="card-header">
                  Chat
                  <?
                                    //echo $content;
                                    //echo "<div class='scrolling-div'>".$content."</div>";
                                    $content = substr($content, -1500);
                                    ?>
                </div>

                <div class="card-body">
                  <div class="card-footer">
                          <form id="send-message-form" method="post">
                            <div class="form-group"><p>Буфер память робота</p>
                              <textarea rows="10"   name="history" id="copyval" style="width:100%"></textarea><p>Запрос для робота</p>
                              <textarea class="form-control" name="zapros" id="message-input" rows="10" placeholder="Type your message here"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send</button>
                          </form>


                           <?
      // echo "<p><b> (Человек)-</b>";
      // echo $zapros;
      // echo "</p>";
      if (!empty($_POST["zapros"])) {
        $history =  $_POST["history"];
        $zapros = $_POST["zapros"];
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $content = json_decode($content, true);
        // $client = OpenAI::client('API_OPENAI');
        //       $result = $client->completions()->create([
        //           'model' => 'text-davinci-003',
        //           'prompt' => "ИСТОРИЯ:".$history."Вопрос человека: ".$zapros,
        //           // 'prompt' => hash("sha256", $content.$zapros),
        //            "max_tokens" => 2000,
        //            "temperature" => 1,
        //       ]);

$client = OpenAI::client('api_openai');
              $result = $client->completions()->create([
                  'model' => 'text-davinci-003',
                  'prompt' => "ИСТОРИЯ:".$history."Вопрос человека: ".$zapros,
                  // 'text_url' => "https://www.avito.ru/moskva/predlozheniya_uslug/uslugi_fasovki_sypuchih_i_zhidkih_do_5kg_2793269614",
                  // 'prompt' => hash("sha256", $content.$zapros),
                   "max_tokens" => 2000,
                   "temperature" => 1,
              ]);
        $data = $result;
        echo $content;
        $result =$result['choices'][0]['text'];
            // echo 'Ответ первый бот:'.$result; // an open-source, ?>
                        </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card message-bubble-left">
                        <div class="card-header">
                          Человек
                        </div>
                        <div class="card-body">
                          <p><?  echo $zapros;?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card message-bubble-right">
                        <div class="card-header">
                          Машина
                        </div>
                        <div class="card-body" id="example">
                          <p id="text_container"><? echo $result; ?></p>

                        <!-- Здесь будут отображаться сообщения -->
                        </div>

                      </div>
                     </div>
                    </div>
<?
            // add records to the log
            $log->info('Человек '.$zapros);
            //$log->info('Ответ: '.$result);
            // $result = hash("sha256", $zapros.$content);
            //$log->info('Бот: '.$result2);
            $log->info('Машина: '.$result);



// Приведенный код проверяет сохранение JSON файла. Этот код принимает данные в формате JSON из POST-запроса, открывает или создает файл JSON, читает текущий контент файла, добавляет новые данные к существующему контенту файла, записывает обновленные данные обратно в файл JSON и закрывает файл.
// объект стандартного класса
            $json_data = json_encode($data);
            //Затем сохраните данные в файл:
            file_put_contents('path/to/file_name.json', $json_data);
      }else{
?>
<br>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card message-bubble-right">
                        <div class="card-header">
                          Машина
                        </div>
                        <div class="card-body" id="example">
                          <p>                  <div class="row">
                    <div class="col-md-12">
                      <div class="card message-bubble-right">
                        <div class="card-header">

                        </div>
                        <div class="card-body" id="example">
                          <p>Я - обученная модель языка, созданная OpenAI.</p>
                          <p>Я могу отвечать на вопросы, генерировать текст, переводить языки, обрабатывать естественный язык и многое другое.</p>
                          <p>Если есть какой-то конкретный запрос, который вы хотите, чтобы я выполнил, скажите мне, и я постараюсь помочь вам как можно лучше.</p>

                        <!-- Здесь будут отображаться сообщения -->
                        </div>

                      </div>
                     </div>
                    </div>

                        <!-- Здесь будут отображаться сообщения -->
                        </div>

                      </div>
                     </div>
                    </div>
<? }?>


                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="row">
                  <div class="col-12">
            <h6 style="color:white; font-weight: 900; box-shadow: 1px, 1px, 1px, black;">ООО "Склад116" - руководитель: Гарипов Марсель Ульфатович (ИНН 023101640661). ИНН 1660362104, ОГРН 1211600029948. ОКПО 77596262, зарегистрировано 16.04.2021 по юридическому адресу 420088, Республика Татарстан, г Казань, Халитова 8. Размер уставного капитала - 10 000 рублей. Статус: действующая с 16.04.2021 Контакты, +79869310192</h6>
          </div>
        </div>


  </div>



<?


  $json = json_decode( file_get_contents('path/to/file_name.json') );
  // echo "<pre>";
  //   print_r($json);
  // echo "</pre>";

// Этот код принимает данные в формате JSON из POST-запроса, открывает или создает файл JSON, читает текущий контент файла, добавляет новые данные к текущему контенту файла, записывает обновленные данные обратно в файл JSON и закрывает файл

    // Get the data from the POST request
  if (!empty($zapros)) {
   // code...

    $json_data = json_decode($zapros);

    // Add the sender information to the data
   $json_data['sender'] = 'openai';

    // Open or create the JSON file
    $json_file = fopen('chat_data.json', 'r+');

    // Read the current content of the JSON file
    $current_data = json_decode(fread($json_file, filesize('chat_data.json')), true);

    // Add the new data to the current content of the JSON file
    $current_data[] = $json_data;

    // Write the updated data back to the JSON file
    fwrite($json_file, json_encode($current_data));

    // Close the file
    fclose($json_file);
 }

// Этот код читает файл JSON, декодирует данные JSON и итерируется через данные. Для каждого элемента данных проверяется значение ключа "sender" (отправитель) и в зависимости от этого выводится сообщение, добавляя соответствующий префикс "User:" или "OpenAI:".

// // Read the JSON file
// $json_file = file_get_contents('chat_data.json');

// // Decode the JSON data
// $data = json_decode($json_file, true);

// // Iterate through the data
// foreach($data as $item) {
//     // Check if the message was sent by the user or OpenAI
//     if ($item['sender'] == 'user') {
//         echo 'User: ' . $item['message'] . '<br>';
//     } else if ($item['sender'] == 'openai') {
//         echo 'OpenAI: ' . $item['message'] . '<br>';
//     }
// }


// Выведем название записи echo $post->post_title;
//Для вывода страницы записи в WordPress вы можете использовать класс WP_Post. Вы можете использовать функцию get_post(), чтобы получить объект WP_Post для любой записи, и затем использовать этот объект, чтобы получить доступ к деталям записи:
// $post = get_post(11);
// echo $post->post_title;// Выведем название записи



      ?>


<script>


  $('#text_container').clone().appendTo('#copyval'); // Вставляем текст в поле ввода
document.getElementById("copyval").value = document.getElementById("text_container").innerHTML;
</script>



<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(92179574, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/92179574" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>