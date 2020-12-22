<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/host.php");
  require_once("class/file.php");
  require_once("class/alert.php");

  // Set page
  $gv_page = "upload";

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get file by id and go to links
  if(isset($_POST["file_id"])){
    $gv_id = db::escape($_POST["file_id"]);
    $go_file_manager = file_manager::get_instance();
    $go_file = $go_file_manager->get_by_id($gv_id);
    if($go_file)
      header("Location: http://" . $gs_configuration["host"] . "/" . $go_file->get_id() . "/links");
    else
      header("Location: http://" . $gs_configuration["host"]);
    exit;
  }

  // Check for files
  if(!isset($_FILES) || count($_FILES) == 0 ){
    header("Location: http://" . $gs_configuration["host"]); exit;
  }

  // Set SID
  if(isset($_POST["sid"]))
    $_COOKIE["SID"] = db::escape($_POST["sid"]);

  // Get user
  $go_user_manager = user_manager::get_instance();
  $go_user         = $go_user_manager->get_by_sid();

  if(!$go_user){
    $gv_title = $gv_data = "Какой не хороший! За вами уже выехали!";
    require("info.php"); exit;
  }

  // All access for root
  if($go_user && $go_user->is_root())
    $gs_configuration["file_upload"] = true;

  // Is upload allowed
  if(!$gs_configuration["file_upload"]){
    $gv_title = $gv_data = $go_configuration->get_comment("file_upload");
    require("info.php"); exit;
  }

  // Get object alert
  $go_alert = alert::get_instance();

  // Get host
  $go_host_manager = host_manager::get_instance();
  $go_host         = $go_host_manager->get();

  // Check for host
  if(!$go_host){
    $go_alert->add("E", "Не найден принимающий сервер", $gv_page, $_SERVER["HTTP_HOST"]);
    $gv_title = $gv_data = "Ошибка принимающего сервера! Оповестите, пожалуйста, об этом <a href='mailto:info@rapidshara.ru'>НЛО</a>!";
    require("info.php"); exit;
  }

  // Get files
  $gt_files = $_FILES;

  // Cretae file manager
  $go_file_manager = file_manager::get_instance();

  foreach($gt_files as $gv_index => $gs_file){

    switch($gs_file["error"]){
      case "0":

        // Check file size
        if(filesize($gs_file["tmp_name"]) == 0){
          $gt_files[$gv_index]["error"]      = true;
          $gt_files[$gv_index]["error_text"] = "Размер файла равен 0";
          break;
        }

        // Create file
        $lo_file = $go_file_manager->create();

        $ls_file = $lo_file->get();
        $ls_file["Host"]                 = $go_host->get_name();
        $ls_file["Path"]                 = $go_host->get_path();
        $ls_file["Type"]                 = $gs_file["type"];
        $ls_file["Name"]                 = $gs_file["name"];
        $ls_file["Size"]                 = $gs_file["size"];

        if($go_user)
          $ls_file["UserID"]             = $go_user->get_id();

        if(empty($ls_file["Type"]))
          $ls_file["Type"]               = "application/octet-stream";

        $lo_file->set($ls_file);

        $lo_file->set_path_tmp($gs_file["tmp_name"]);

        $lo_file->save($gs_file["tmp_name"]);

        if(isset($_POST["light"])){
          header("Location: " . $lo_file->get_url_links($go_configuration->get_host())); exit;
        }else{
          echo($lo_file->get_id()); exit;
        }

        $gt_files[$gv_index]["error"]     = false;
        $gt_files[$gv_index]["object"]    = $lo_file;

        break;

      case "1":
        $gt_files[$gv_index]["error"]      = true;
        $gt_files[$gv_index]["error_text"] = "Превышен максимальный размер файла";
        break;
      case "2":
        $gt_files[$gv_index]["error"]      = true;
        $gt_files[$gv_index]["error_text"] = "Превышен максимальный размер файла";
        break;
      case "3":
        $gt_files[$gv_index]["error"]      = true;
        $gt_files[$gv_index]["error_text"] = "Файл был закачен с ошибками";
        break;
      case "4":
        $gt_files[$gv_index]["error"]      = true;
        $gt_files[$gv_index]["error_text"] = "Не указан файл для загрузки";
        break;
    }
  }

  // Save alerts
  $go_alert->save();

?>