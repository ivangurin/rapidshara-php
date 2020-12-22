<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/alert.php");

  // Set page
  $gv_page = "profile";

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get user
  $go_user_manager = user_manager::get_instance();
  $go_user         = $go_user_manager->get_by_sid();

  // Если пользователь не зарегистрирован, то кыш
  if(!$go_user){
    header("Location: http://" . $_SERVER["HTTP_HOST"]);
    exit;
  }

  // All access for root
  if($go_user && $go_user->is_root())
    $gs_configuration["user_change"] = true;

  // Is user change allowed
  if(!$gs_configuration["user_change"]){
    $gv_title = $gv_data = $go_configuration->get_comment("user_change");
    require("info.php"); exit;
  }

  // Alerts
  $go_alert = alert::get_instance();

  if(isset($_GET["id"])){
    if($go_user->is_root()){
      $gv_id = db::escape($_GET["id"]);
      $go_user = $go_user_manager->get_by_id($gv_id);
      if($go_user){
        $gs_user = $go_user->get();
      }else{
        $gv_title = $gv_data = "Личное дело № $gv_id не найдено";
        require("info.php");
        exit;
      }
    }else{
      header("Location: http://" . $_SERVER["HTTP_HOST"] . "/profile");
      exit;
    }
  }

  $gv_wrong_name          = false;
  $gv_wrong_email         = false;
  $gv_wrong_password      = false;
  $gv_wrong_size          = true;

  $gv_action              = "";
  $gv_name                = "";
  $gv_email               = "";
  $gv_password            = "";

  $gv_name                = $go_user->get_name();
  $gv_email               = $go_user->get_email();
  $gv_password            = $go_user->get_password();


  if(isset($_POST["action"]))
    $gv_action            = db::escape($_POST["action"]);

  if(!empty($gv_action) && $gv_action != "change" ){

    $go_alert->add("S", "Попытка изменения профиля не через форму", $go_user->gs_user["ID"], $gv_name, $gv_email, $gv_password);
    $go_alert->save();

    $gv_title = "Предупреждение";
    $gv_data  = "За вами уже выехали! Ждите!";

    require("page_info.php");
    exit;

  }

  if($gv_action == "change"){

    if(isset($_POST["Name"]))
      $gv_name                = db::escape($_POST["Name"]);

    if(isset($_POST["Email"]))
      $gv_email               = db::escape($_POST["Email"]);

    if(isset($_POST["Password"]))
      $gv_password            = db::escape($_POST["Password"]);

    if(empty($gv_name))
      $gv_wrong_name     = true;

    if(empty($gv_email))
      $gv_wrong_email    = true;

    if(empty($gv_password))
      $gv_wrong_password = true;

    if($gv_wrong_email === false)
      if($go_user->get_email() <> $gv_email)
        $gv_wrong_email = user_service::check_email($gv_email);

    if($gv_wrong_name     === false &&
       $gv_wrong_email    === false &&
       $gv_wrong_password === false){

      $ls_user = $go_user->get();

      $ls_user["Name"]                = $gv_name;
      $ls_user["Email"]               = $gv_email;
      $ls_user["Password"]            = $gv_password;

      $go_user->set($ls_user);

      $go_user->save();

      header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
      exit;

    }

  }

?>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Личное дело № <?php echo($go_user->get_id()) ?></title>
  <script type="text/javascript" src="/js/user_change.js"></script>
</head>
<body onload="wrong_email('<?php if($gv_wrong_email) echo($gv_email); ?>'); check();">
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle">
      <form name="Profile" method="post">
        <input type="hidden" name="action" value="change">
        <table border="0" width="40%" cellpadding="0" cellspacing="0">
          <tr>
            <td></td>
            <td width="100%"><p style="font-size: 200%">Личное дело № <?php echo($go_user->get_id()) ?></p></td>
          </tr>
          <tr><td height="25"></td><td></td></tr>
          <tr valign="middle">
            <td><p>&nbsp;Имя&nbsp;</p></td>
            <td><input type="text" name="Name" id="Name" maxlength="100" style="width: 100%" value="<?php echo($gv_name) ?>" onkeypress="check_name()" onkeyup="check_name()" onblur="check_name()"></td>
          </tr>
          <tr valign="top"><td height="25"></td><td><div id="NameInfo"></div></td></tr>
          <tr valign="middle">
            <td><p>&nbsp;Почта&nbsp;</p></td>
            <td><input type="text" name="Email" id="Email" maxlength="50" style="width: 100%" value="<?php echo($gv_email)?>" onkeypress="check_email()" onkeyup="check_email()" onblur="check_email()"></td>
          </tr>
          <tr valign="top"><td height="25"></td><td><div id="EmailInfo"></div></td></tr>
          <tr valign="middle">
            <td><p>&nbsp;Пароль&nbsp;</p></td>
            <td><input type="password" name="Password" id="Password" maxlength="50" style="width: 100%" value="<?php echo($gv_password) ?>" onkeypress="check_password()" onkeyup="check_password()" onblur="check_password()"></td>
          </tr>
          <tr height="25" valign="top"><td></td><td><div id="PasswordInfo"></div></td></tr>
          <tr>
            <td></td>
            <td><input type="submit" name="Submit" value="Сохранить" style="width: 100;" disabled="disabled"></td>
          </tr>
        </table>
      </form>
    </td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>