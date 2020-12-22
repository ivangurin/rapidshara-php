<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");

  // Set page
  $gv_page = "rules";

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get user
  $go_user_manager = user_manager::get_instance();
  $go_user         = $go_user_manager->get_by_sid();

?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
 	<title>Правила</title>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr height="100%"><td align="center" valign="middle">
      <table border="0" cellpadding="0" cellspacing="0">
        <tr><td>
          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td height="25" align="right">&nbsp;1.&nbsp;</td>
              <td width="100%">&nbsp;Любые&nbsp;файлы&nbsp;</td>
            </tr>
            <tr>
              <td height="25" align="right">&nbsp;2.&nbsp;</td>
              <td>&nbsp;Размером&nbsp;до&nbsp;<?php echo($go_configuration->get_size()); ?>&nbsp;мегабайт&nbsp;</td>
            </tr>
            <tr>
              <td height="25" align="right">&nbsp;3.&nbsp;</td>
              <td>&nbsp;Хранятся <?php echo($go_configuration->get_days_after_download()); ?> дней после последнего скачивания</td>
            </tr>
            <tr>
              <td height="25" align="right">&nbsp;4.&nbsp;</td>
              <td>&nbsp;И <?php echo($go_configuration->get_days_after_upload()); ?> дней, если не были скачаны</td>
            </tr>
            <tr>
              <td height="25" align="right">&nbsp;5.&nbsp;</td>
              <td>&nbsp;Инвайтов в наличии нет :(</td>
            </tr>
            <tr>
              <td height="25" align="right">&nbsp;6.&nbsp;</td>
              <td>&nbsp;Об ошибках и предложениях сообщайте на <a href="mailto:info@rapidshara.ru">почту</a>&nbsp;</td>
            </tr>
          </table>
        </td></tr>
      </table>
    </td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>