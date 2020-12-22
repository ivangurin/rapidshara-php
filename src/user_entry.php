<?php

  require_once("cfg/config.php");
  require_once("class/database.php");
  require_once("class/configuration.php");
  require_once("class/user.php");
  require_once("class/alert.php");

  // Set page
  $gv_page = "entry";

  // Connect to BD
  $go_db =& db::get_instance();
  $go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  // Get user
  $go_user_manager = user_manager::get_instance();
  $go_user         = $go_user_manager->get_by_sid();

  // If registered then go home
  if($go_user){
    header("Location: http://" . $_SERVER["HTTP_HOST"]);
    exit;
  }

  // Is user entry allowed
  if(!$gs_configuration["user_entry"]){
    $gv_title = $gv_data = $go_configuration->get_comment("user_entry");
    require("info.php"); exit;
  }

  // Alerts
  $go_alert = alert::get_instance();

  $gv_wrong_email    = false;
  $gv_wrong_password = false;

  $gv_action         = "";
  $gv_email          = "";
  $gv_password       = "";

  if(isset($_POST["entry"]))
    $gv_action = "entry";

  if(isset($_POST["email"]))
    $gv_email    = db::escape($_POST["email"]);

  if(isset($_POST["password"]))
    $gv_password = db::escape($_POST["password"]);

  if(!empty($gv_action) && $gv_action != "entry" ){

    $go_alert->add("S", "Попытка входа не через заполнение формы", $gv_email, $gv_password);
    $go_alert->save();

    $gv_title = "Предупреждение";
    $gv_data  = "За вами уже выехали! Ждите!";

    require("info.php");
    exit;

  }

  if($gv_action == "entry"){

    if(empty($gv_email))
      $gv_wrong_email    = true;

    if(empty($gv_password))
      $gv_wrong_password = true;

    if($gv_wrong_email    === false &&
       $gv_wrong_password === false ){

      $gv_result = $go_user_manager->authorize($gv_email, $gv_password);

      if ($gv_result == 0){

        $go_user_manager->save();

        header("Location: http://" . $_SERVER["HTTP_HOST"]);
        exit;

      }

      if ($gv_result == 1){
        $gv_wrong_email    = true;
        $go_alert->add("S", "Не пройдена авторизация на E-mail", $gv_email, $gv_password);
        $go_alert->save();
      }

      if ($gv_result == 2){
        $gv_wrong_password = true;
        $go_alert->add("S", "Не пройдена авторизация на пароль", $gv_email, $gv_password);
        $go_alert->save();
      }

    }

  }

?>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Вход</title>
  <script type="text/javascript" src="/js/user_entry.js"></script>
</head>
<body onload="set_focus(); wrong_email('<?php if($gv_wrong_email) if(empty($gv_email)) echo("X"); else echo($gv_email); ?>'); wrong_password('<?php if($gv_wrong_password) if(empty($gv_password)) echo("X"); else echo($gv_password); ?>'); check();">
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle">
      <form name="entry" method="post">
        <input type="hidden" name="Action" value="entry">
        <table border="0" width="40%" cellpadding="0" cellspacing="0">
          <tr><td width="70"></td><td><p style="font-size: 200%">Вход</p></td></tr>
          <tr><td height="25"></td><td></td></tr>
          <tr valign="middle"><td>&nbsp;Почта&nbsp;</td><td><input type="text" id="email" name="email" style="width: 100%" value="<?php echo($gv_email); ?>" onkeypress="check_email()" onkeyup="check_email()" onblur="check_email()"></td></tr>
          <tr valign="top"><td height="25"></td><td><div id="email_info"></div></td></tr>
          <tr valign="middle"><td>&nbsp;Пароль&nbsp;</td><td><input type="password" id="password" name="password" style="width: 100%" value="<?php echo($gv_password); ?>" onkeypress="check_password()" onkeyup="check_password()" onblur="check_password()"></td></tr>
          <tr valign="top"><td height="25"></td><td><div id="password_info"></div></td></tr>
          <tr><td></td><td><input type="submit" name="entry" value="Войти" disabled="disabled"></td></tr>
          <tr><td height="25"></td><td></td></tr>
          <tr><td colspan="2"><p>&nbsp;<a class="menu" href="http://<?php echo($_SERVER["HTTP_HOST"]) ?>/registration">Регистрация</a>&nbsp;</p></td></tr>
        </table>
      </form>
    </td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>