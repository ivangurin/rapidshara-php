<?php

  function get_page($method, $url, $referer, $cookie, $data, $file, $debug){

    // Формирование куки
    $cookies = "";
    if($cookie){
      if(is_array($cookie)){
        foreach($cookie as $key => $value){
         $cookies .= "Cookie: " . trim($value) . "\r\n";
        }
      }else{
        $cookies .= "Cookie: " . trim($cookie) . "\r\n";
      }
    }

    // Формирование тела запроса
    if(!empty($data)){
      foreach($data as $key => $value){
        if(!isset($body))
          $body  = "$key=$value";
        else
          $body .= "&$key=$value";
      }
    }

    switch ($method){
      case "GET";
        $header  = "GET " . $url["path"];
        if(isset($url["query"]) && !empty($url["query"]))
          $header  .= "?" . $url["query"];
        elseif(isset($body) && !empty($body))
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

    if(!empty($referer))
      $header .= "Referer: " . $referer . "\r\n";

    $header .= $cookies;
    $header .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)" . "\r\n";
    $header .= "Connection: Close" . "\r\n";
    $header .= "\r\n";

    if($debug) file_put_contents("request.html", $header . $body);

    if(!isset($url["port"])) $url["port"] = "80";

    for($i=1; $i<=10; $i++){
      $fp = fsockopen($url["host"], $url["port"]);
      if($fp) break;
    }

    if($fp){
      stream_set_timeout($fp, 300);
      fputs($fp, $header . $body);

      // Чтение заголовков
      $page["header"] = "";
      while($buffer = trim(fgets($fp))){
        $page["header"] .= $buffer . "\r\n";
      }

      // Если нужно положить контент в файл, то открываем файл на запись
      if(!empty($file)){
        if (file_exists($file)) unlink($file);
        $handle = fopen($file, "wb");
      }

      // Чтение контента
      $page["content"] = "";
      while(!feof($fp)){
        $buffer = fread($fp, 64*1024);
        if(!empty($file))
          fwrite($handle, $buffer);
        else
          $page["content"] .= $buffer;
      }

      // Если нужно положить контент в файл, то закрывает файл на запись
      if(!empty($file)) fclose($handle);

      fclose($fp);

    }else{
      return false;
    }

    return $page;

  }

 function send_file($url, $cookie, $file, $data, $debug){

    $boundary = substr(md5(time()),-8);

    // Формирование куки
    $cookies = "";
    if($cookie){
      if(is_array($cookie)){
        foreach($cookie as $key => $value){
         $cookies .= "Cookie: " . trim($value) . "\r\n";
        }
      }else{
        $cookies .= "Cookie: " . trim($cookie) . "\r\n";
      }
    }

    // Формирование тела запроса
    $body1 = "";
    if (!empty($data)){
      foreach ($data as $key => $value){
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

    for ($i=1; $i<=10; $i++){
      $fp = fsockopen($url["host"], $url["port"]);
      if($fp) break;
    }

    if($fp){

      stream_set_timeout($fp, 300);

      fputs($fp, $header . $body1);

      $handle = fopen($file, "rb");
      if($handle){
        stream_copy_to_stream($handle, $fp);
        fclose($handle);
      }else{
        return false;
      }

      fputs($fp, $body2);

      $page = stream_get_contents($fp);
      fclose($fp);

    }else{

      return false;

    }

    return $page;

  }

  function get_cookie($text){
  	$cookie = "";
    $array = explode("\n", $text);
    foreach($array as $key => $value){
      if(strpos($value, "Set-Cookie:") !== false){
        $cookie[] = trim(str_replace("Set-Cookie:", "", $value));
      }
    }
    return $cookie;
  }

  function get_value($text, $left, $right){
    $array = explode($left, $text);
    if(!isset($array[1])) return false;
    $array = explode($right, $array[1]);
    return trim($array[0]);
  }
?>