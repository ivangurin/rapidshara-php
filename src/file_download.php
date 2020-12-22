 <?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/file.php");
  require_once("class/reference.php");
  require_once("class/ip.php");
  require_once("class/host.php");
  
  // Set page
  $gv_page = "download";

  // If id not exist
  if(!isset($_GET["id"])){
    header("Location: http://" . $_SERVER["HTTP_HOST"]);exit;
  }

  // Get id
  $gv_id = db::escape($_GET["id"]);

  // IFolder point
  //if($gv_id > 200000){
//    header("Location: http://rapidshara.ifolder.ru/" . $gv_id);
//  }

  // Get name
  $gv_name = "";
  if(isset($_GET["name"]))
    $gv_name = db::escape($_GET["name"]);

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);
  
  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get user
  $go_user_manager = user_manager::get_instance();
  $go_user         = $go_user_manager->get_by_sid();

  // All access for root
  if($go_user && $go_user->is_root())
    $gs_configuration["file_download"] = true;

  // Is downlaod allowed
  if(!$gs_configuration["file_download"]){
    $gv_title = $gv_data = $go_configuration->get_comment("file_download");
    require("info.php"); exit;
  }  

  // If ip is blocked
  if(!ip::is_download_allowed()){
    $gv_title = $gv_data = ip::get_comment();
    require("info.php"); exit;
  }

  // If ip is russian  
  if($gs_configuration["only_russians"] && !ip::is_russian()){
    $gv_title = $gv_data = $go_configuration->get_comment("only_russians");
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
  if(!$go_user || ($go_user && !$go_user->is_root())){
    if($go_file->is_deleted()){
      $gv_title = $gv_data = "Прилетало НЛО и удалило этот файл!";
      require("info.php"); exit;
    }
  }

  // If removed
  if($go_file->is_removed()){
    $gv_title = $gv_data = "Прилетало НЛО и удалило этот файл!";
    require("info.php"); exit;
  }

  // If blocked
  if($go_file->is_blocked()){
    $gv_title = $gv_data = "НЛО заблокировало скачивание этого файла!";
    require("info.php"); exit;
  }

  // Get reference
  $go_reference_manager = reference_manager::get_instance();
  $go_reference = $go_reference_manager->get_by_file_id($go_file->get_id());

  // If reference exist
  if($go_reference){

    // If blocked
    if($go_reference->is_blocked()){
      $gv_title = $gv_data = "НЛО заблокировало для вас скачивание этого файла!";
      require("info.php"); exit;
    }

    // Go to download file
    if(isset($_POST["download"])){

      header("Location: " . $go_reference->get_url());
      exit;

    }else{

      // Increase counter and show downlad button
      if(empty($gv_name)){

        $go_reference->increase_counter();
        $go_reference->save();

      // Start download
      }else{

        $go_file->download();

        exit;

      }

    }

  }else{

    if(!empty($gv_name)){
      header("Location: http://" . $go_configuration->get_host() . "/" . $go_file->get_id());
      exit;
    }

    // If file is protrected
    if($go_file->is_protected()){

      if(isset($_POST["check_password"])){

        $gv_password = "";
        if(isset($_POST["password"]))
          $gv_password = $_POST["password"];

        if($gv_password == $go_file->get_password()){

          $go_reference = $go_reference_manager->create();
          $go_reference->set_file_id($go_file->get_id());
          $go_reference_manager->save();

          header("Location: " . $go_file->get_url());
          exit;

        }else{
          $gv_title = $gv_data = "Пароль не тот";
          require("info.php"); exit;
        }

      }

    // If file is for sell
    }elseif($go_file->is_for_sell()){



    // Create reference and go
    }else{

      $go_reference = $go_reference_manager->create();
      $go_reference->set_file_id($go_file->get_id());
      $go_reference->increase_counter();
      $go_reference_manager->save();

    }

  }

?>

<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Файл <?php echo($go_file->get_id()); ?> - <?php echo($go_file->get_name()); ?></title>
</head>
<body>
  <table class="main" border="0" cellspacing="0" cellpadding="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr height="30%" align="center" valign="middle"><td><?php require("banner_top.php"); ?></td></tr>
    <tr height="40%"><td align="center" valign="middle">
      <table border="0" width="60%" cellspacing="0" cellpadding="0">
        <tr><td align="center">
          <table class="dl" border="0" cellspacing="0" cellpadding="0">
            <tr><td class="dl_left"><b>Имя</b>&nbsp;</td><td class = "dl_right">&nbsp;<?php echo($go_file->get_name()); ?></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td class="dl_left"><b>Размер</b>&nbsp;</td><td class = "dl_right">&nbsp;<?php echo($go_file->get_size_text_with_bytes()); ?></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <?php if($go_file->is_descriptioned()){ ?>
            <tr><td class="dl_left"><b>Описание</b&nbsp;></td><td class = "dl_right">&nbsp;<?php echo($go_file->get_description()); ?></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <?php } ?>
            <tr><td class="dl_left"><b>Скачан</b>&nbsp;</td><td class = "dl_right">&nbsp;<?php echo($go_file->get_counter()); ?> раз(а)</td></tr>
          </table>
        </td></tr>
        <?php if($go_reference){ ?>
          <?php if($go_file->get_extension() == "mp3"){ ?>
          <tr><td>&nbsp;</td></tr>
          <tr><td align="center"><embed src="/js/player/player.swf"width="400"height="20"allowscriptaccess="always"allowfullscreen="true"flashvars="height=20&width=400&file=<?php echo($go_reference->get_url()); ?>&showstop=true&usefullscreen=false"/></td></tr>
          <?php } ?>
          <?php if($go_file->get_extension() == "flv" || $go_file->get_extension() == "mp4") { ?>
          <tr><td>&nbsp;</td></tr>
          <tr><td align="center"><embed src="/js/player/player.swf"width="400"height="320"allowscriptaccess="always"allowfullscreen="true"flashvars="height=320&width=400&file=<?php echo($go_reference->get_url()); ?>&searchbar=false&showstop=true&usefullscreen=false"/></td></tr>
          <?php } ?>
          <?php if($go_file->get_extension() == "wma") { ?>
          <tr><td>&nbsp;</td></tr>
          <tr><td align="center">
            <script type='text/javascript' src="/js/player/silverlight.js"></script>
            <script type='text/javascript' src="/js/player/wmvplayer.js"></script>
            <div id="container"></div>
            <script type="text/javascript"> var cnt = document.getElementById("container"); var src = '/js/player/wmvplayer.xaml'; var cfg = {  height:'20',  width:'400',  file:'<?php echo($go_reference->get_url()); ?>',  showstop:'true',  usefullscreen:'false' }; var ply = new jeroenwijering.Player(cnt,src,cfg);</script>
          </td></tr>
          <?php } ?>
          <?php if($go_file->get_extension() == "wmv" ) { ?>
          <tr><td>&nbsp;</td></tr>
          <tr><td align="center">
            <script type='text/javascript' src="/js/player/silverlight.js">
            </script><script type='text/javascript' src="/js/player/wmvplayer.js"></script>
            <div id="container"></div>
            <script type="text/javascript"> var cnt = document.getElementById("container"); var src = '/js/player/wmvplayer.xaml'; var cfg = {  height:'220',  width:'400',  file:'<?php echo($go_reference->get_url()); ?>',  showstop:'true',  usefullscreen:'true' }; var ply = new jeroenwijering.Player(cnt,src,cfg);</script>
          </td></tr>
          <?php } ?>
          <tr><td>&nbsp;</td></tr>
          <tr><td align="center"><form name="download" method="post"><input type="submit" name="download" value="Скачать" /></form></td></tr>
        <?php }else{ ?>
          <?php if($go_file->is_protected()){ ?>
          <tr><td>&nbsp;</td></tr>
          <tr><td><form name="password" method="post">
          <table class="dl" border="0" cellspacing="0" cellpadding="0">
            <tr><td class="dl_left_p"><b>Пароль</b></td><td class="dl_right"><input type="password" name="password" /></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2" align="center"><input type="submit" name="check_password" value="Ввести" /></td></tr>
          </table>
          </form></td></tr>
          <?php }elseif($go_file->is_for_sell()){ ?>
          <tr><td>&nbsp;</td></tr>
          <tr><td><form name="sell" method="post">
          <table class="dl" border="0" cellspacing="0" cellpadding="0">
            <tr><td class="dl_left_p"><b>Код</b></td><td class="dl_right"><input type="code" name="password" /></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr><td colspan="2" align="center"><input type="submit" name="check_code" value="Ввести" /></td></tr>
          </table>
          </form></td></tr>
          <?php } ?>
        <?php } ?>
      </table>
    </td></tr>
    <tr height="30%" align="center" valign="middle"><td align="center"><?php require("banner_bottom.php"); ?></td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>