<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/file.php");
  require_once("class/navigation.php");

  // Set page
  $gv_page    = "reports";

  // Set section
  $gv_section = "";
  if(isset($_GET["section"]))
    $gv_section = db::escape($_GET["section"]);

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
    header("Location: /"); exit;
  }

  // If not root
  if(!$go_user->is_root()){
    header("Location: /"); exit;
  }

  if($gv_section == "files"){
    switch($gv_report){
      case "uploaded":
        require("reports_files_uploaded.php");
        exit;
      case "downloaded":
        require("reports_files_downloaded.php");
        exit;
      case "not_downloaded":
        require("reports_files_not_downloaded.php");
        exit;
      case "deleted":
        require("reports_files_deleted.php");
        exit;
      case "removed":
        require("reports_files_removed.php");
        exit;
      case "to_delete":
        require("reports_files_to_delete.php");
        exit;
      case "to_remove":
        require("reports_files_to_remove.php");
        exit;
    }
  }

  if($gv_section == "connections"){
    header("Location: http://hostside.ru/apache-server-status");
    exit;
  }

?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
 	<title>Отчеты</title>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle"></td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>