<?php
  function DownloadRapidshareRu($url, $path)
  {

    // Получение первой страницы
    $page = get_page("GET", $url, "", "", "");
    //file_put_contents("RapidshareRu.page1.html", $page["header"] . "\r\n" . $page["content"]);

    // Закачка файла
    $url              = parse_url(cut_str($page["content"], "a href=\"'+'", "\""));
    $cookie           = cut_str($page["header"], "Set-Cookie: ", "\n");

    $file["name"]     = iconv ("UTF-8", "CP1251", basename($url["path"]));
    $file["tmp_name"] = $path . md5($file["name"] . $_SERVER["REMOTE_ADDR"] . microtime());

    $page = get_page("GET", $url, $cookie, "", $file["tmp_name"]);

    $file["type"]  = "application/octet-stream";
    $file["error"] = 0;
    $file["size"]  = filesize($file["tmp_name"]);

    return $file;
  }                            
?>