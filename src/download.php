<?php

  require_once("cfg/config.php");
  require_once("func/database.php");
  require_once("func/connection.php");
  require_once("func/download.php");
  require_once("func/char.php");

  if ( !isset($_REQUEST["FileID"]) ||
       !isset($_REQUEST["Name"])   ||
       !isset($_REQUEST["RefID"]) ){
    header("Location: " . "http://" . $_SERVER["HTTP_HOST"]);
    exit;
  }

  $Page = "Download";

  if(isset($_SERVER["HTTP_RANGE"])){
    header("HTTP/1.0 403 Forbidden", true, 403);
    header("Content-Description: " . "Forbidden!");
    header("Connection: close");
    exit;
  }

 $Count = 1;
 if(isset($_GET["RefID"])){
   $Count = 0;
 }

 $gv_name = $_REQUEST["Name"];

  Connect($gDatabaseHost, $gDatabaseLogin, $gDatabasePassword, $gDatabaseName);

  // Проверка IP на блокировку
  $IP = Fetch(Query("select * from IP where IP = '" . $_SERVER["REMOTE_ADDR"] . "' limit 1"));
  if (is_array($IP))
    if (!$IP["Download"])
    {
      header("HTTP/1.0 403 Forbidden", true, 403);
      header("Content-Description: " . "IP is blocked for download!");
      header("Connection: close");
      exit;
    }

  // Проверка на зарегистрированного пользователя
  $Registered = false;
  if (isset($_COOKIE["SID"])){
    $User = Fetch(Query("select * from Users where SID = '" . $_COOKIE["SID"] . "'"));
    if (isset($User["ID"])){
      $Registered = true;
    }
  }

  // Выборка конфигурации
  $Configuration = Fetch(Query("select * from Config limit 1"));

  // Выборка основной записи ссылки
  $Reference = Fetch(Query("select * from Refers where RefID = '" . $_REQUEST["RefID"] . "' limit 1"));

  // Проверка на существование ссылки
  if (!isset($Reference["RefID"]))
  {
    header("HTTP/1.0 403 Forbidden", true, 403);
    header("Content-Description: " . "Reference not found!");
    header("Connection: close");
    exit;
  }

  // Проверка ссылки на IP адрес
  if ($Reference["IP"] != $_SERVER["REMOTE_ADDR"])
  {
    header("HTTP/1.0 403 Forbidden", true, 403);
    header("Content-Description: " . "Reference not for you! Original file http://rapidshara.ru/" . $Reference["FileID"]);
    header("Connection: close");
    exit;
  }

  // Выборка основной записи файла
  $File = Fetch(Query("select * from Files where FileID = " . $Reference["FileID"] . " limit 1"));

  // Проверка на удаление файла
  if ($File["DelMark"])
  {
    header("HTTP/1.0 404 File not found", true, 404);
    header("Content-Description: " . "File not found");
    header("Connection: close");
    exit;
  }

  // Проверка пароля
  if (!empty($File["Password"]))
    if ($File["Password"] != $_POST["Password"])
      {
        $info  = "Пароль не тот!";
        $title =  $File["Name"] . " - " . $info;
        require("info.php");
        exit();
      }

  // Проверка на текущие скачивания
  //$streams = count_connection($_SERVER["REMOTE_ADDR"]);
  //if ($streams >= $Configuration["Streams"])
  //{
  //  header("HTTP/1.0 403 Forbidden", true, 403);
  //  header("Content-Description: " . "You alredy downloading now!");
  //  header("Connection: close");
  //  exit;
  //}

  // Увеличение счетчика скачиваний если это не запрос на часть файла
  if (!isset($_SERVER["HTTP_RANGE"]) && $Count) {
    Query("update Files set IP_Download = '" . $_SERVER["REMOTE_ADDR"] . "', Date_Download = NOW(), Counter = Counter + 1 where FileID = " . $File["FileID"]);
  }

  // Создание записи соединения
  //$ConnectionID = create_connection($Reference["IP"], $Reference["FileID"], $Reference["IP_RUS"], $_SERVER["HTTP_USER_AGENT"]);

  Disconnect();

  //function close_connection()
  //{
  //  global $gDatabaseHost, $gDatabaseLogin, $gDatabasePassword, $gDatabaseName, $ConnectionID;
  //
  //  Connect($gDatabaseHost, $gDatabaseLogin, $gDatabasePassword, $gDatabaseName);
  //  destroy_connection($ConnectionID);
  //  Disconnect();
  //}

  //register_shutdown_function("close_connection");


  // Скачивание файла с докачкой
  //if ($Registered)
  //  Download($File["Path"] . $File["FileID"] . "/" . $File["Name"], $File["Name"], $File["Type"]);
  //else
  //  Download_lite($File["Path"] . $File["FileID"] . "/" . $File["Name"], $File["Name"], $File["Type"]);

  //Download_readfile($File["Path"] . "/" . $File["FileID"], $File["Name"], $File["Type"]);

  Download_X_Sendfile($File["Path"] . "/" . $File["FileID"], $gv_name, $File["Type"]);

  //Connect($gDatabaseHost, $gDatabaseLogin, $gDatabasePassword, $gDatabaseName);
  //destroy_connection($ConnectionID);
  //Disconnect();

?>