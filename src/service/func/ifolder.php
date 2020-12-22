<?php

  function registration($name, $email, $password, $gender, $birth_year, $birth_month, $birth_day)
  {
    $url = parse_url("http://ipapko.ru/auth/register/");

    if (empty($gender)) $gender = rand(1, 2);
    if (empty($birth_year)) $birth_year = rand(1970, 1990);
    if (empty($birth_month)) $birth_month = rand(1, 12);
    if (empty($birth_day)) $birth_day = rand(1, 28);

    $page = get_page("GET", $url, "", "");
    //file_put_contents("ifolder.reg.page1.html", $page);

    //Пытаемся ввести номер
    for ($i=1; $i<=10; $i++)
    {
      // Парсер страницы
      $data["return_path"]      = cut_str($page, "name=\"return_path\" value=\"", "\"");
      $data["return_params"]    = cut_str($page, "name=\"return_params\" value=\"", "\"");
      $data["session"]          = cut_str($page, "name=\"session\" value=", ">");
      $data["cmd"]              = cut_str($page, "name=\"cmd\" value=\"", "\"");
      $data["name"]             = $name;
      $data["email"]            = $email;
      $data["email_check"]      = $email;
      $data["password"]         = $password;
      $data["password_check"]   = $password;
      $data["gender"]           = $gender;
      $data["birth_year"]       = $birth_year;
      $data["birth_month"]      = $birth_month;
      $data["birth_day"]        = $birth_day;
      $data["confirmed_number"] = get_code($url, $page);

      // Получение страницы с подтвержденным файлом
      $page = get_page("POST", $url, "", $data);
      //file_put_contents("ifolder.reg.page2.html", $page);

      if (strpos($page, "неверный код") === false)
        break;
    }

    if (strpos($page, "Подтверждение регистрации") !== false)
      return array("retcode"=>"1", "message"=>"Регистрация успешна");

    if (strpos($page, "Пользователь с E-mail") !== false)
      return array("retcode"=>"0", "message"=>"Пользователь уже существует");  

    return array("retcode"=>"0", "message"=>"Ошибка при регистрации");
  }

  function authorization($url, $email, $password)
  {
    if(!empty($email) and !empty($password))
    {
      // Получение главной страницы
      $page = get_page("GET", $url, "", "");
      //file_put_contents("ifolder.auth.page1.html", $page);

      // Парсер страницы
      $url["path"]           = cut_str($page, "form action=\"", "\"");

      $data["cmd"]           = cut_str($page, "name=\"cmd\" value=\"", "\"");
      $data["return_path"]   = cut_str($page, "name=\"return_path\" value=\"", "\"");
      $data["return_params"] = cut_str($page, "name=\"return_params\" value=\"", "\"");
      $data["email"]         = $email;
      $data["password"]      = $password;

      // Результат авторизации
      $page = get_page("GET", $url, "", $data);
      //file_put_contents("ifolder.auth.page2.html", $page);

      // Анализ результата
      if (strpos($page, "auth_ok") !== false)
      {
        $url    = parse_url(cut_str($page, "Location: ", "\n"));
        $cookie = trim(cut_str($page, "Set-Cookie: ", "\n"));
        return array("retcode" => 1, "url" => $url, "cookie" => $cookie, "message" => "Авторизация успешна!");
      }
      else
      {
        return array("retcode" => 0, "message" => "Ошибка авторизации!");
      }
    }
  }

  function upload($file, $desc, $pass, $login, $password, $debug)
  {
    // Адрес для закачки
    $url = parse_url("http://ifolder.ru/");

    // Проверка на существование файла
    if (!file_exists($file)) return false;

    // Авторизация
    $auth = authorization($url, $login, $password);

    // Результат авторизации
    if (!$auth["retcode"])
    {
      return false;
    }
    else
    {
      $url    = $auth["url"];
      $cookie = $auth["cookie"];
    }

    // Получение главной страницы
    $page = get_page("GET", $url, $cookie, "");
    if($page === false) return false;

    if(!empty($debug))
      file_put_contents("ifolder.upload.page1.html", $page);

    // Парсер страницы
    $data["upload_params"]     = cut_str($page, "name=\"upload_params\" value=\"", "\"");
    $data["clone"]             = cut_str($page, "name=\"clone\" value=\"", "\"");
    $data["progress_bar"]      = cut_str($page, "name=\"progress_bar\" value=\"", "\"");
    $data["upload_host"]       = cut_str($page, "name=\"upload_host\" value=\"", "\"");
    $data["MAX_FILE_SIZE"]     = cut_str($page, "name=\"MAX_FILE_SIZE\" value=\"", "\"");
    $data["show_progress_bar"] = "0";

    $url = parse_url(cut_str($page, "form-data\" action=\"", "?") . "?serial=" . $data["progress_bar"]);

    // Получаем страницу после закачки файла
    $page = get_page_file($url, $cookie, $file, $data);
    if($page === false) return false;

    if(!empty($debug))
      file_put_contents("ifolder.upload.page2.html", $page);

    // Получение страницы с результатом закачки
    $url = parse_url(cut_str($page, "Location: ", "\n"));

    $page = get_page("GET", $url, $cookie, "");
    if($page === false) return false;

    if(!empty($debug))
      file_put_contents("ifolder.upload.page3.html", $page);

    $ok = false;

    for ($i = 1; $i <=10; $i++)
    {
      // Парсер страницы
      $number = cut_str($page, ";: <b>", "<br>");

      $data["descr_$number"]    = $desc;
      $data["password_$number"] = $pass;
      $data["via_ints_$number"] = "1";
      $data["email"]            = "";
      $data["confirmed_number"] = get_code($url, $page);
      $data["session"]          = cut_str($page, "name=\"session\" value=", ">");
      $data["action"]           = cut_str($page, "name=\"action\" value=\"", "\"");

      // Получение страницы с подтвержденным файлом
      $page = get_page("POST", $url, $cookie, $data);

      if(!empty($debug))
        file_put_contents("ifolder.upload.page4.html", $page);

      if (strpos($page, "неверный код") === false)
      {
        $ok = true;
        break;
      }
    }

    if (!$ok) return false;

    // Парсер страницы результата
    $number       = cut_str($page, "Файл &#8470; ", " подтвержден");
    if($number === false) return false;

    return $number;

    //$url_download = cut_str($page, "Ссылка для скачивания файла: <a href=\"", "\"");
    //$url_control  = $url["scheme"] . "://" . $url["host"] . "/control/" . cut_str($page, "/control/", "\"");

  }

  function prolongation1($email, $password, $amount, $debug)
  {

    $url = parse_url("http://ifolder.ru/");

    // Авторизация
    $auth = authorization($url, $email, $password);

    // Результат авторизации
    if (!$auth["retcode"])
    {
      die($auth["message"]);
    }
    else
    {
      $url    = $auth["url"];
      $cookie = $auth["cookie"];
    }

    // Число файлов на странице
    if(empty($amount)) $amount = 10;

    $ok = true;

    // Получение страницы со списком файлов
    $url = parse_url($url["scheme"] . "://" . $url["host"] . "/iframe3?tt=946&folder_id=-1&page=0&o=status&direct=u&amount=$amount");
    $page = get_page("GET", $url, $cookie, "");
    if($page === false)
      die("Не получилось получить страницу со списком файлов! URL = " . $url["scheme"] . "://" . $url["host"] . "/iframe3?tt=946&folder_id=-1&page=0&o=status&direct=u&amount=$amount");

    if($ok === true)
      if(!empty($debug))
        file_put_contents("ifolder.prol.page1.html", $page);

    // Разбиваем страницу в массив
    $array = explode("Доступен до", $page);

    // Начиная со второй строки анализируем каждый файл
    for ($i=1; $i<count($array); $i++)
    {

      $ok = true;

      // Панель управления файлом
      $url = parse_url(cut_str($array["$i"], "<a href=\"", "\""));
      $page = get_page("GET", $url, $cookie, "");
      if($page === false)
        $ok = false;

      if($ok === true)
        if(!empty($debug))
          file_put_contents("ifolder.prol.page2.html", $page);

      // Парсер страницы
      if($ok === true)
      {
        $number          = cut_str($page, "8470; ", "<");
        print_r($number . " - ");
        flush();
      }

      if($ok === true)
      {
        $data["prolong"] = cut_str($page, "name=\"prolong\" value=\"", "\"");
        if($data["prolong"] !== false)
          print_r("ok");
        else
        {
          print_r("Не удалось определить ссылку продления!");
          $ok = false;
        }
        print_r("<br>\r\n");
        flush();
      }

      // Подтверждение файла
      if($ok === true)
      {
        $page = get_page("POST", $url, $cookie, $data);
        if($page === false)
        {
          print_r("Не удалось получить страницу " . $url . " " . $data);
          print_r("<br>\r\n");
          $ok = false;
        }
      }

      if($ok === true)
        if(!empty($debug))
          file_put_contents("ifolder.prol.page3.html", $page);
    }
  }

  function prolongation2($email, $password, $amount)
  {

    $url = parse_url("http://ifolder.ru/");

    // Авторизация
    $auth = authorization($url, $email, $password);

    // Результат авторизации
    if (!$auth["retcode"])
    {
      die($auth["message"]);
    }
    else
    {
      $url    = $auth["url"];
      $cookie = $auth["cookie"];
    }

    // Число файлов на странице
    if (empty($amount)) $amount = 10;

    // Получение страницы со списком файлов
    $url = parse_url($url["scheme"] . "://" . $url["host"] . "/iframe3?folder_id=-1&page=0&o=status&direct=u&amount=$amount");
    $page = get_page("GET", $url, $cookie, "");
    //file_put_contents("ifolder.prol.page1.html", $page);

    // Если устаревших файлов нет, то выходим
    if (strpos($page, "Устарел") === false)
      die("Устарелых файлов нет!\n");

    // Разбиваем страницу в массив
    $array = explode("Устарел", $page);

    // Начиная со второй строки анализируем каждый файл
    for ($i=1; $i<count($array); $i++)
    {
      // Панель управления файлом
      $url = parse_url(cut_str($array["$i"], "<a href=\"", "\""));
      $page = get_page("GET", $url, $cookie, "");
      //file_put_contents("ifolder.prol.page2.html", $page);

      // Страница с рекламодателями для просмотра
      $url = parse_url(cut_str($page, "или <a href=\"", "\""));
      $page = get_page("GET", $url, $cookie, "");
      //file_put_contents("ifolder.prol.page3.html", $page);

      // Страница с кучей кукесов и переадресацией
      $url = parse_url(cut_str($page, "<font size=\"+1\"><a href=", ">"));
      $page = get_page("GET", $url, $cookie, "");
      //file_put_contents("ifolder.prol.page4.html", $page);

      // Вырезание всех кукесов в массив
      if (isset($cookies)) unset($cookies);
      $cookies = explode("Set-cookie: ", $page);
      $last_row = count($cookies) - 1;
      unset($cookies["0"]);
      $cookies["$last_row"] = substr($cookies["$last_row"], 0, strpos($cookies["$last_row"], "\n"));

      // Страница с двумя фреймами. В первом данные агавы. Во втором данные рекламодателя.
      $url = parse_url(cut_str($page, "Location: ", "\n"));
      $page = get_page("GET", $url, $cookies, "");
      //file_put_contents("ifolder.prol.page5.html", $page);

      // Открытие первого фрейма со счетчиком
      $url = parse_url($url["scheme"] . "://" . $url["host"] . cut_str($page, "f_top\" src=\"", "\""));
      $page = get_page("GET", $url, $cookies, "");
      //file_put_contents("ifolder.prol.page6.html", $page);

      // Ждем 30 сек
      sleep(30);

      // Получение этой же страницы
      $page = get_page("GET", $url, $cookies, "");
      //file_put_contents("ifolder.prol.page7.html", $page);

      //Пытаемся ввести номер
      for ($j=1; $j<=10; $j++)
      {
        // Парсер страницы
        $data["confirmed_number"] = get_code($url, $page);
        $data["session"]          = cut_str($page, "name=\"session\" value=", ">");
        $data["action"]           = cut_str($page, "name=action value=", ">");

        // Ввод кода и получение страницы с подтвержденным файлом
        $page = get_page("POST", $url, $cookies, $data);
        //file_put_contents("ifolder.prol.page8.html", $page);

        if (strpos($page, "неверный код") === false)
          break;
      }

      // После ввода кода нас пересылают на страницу результата
      $url  = parse_url(cut_str($page, "Location: ", "\n"));
      $page = get_page("GET", $url, $cookie, "");
      //file_put_contents("ifolder.prol.page9.html", $page);

    }
  }

  function get_page($method, $url, $cookie, $data)
  {

    // Формирование куки
    $cookies = "";
    if ($cookie)
    {
      if (is_array($cookie))
      {
        foreach($cookie as $key => $value)
        {
         $cookies .= "Cookie: " . trim($value) . "\r\n";
        }
      }
      else
      {
        $cookies .= "Cookie: " . trim($cookie) . "\r\n";
      }
    }

    // Формирование тела запроса
    if (!empty($data))
    {
      foreach ($data as $key => $value)
      {
        if(!isset($body))
          $body  = "$key=$value";
        else
          $body .= "&$key=$value";
      }
    }

    switch ($method)
    {
      case "GET";
        $header  = "GET " . $url["path"];
        if (isset($url["query"]) && !empty($url["query"]))
          $header  .= "?" . $url["query"];
        elseif (isset($body) && !empty($body))
          $header  .= "?" . $body;
        $header .= " HTTP/1.1" . "\r\n";
        $header .= "Host: " . $url["host"] . "\r\n";
        $body    = "";
        break;
      case "POST";
        if(isset($url["query"]))
          $header  = "POST " . $url["path"] . "?" . $url["query"] . " HTTP/1.0" . "\r\n";
        else
          $header  = "POST " . $url["path"] . " HTTP/1.0" . "\r\n";
        $header .= "Host: " . $url["host"] . "\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded" . "\r\n";
        $header .= "Content-Length: " . strlen($body) . "\r\n";
        break;
    }

    $header .= $cookies;
    $header .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)" . "\r\n";
    $header .= "Connection: Close" . "\r\n";
    $header .= "\r\n";

    //file_put_contents("ifolder.request.html", $header . $body);

    if(!isset($url["port"])) $url["port"] = "80";

    for ($i=1; $i<=20; $i++)
    {
      $fp = fsockopen($url["host"], $url["port"]);
      if($fp) break;
    }
    if($fp)
    {
      stream_set_timeout($fp, 300);
      fputs($fp, $header . $body);
      $page = stream_get_contents($fp);
      fclose($fp);
    }
    else
    {
      return false;
    }

    return $page;
  }

  function get_page_file($url, $cookie, $file, $data)
  {

    $boundary = substr(md5(time()),-8);

    // Формирование куки
    $cookies = "";
    if ($cookie)
    {
      if (is_array($cookie))
      {
        foreach($cookie as $key => $value)
        {
         $cookies .= "Cookie: " . trim($value) . "\r\n";
        }
      }
      else
      {
        $cookies .= "Cookie: " . trim($cookie) . "\r\n";
      }
    }

    // Формирование тела запроса
    $body1 = "";
    if (!empty($data))
    {
      foreach ($data as $key => $value)
      {
        $body1 .= "--$boundary" . "\r\n";
        $body1 .= "Content-Disposition: form-data; name=\"$key\"" . "\r\n" . "\r\n";
        $body1 .= $value . "\r\n";
      }
    }

    $body1 .= "--$boundary" . "\r\n";
    $body1 .= "Content-Disposition: form-data; name=\"filename\"; filename=\"" . basename($file) . "\"" . "\r\n";
    $body1 .= "Content-Type: application/octet-stream" . "\r\n" . "\r\n";

    //$filebody = file_get_contents($file);

    $body2  = "\r\n" . "--$boundary--" . "\r\n";

    // Формирование заголовка
    $header  = "POST " . $url["path"] . "?" . $url["query"] . " HTTP/1.1" . "\r\n";
    $header .= "Host: " . $url["host"] . "\r\n";
    $header .= "Content-Type: multipart/form-data; boundary=$boundary" . "\r\n";
    $header .= "Content-Length: " . (strlen($body1) + filesize($file) + strlen($body2)) . "\r\n";
    $header .= $cookies;
    $header .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)" . "\r\n";
    $header .= "Referer: " . $url["scheme"] . "://" . "ifolder.ru" . "\r\n";
    $header .= "Connection: Close" . "\r\n";
    $header .= "\r\n";

    //file_put_contents("ifolder.debug.request.html", $header . $body1 . $body2);

    if(!isset($url["port"])) $url["port"] = "80";

    for ($i=1; $i<=20; $i++)
    {
      $fp = fsockopen($url["host"], $url["port"]);
      if($fp) break;
    }
    if($fp)
    {
      stream_set_timeout($fp, 300);

      fputs($fp, $header . $body1);

      $handle = fopen($file, "rb");
      if($handle)
      {
        stream_copy_to_stream($handle, $fp);
        fclose($handle);
      }
      else
      {
        die("ERROR: Can't open file.");
      }

      fputs($fp, $body2);

      $page = stream_get_contents($fp);
      fclose($fp);
    }
    else
    {
      die("ERROR: Can't connect.");
    }

    return $page;

  }

  function get_code($url, $page)
  {
    $sid = cut_str($page, "file.wav?session=", "'");
    $url = "http://www.rapidshara.ru/ifolder/" . $sid;
    $code = file_get_contents($url);
    return $code;
  }

  function cut_str($text, $left, $right)
  {
    $array = explode($left, $text);
    if (!isset($array[1]))
    {
      return false;
    }
    $array = explode($right, $array[1]);
    return trim($array[0]);
  }

?>