<?php

  require_once("http.php");

  upload("D:/Temp/test.zip", "test.zip", true);

  function authorization($login, $password, $debug) {

    $url     = parse_url("http://narod.yandex.ru/disk/");
    $referer = "http://narod.yandex.ru/disk/";

    // Получение главной страницы c формой
    $page = get_page("GET", $url, "", "", "", "", $debug);
    if($debug) file_put_contents("ya.auth1.html", $page);
    $cookies           = get_cookie($page["header"]);
    //$url               = parse_url(get_value($page["content"], "form method=\"post\" action=\"", "\""));
    $url               = parse_url("http://passport.yandex.ru/passport?mode=auth&retpath=http://narod.yandex.ru/disk/");
    $data["login"]     = $login;
    $data["passwd"]    = $password;

    // Получение страницы с результатом после логирования
    $page = get_page("POST", $url, $referer, $cookies, $data, "", $debug);
    if($debug) file_put_contents("ya.auth2.html", $page);
    $cookies = array_merge($cookies, get_cookie($page["header"]));
    $url     = parse_url(get_value($page["header"], "Location: ", "\n"));

    // Получение страницы с результатом переадресации
    $page = get_page("GET", $url, $referer, $cookies, "", "", $debug);
    if($debug) file_put_contents("ya.auth3.html", $page);
    $url     = parse_url(get_value($page["header"], "Location: ", "\n"));

    // Получение страницы с новым куки
    $page = get_page("GET", $url, $referer, $cookies, "", "", $debug);
    if($debug) file_put_contents("ya.auth4.html", $page);
    $url     = parse_url(get_value($page["header"], "Location: ", "\n"));
    $cookies = array_merge($cookies, get_cookie($page["header"]));

    // Получение страницы
    $page = get_page("GET", $url, $referer, $cookies, "", "", $debug);
    if($debug) file_put_contents("ya.auth5.html", $page);

print_r($cookies); exit;

    return $cookies;

  }

  function upload($file, $name, $debug) {

    // Адрес для закачки
    $url      = parse_url("http://narod.yandex.ru/disk/");
    $login    = "rapidshararu";
    $password = "iarapidsharagu";

    // Проверка на существование файла
    if(!file_exists($file)) return false;

    // Авторизация c получением кукесов
    $cookies = authorization($login, $password, $debug);
    if($cookies === false) return false;

    print_r($cookies);

    // Получение главной страницы
    $page = get_page("GET", $url, "", $cookies, "", "", $debug);
    if($debug) file_put_contents("ya.page1.html", $page);

  }

?>