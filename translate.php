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



<script>
  jQuery(document).ready(function(){
    // Скопируйте данные из json
    var data = "chat_data.json";
    // Создание формы массива с данными
    var array = [];
    for (var i=0; i < data.length; i++) {
      array.push(data[i]);
    }

    // Отправка данных с формы массива в функцию php через ajax
    $.ajax({
      type: "POST",
      url: "/my-php-function.php",
      data: { MyArrayData: array  },
      success: function(response){
        // Создание нового тега P, и внутрь тега P размещение
        // ответа сервера
        $('body').append('<p>'+response+'</p>');
      }
    });
  });
</script>
</body>

</html>

<?

// $data = json_decode(file_get_contents($file_path), true);
// if ($data) { processData($data); }






?>