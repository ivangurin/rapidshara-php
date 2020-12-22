<?php

  require_once("../cfg/config.php");
  require_once("../class/database.php");
  require_once("../class/configuration.php");
  require_once("../class/reference.php");
  require_once("../class/host.php");

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get reference
  $go_reference_manager = reference_manager::get_instance();
  $go_reference_manager->delete();

?>
