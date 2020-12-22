<?php
  
  require_once("cfg/config.php");
  require_once("func/database.php");
  require_once("func/check_ip.php");

  // Старница загрузки
  $Page = "Remote";
  
  header("Location: ");
  exit;

  Connect($gDatabaseHost, $gDatabaseLogin, $gDatabasePassword, $gDatabaseName);

  // Выборка конфигурации
  $Configuration = Fetch(Query("select * from Config limit 1"));

  // Проверка на зарегистрированного пользователя
  $Registered = false;
  if (isset($_COOKIE["SID"]))
  {
    $User = Fetch(Query("select * from Users where SID = '" . $_COOKIE["SID"] . "'"));
    if (isset($User["ID"]))
    {
      $Registered = true;
    }
  }

  // Разрешаем всем закачивать файлы по умолчанию
  $allow_upload = 1;

  // Проверка на резрешение закачки файлов
  if ($allow_upload)
  {
    if (!$Configuration["Upload"])
    {
      $allow_upload = 0;
    }
  }

  // Проверка на резрешение закачки файлов для зарубежных IP
  if ($allow_upload)
  {
    if ($Configuration["Upload_Rus"])
    {
      $IP_Rus = check_ip($_SERVER["REMOTE_ADDR"]);
      if (!$IP_Rus)
      {
        $allow_upload = 0;
      }
    }
  }

  // Проверка IP адреса на блокировку
  if ($allow_upload)
  {
    $IP = Fetch(Query("select * from IP where IP = '" . $_SERVER["REMOTE_ADDR"] . "' limit 1"));
    if (isset($IP["IP"]) && !$IP["Upload"])
    {
      $allow_upload = 0;
    }
  }

  Disconnect();
?>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <script type="text/javascript" src="/js/ProgressBar.js"></script>
  <script type="text/javascript" src="/js/StartUpload.js"></script>
  <script type="text/javascript">
    function SetFocus()
    {
      document.forms["Remote"].url.select();
      document.forms["Remote"].url.focus();
    }
  </script>
	<title>Удаленная загрузка файла</title>
</head>
<body onload="SetFocus();">
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td><?php require("menu.php"); ?></td>
    </tr>
    <tr>
      <td height="100%" align="center" valign="middle">
        <div id="Form" style="display: block;">
          <form name="Remote" action="upload" method="POST" onsubmit="StartUpload();">
            <table border="0" width="60%" cellpadding="0" cellspacing="0">
              <tr><td>
                <p>Только "<b>прямые</b>" ссылки :) <font color="white">но для тебя можно еще и с rapidshare.com!</font></font></p>
              </td></tr>
              <tr>
                <td width="100%"><input type="text" name="url" maxlength="300" style="width:100%" value=""></td>
                <td><input name="submit" type="submit" value="Закачать"</td>
              </tr>
            </table>
          </form>
        </div>
        <div id="Progress" style="display: none;">
          Закачивается... Ждите! <br /><br />
          <script type="text/javascript">
            var bar = createBar(400,15,'white',1,'#006699','#006699',385,17,3,"");
          </script>
        </div>
      </td>
    </tr>
    <tr>
      <td align="center"><?php require("counters.php");?></td>
    </tr>
  </table>
</body>
</html>