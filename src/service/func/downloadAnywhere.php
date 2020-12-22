<?php
  function DownloadAnywhere($url, $path)
  {
    $file["tmp_name"] = $path . md5($url . $_SERVER["REMOTE_ADDR"] . microtime());

    // Закачка файла
    $page = get_page("GET", $url, "", "", $file["tmp_name"]);
    //file_put_contents("Anywhere.page1.html", $page["header"] . "\r\n" . $page["content"]);

    $file["name"]  = basename($url["path"]);
    $file["type"]  = "application/octet-stream";
    $file["error"] = 0;
    $file["size"]  = filesize($file["tmp_name"]);   

    return $file;
  }
?>