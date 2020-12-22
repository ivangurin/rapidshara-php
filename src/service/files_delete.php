<?php

  require_once("../cfg/config.php");
  require_once("../class/database.php");
  require_once("../class/configuration.php");
  require_once("../class/user.php");
  require_once("../class/file.php");

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();

  // Get user manager
  $go_user_manager = user_manager::get_instance();

  // Get file manager
  $go_file_manager = file_manager::get_instance();

  // Get files for delete
  $gt_files = $go_file_manager->get_to_delete();

  if(!$gt_files)
    exit;

  // Delete
  foreach($gt_files as $gv_id => $go_file){
    $go_file->delete();
  }

  // Save
  $go_file_manager->save();

?>