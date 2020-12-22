<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/file.php");

  // Set page
  $gv_page = "links";
  
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
    $gs_configuration["file_change"] = true;

  // Check for change allowed
  if(!$gs_configuration["file_change"]){
    $gv_title = $gv_data = $go_configuration->get_comment("file_change");
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
  
  // If deleted
  if($go_file->is_deleted()){
    $gv_title = $gv_data = "Прилетало НЛО и удалило этот файл!";
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
  
  // Get action
  $gv_action = "";
  if(isset($_POST["change"]))
    $gv_action = "change";
  elseif(isset($_POST["delete"]))
    $gv_action = "delete";       
  
  // To change
  if($gv_action == "change"){   

    $ls_file = $go_file->get();
    
    if(!empty($_POST["name"]))
      $ls_file["Name"]      = db::escape($_POST["name"]);
    
    $ls_file["Description"] = db::escape($_POST["description"]);
    $ls_file["Mirrors"]     = db::escape($_POST["mirrors"]);
    $ls_file["Password"]    = db::escape($_POST["password"]);    
    
    $go_file->set($ls_file);

    $go_file->save();
    
    header("Location: " . $go_file->get_url_change());
    exit;
  
  }

  // To delete
  if($gv_action == "delete"){
    header("Location: " . $go_file->get_url_delete());
    exit;
  }

?>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Изменение файла <?php echo($go_file->get_id()); ?> &ndash; <?php echo($go_file->get_name()); ?><?php if($go_file->get_description()) echo("&ndash;" . $go_file->get_description()); ?></title>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr height="100%"><td align="center" valign="middle">
      <form name="change" method="post">
        <table border="0" width="50%" cellpadding="0" cellspacing="0">
          <tr>
            <td>&nbsp;</td>
            <td><p style="font-size: 200%">Файл № <?php echo($go_file->get_id()) ?> [<a href="<?php echo($go_file->get_url_links()); ?>">cсылки</a>]</p></td>
          </tr>        
          <tr><td height="20" colspan="2">&nbsp;</td></tr>
          <tr>
            <td>&nbsp;Размер&nbsp;</td>
            <td><?php echo($go_file->get_size_text_with_bytes()) ?></td>
          </tr>          
          <tr><td height="20" colspan="2">&nbsp;</td></tr>
          <tr>
            <td>&nbsp;Имя&nbsp;</td>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0" style="table-layout: fixed"><tr><td><input type="text" name="name" style="width: 100%" value="<?php echo($go_file->get_name()) ?>" /></td></tr></table></td>
          </tr>
          <tr><td height="20" colspan="2">&nbsp;</td></tr>
          <tr valign="top">
            <td height="50"><p>&nbsp;Описание&nbsp;</p></td>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0" style="table-layout: fixed"><tr><td><textarea name="description" style="width: 100%; height=50"><?php echo($go_file->get_description()) ?></textarea></td></tr></table></td>
          </tr>
          <tr><td height="20" colspan="2">&nbsp;</td></tr>
          <tr valign="top">
            <td>&nbsp;Зеркала&nbsp;</td>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0" style="table-layout: fixed"><tr><td><textarea name="mirrors" style="width: 100%; height=50"><?php echo($go_file->get_mirrors()) ?></textarea></td></tr></table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><p style="color: #666666">Пример заполнения: http://site1.ru/file.rar<b>,</b> http://site2.ru/file.rar</p></td>
          </tr>
          <tr><td height="20" colspan="2">&nbsp;</td></tr>
          <tr>
            <td>&nbsp;Пароль&nbsp;</td>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0" style="table-layout: fixed"><tr><td><input type="password" name="password" maxlength="20" style="width: 100%" value="<?php echo($go_file->get_password()) ?>" /></td></tr></table></td>
          </tr>
          <tr><td height="20" colspan="2">&nbsp;</td></tr>
          <tr><td height="20" colspan="2">&nbsp;</td></tr>
          <tr>
            <td>&nbsp;</td>
            <td>
              <table border="0" width="100%" cellpadding="0" cellspacing="0"><tr>
                <td width="50%"><input type="submit" name="change" value="Изменить" style="width: 100;"></td>
                <td width="50%" align="right"><input type="submit" name="delete" value="Удалить" style="width: 100;"></td>
              </tr></table>
            </td>
          </tr>
        </table>
      </form>               
    </td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>
