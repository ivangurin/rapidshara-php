<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/file.php");
  require_once("class/host.php");

  // Set page
  $gv_page = "remove";

  // If id not exist
  if(!isset($_GET["id"])){
    header("Location: http://" . $_SERVER["HTTP_HOST"]);exit;
  }
  
  // Get id  
  $gv_id = db::escape($_GET["id"]);  
  
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

  // All access for root
  if($go_user && $go_user->is_root())
    $gs_configuration["file_remove"] = true;

  // Is remove allowed
  if(!$gs_configuration["file_remove"]){
    $gv_title = $gv_data = $go_configuration->get_comment("file_remove");
    require("info.php"); exit;
  }

  // Get file
  $go_file_manager = file_manager::get_instance();
  $go_file = $go_file_manager->get_by_id($gv_id);
  
  // If not found  
  if(!$go_file){
    $gv_title = $gv_data = "НЛО не нашло этот файл";
    require("info.php"); exit;
  }
  
  // If removed
  if($go_file->is_removed()){
    $gv_title = $gv_data = "Прилетало НЛО и удалило этот файл!";
    require("info.php"); exit;
  }  
  
  // If other user
  if($go_user->get_id() != $go_file->get_user_id() && !$go_user->is_root()){
    $gv_title = $gv_data = "Файл не ваш! За вами уже вылетело НЛО!";
    require("info.php"); exit;
  } 
  
  $go_file->remove(); 
  $go_file->save(); 

  $gv_title = $gv_data = "НЛО окончательно удалило файл!";
  require("info.php"); exit;

?>