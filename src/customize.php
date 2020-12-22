<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");

  // Set page
  $gv_page = "customize";

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get user
  $go_user_manager = user_manager::get_instance();
  $go_user         = $go_user_manager->get_by_sid();

  // If not registered
  if(!$go_user){
    header("Location: http://" . $_SERVER["HTTP_HOST"]); exit;
  }

  // If not root
  if(!$go_user->is_root()){
    header("Location: http://" . $_SERVER["HTTP_HOST"]); exit;
  }

?>
<html>
<head>
	<meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
 	<title>Отчеты</title>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle">В разработке</td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>