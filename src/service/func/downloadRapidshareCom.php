<?php

  function DownloadRapidshareCom($login, $password, $url, $path)
  {

    // Получение первой страницы
    $page = get_page("GET", $url, "", "", "");
    //file_put_contents("RapidshareCom.page1.html", $page["header"] . "\r\n" . $page["content"]);

    if (strpos($page["content"], "THIS FILE IS FORBIDDEN") !== false)
      return false;

    // Получение страницы с авторизацией
    unset($data);
    $url                  = parse_url(cut_str($page["content"], "form action=\"", "\""));
    $data["dl.start"]     = "PREMIUM";

    $page = get_page("POST", $url, "", $data, "");
    //file_put_contents("RapidshareCom.page2.html", $page["header"] . "\r\n" . $page["content"]);

    // Авторизация       
    unset($data);
    $url["path"]          = cut_str($page["content"], "form action=\"", "\"");
    $data["premiumlogin"] = cut_str($page["content"], "name=\"premiumlogin\" value=\"", "\"");
    $data["fileid"]       = cut_str($page["content"], "name=\"fileid\" value=\"", "\"");
    $data["filename"]     = cut_str($page["content"], "name=\"filename\" value=\"", "\"");
    $data["serverid"]     = cut_str($page["content"], "name=\"serverid\" value=\"", "\"");
    $data["accountid"]    = $login;
    $data["password"]     = $password;

    $file["name"]     = $data["filename"];
    $file["tmp_name"] = $path . md5($file["name"] . $_SERVER["REMOTE_ADDR"] . microtime());

    $page = get_page("POST", $url, "", $data, "");
    //file_put_contents("RapidshareCom.page3.html", $page["header"] . "\r\n" . $page["content"]);

    // Получение страницы с ссылкой на файл
    unset($data);
    $url                  = parse_url(cut_str($page["content"], "form action=\"", "\""));
    $cookie               = cut_str($page["header"], "Set-Cookie: ", "\n");
    $data["l"]            = cut_str($page["content"], "name=\"l\" value=\"", "\"");
    $data["p"]            = cut_str($page["content"], "name=\"p\" value=\"", "\"");
    $data["dl.start"]     = cut_str($page["content"], "name=\"dl.start\" value=\"", "\"");

    $page = get_page("POST", $url, "", $data, "");
    //file_put_contents("RapidshareCom.page4.html", $page["header"] . "\r\n" . $page["content"]);

    // Закачка файла
    $url                  = parse_url(cut_str($page["content"], "<table><tr><td><a href=\"", "\""));
    $page = get_page("GET", $url, $cookie, "", $file["tmp_name"]);
    //file_put_contents("RapidshareCom.page5.html", $page["header"] . "\r\n" . $page["content"]);

    $file["type"]  = "application/octet-stream";
    $file["error"] = 0;
    $file["size"]  = filesize($file["tmp_name"]);

    return $file;

  }
?>