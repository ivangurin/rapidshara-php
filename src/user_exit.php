<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");

  // Set page
  $gv_page = "exit";

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get user
  $go_user_manager = user_manager::get_instance();
  $go_user         = $go_user_manager->get_by_sid();

  if(!$go_user){
    header("Location: http://" . $_SERVER["HTTP_HOST"]);
    exit;
  }

  // All access for root
  if($go_user && $go_user->is_root())
    $gs_configuration["user_exit"] = true;

  // Is user exit allowed
  if(!$gs_configuration["user_exit"]){
    $gv_title = $gv_data = $go_configuration->get_comment("user_exit");
    require("info.php"); exit;
  }

  if($go_user){
    // Logout
    $go_user->logout();
    // Save
    $go_user->save();
  }

  header("Location: http://" . $_SERVER["HTTP_HOST"]);
  exit;

?>