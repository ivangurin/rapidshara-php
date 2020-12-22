<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/file.php");

  if(!isset($_GET["id"])){
    header("Location: http://" . $_SERVER["HTTP_HOST"]);exit;
  }

  // Set page
  $gv_page = "links";
  
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
                            
?>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Ссылки на файл <?php echo($go_file->get_id()); ?> &ndash; <?php echo($go_file->get_name()); ?><?php if($go_file->get_description()) echo("&ndash;" . $go_file->get_description()); ?></title>
</head>
<body>
  <table class="main" border="0" cellspacing="0" cellpadding="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle">
      <table border="0" width="35%" cellspacing="0" cellpadding="0">
        <tr>
          <td><b>Файл</b></td>
          <td width="100%"><?php echo($go_file->get_id()) ?></td>
        </tr>
        <tr><td colspan="2" height="10">&nbsp;</td></tr>
        <tr>
          <td><b>Имя</b></td>
          <td><?php echo($go_file->get_name()); ?></td>
        </tr>
        <tr><td colspan="2" height="10">&nbsp;</td></tr>        
        <tr><td><b>Размер</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td><?php echo($go_file->get_size_text_with_bytes()); ?></td>
        </tr>        
        <tr><td colspan="2" height="20">&nbsp;</td></tr>        
        <tr>
          <td colspan="2">Для скачивания</td>
        </tr>
        <tr>
          <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0" style="table-layout: fixed"><tr><td><input type="text" style="width: 100%" value="<?php echo($go_file->get_url()); ?>" readonly="true" onclick="this.select();" /></td></tr></table></td>
        </tr>
        <tr><td colspan="2" height="20">&nbsp;</td></tr>        
        <tr>
          <td colspan="2">Для форума</td>
        </tr>
        <tr>
          <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0" style="table-layout: fixed"><tr><td><input type="text" style="width: 100%" value="[url=<?php echo($go_file->get_url()); ?>]<?php echo($go_file->get_name() . " (" . $go_file->get_size_text() . ") " . " - rapidshara.ru"); ?>[/url]" readonly="true" onclick="this.select();" /></td></tr></table></td>
        </tr>
        <tr><td colspan="2" height="20">&nbsp;</td></tr>       
        <tr><td colspan="2" align="center">
          <?php if($go_user){ ?>
          [ <a class="menu" href="<?php echo($go_file->get_url()); ?>"><b>Скачать</b></a> | <a href="<?php echo($go_file->get_url_change()); ?>"><b>Редактировать</b></a> | <a href="<?php echo($go_file->get_url_delete()); ?>"><b>Удалить</b></a> ]
          <?php }else{ ?>
          [ <a class="menu" href="<?php echo($go_file->get_url()); ?>"><b>Скачать</b></a> ]
          <?php } ?>        
        </td></tr>         
      </table>
    </td></tr>
    <tr align="center"><td><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>