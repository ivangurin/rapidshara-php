<?php
require_once("cfg/config.php");
require_once("class/database.php");
require_once("class/configuration.php");
require_once("class/user.php");
require_once("class/alert.php");

// Set page
$gv_page = "registration";

// Connect to BD
$go_db = & db::get_instance();
$go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

// Get configuration
$go_configuration = configuration::get_instance();
$gs_configuration = $go_configuration->get();

// Get user
$go_user_manager = user_manager::get_instance();
$go_user = $go_user_manager->get_by_sid();

// If registered then go home
if ($go_user) {
    header("Location: http://" . $_SERVER["HTTP_HOST"]);
    exit;
}

// Is user create allowed
if (!$gs_configuration["user_registration"]) {
    $gv_title = $gv_data = $go_configuration->get_comment("user_registration");
    require("info.php");
    exit;
}

// Alerts
$go_alert = alert::get_instance();

$gv_wrong_name = false;
$gv_wrong_email = false;
$gv_wrong_password = false;
$gv_wrong_invite = false;

$gv_action = "";
$gv_name = "";
$gv_email = "";
$gv_password = "";
$gv_invite = "";

if (isset($_POST["create"]))
    $gv_action = "create";

if (isset($_POST["name"]))
    $gv_name = db::escape($_POST["name"]);

if (isset($_POST["email"]))
    $gv_email = db::escape($_POST["email"]);

if (isset($_POST["password"]))
    $gv_password = db::escape($_POST["password"]);

if (isset($_POST["invite"]))
    $gv_invite = db::escape($_POST["invite"]);

if (!empty($gv_action) && $gv_action != "create") {

    $go_alert->add("S", "Попытка регистрации не через заполнение формы", $gv_name, $gv_email, $gv_password);
    $go_alert->save();

    $gv_title = "Предупреждение";
    $gv_data = "За вами уже выехали! Ждите!";

    require("page_info.php");
    exit;
}

if ($gv_action == "create") {

    if (empty($gv_name))
        $gv_wrong_name = true;

    if (empty($gv_email))
        $gv_wrong_email = true;

    if (empty($gv_password))
        $gv_wrong_password = true;

    $gv_wrong_invite = true;
    if ($gv_invite == "12345")
        $gv_wrong_invite = false;

    if ($gv_wrong_email === false)
        $gv_wrong_email = user_service::check_email($gv_email);

    if ($gv_wrong_name === false &&
            $gv_wrong_email === false &&
            $gv_wrong_password === false &&
            $gv_wrong_invite === false) {

        $go_user = $go_user_manager->create();

        $gs_user = $go_user->get();
        $gs_user["Name"] = $gv_name;
        $gs_user["Email"] = $gv_email;
        $gs_user["Password"] = $gv_password;
        $go_user->set($gs_user);

        $go_user->login();

        $go_user->save();

        header("Location: http://" . $_SERVER["HTTP_HOST"]);
        exit;
    }
}
?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="/css/styles.css" />
        <script type="text/javascript" src="/js/user_create.js"></script>
        <title>Регистрация</title>
    </head>
    <body onload="set_focus(); wrong_name('<?php if ($gv_wrong_name)
    echo('X'); ?>'); wrong_email('<?php
    if ($gv_wrong_email)
        if (empty($gv_email))
            echo("X"); else
            echo($gv_email); ?>'); wrong_password('<?php if ($gv_wrong_password)
        echo('X'); ?>'); wrong_invite('<?php
    if ($gv_wrong_invite)
        if (empty($gv_invite))
            echo("X"); else
            echo($gv_invite);
?>'); check();">
        <table class="main" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php require("menu.php"); ?></td></tr>
            <tr><td height="100%" align="center" valign="middle">
                    <form name="registration" method="post">
                        <table border="0" width="40%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="70"></td>
                                <td><p style="font-size: 200%">Регистрация</p></td>
                            </tr>
                            <tr><td height="25"></td><td></td></tr>
                            <tr valign="middle">
                                <td>&nbsp;Имя&nbsp;</td>
                                <td><input type="text" name="name" id="name" style="width: 100%" value="<?php echo($gv_name); ?>" onkeypress="check_name()" onkeyup="check_name()" onblur="check_name()"></td>
                            </tr>
                            <tr valign="top"><td height="25"></td><td><div id="name_info"></div></td></tr>
                            <tr valign="middle">
                                <td>&nbsp;Почта&nbsp;</td>
                                <td><input type="text" name="email" id="email" style="width: 100%" value="<?php echo($gv_email); ?>" onkeypress="check_email()" onkeyup="check_email()" onblur="check_email()"></td>
                            </tr>
                            <tr valign="top"><td height="25"></td><td><div id="email_info"></div></td></tr>
                            <tr valign="middle">
                                <td>&nbsp;Пароль&nbsp;</td>
                                <td><input type="password" name="password" id="password" style="width: 100%" value="<?php echo($gv_password); ?>" onkeypress="check_password()" onkeyup="check_password()" onblur="check_password()"></td>
                            </tr>
                            <tr valign="top"><td height="25"></td><td><div id="password_info"></div></td></tr>
                            <tr valign="middle">
                                <td>&nbsp;Инвайт&nbsp;</td>
                                <td><input type="text" name="invite" id="invite" style="width: 100%" value="<?php echo($gv_invite); ?>" onkeypress="check_invite()" onkeyup="check_invite()" onblur="check_invite()"></td>
                            </tr>
                            <tr valign="top"><td height="25"></td><td><div id="invite_info"></div></td></tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" name="create" value="Войти" disabled="disabled"></td>
                            </tr>
                        </table>
                    </form>
                </td></tr>
            <tr><td align="center"><?php require("counters.php"); ?></td></tr>
        </table>
    </body>
</html>