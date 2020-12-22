<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/file.php");

  // Set page
  $gv_page = "files";

  // Set report
  $gv_report = "";
  if(isset($_GET["report"]))
    $gv_report = db::escape($_GET["report"]);

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

  switch($gv_report){
    case "active":
      require("files_active.php");
      exit;
    case "deleted":
      require("files_deleted.php");
      exit;
  }

?>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Мои файлы</title>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="top">&nbsp;</td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>