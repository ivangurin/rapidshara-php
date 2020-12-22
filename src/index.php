<?php
require_once("cfg/config.php");
require_once("class/database.php");
require_once("class/configuration.php");
require_once("class/user.php");
require_once("class/ip.php");

// Set page
$gv_page = "start";

// Connect to BD
$go_db = & db::get_instance();
$go_db->connect($gv_db_host, $gv_db_login, $gv_db_pass, $gv_db_name);

// Get configuration
$go_configuration = configuration::get_instance();
$gs_configuration = $go_configuration->get();

// Get user
$go_user_manager = user_manager::get_instance();
$go_user = $go_user_manager->get_by_sid();

if (!$go_user) {
    //header("Location: /entry");
    //exit;
}

// All access for root
if ($go_user && $go_user->is_root())
    $gs_configuration["file_upload"] = true;

// Is upload allowed
if (!$gs_configuration["file_upload"]) {
    $gv_title = $gv_data = $go_configuration->get_comment("file_upload");
    require("info.php");
    exit;
}

// Check is ip blocked
if (!ip::is_upload_allowed()) {
    $gv_title = $gv_data = ip::get_comment();
    require("info.php");
    exit;
}

// Check is ip russian
if ($gs_configuration["only_russians"] && !ip::is_russian()) {
    $gv_title = $gv_data = $go_configuration->get_comment("only_russians");
    require("info.php");
    exit;
}

$gv_size = $gs_configuration["size"];
if ($go_user)
    if ($go_user->get_size() != 0)
        $gv_size = $go_user->get_size();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php if (!$go_user) { ?>
    <html>
        <head>
            <title>Хранилище файлов</title>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <meta name="keywords" content="rapidshare, рапидшара, бесплатный хостинг файлов, free file hosting, хранилище файлов, mp3, wma, wmv, flv, video" />
            <meta name="description" content="Хранилище файлов" />
            <meta name='yandex-verification' content='5d73719fad3b35b2' />
            <link rel="stylesheet" type="text/css" href="/css/styles.css" />
        </head>
        <body>
            <table class="main">
                <tr><td><?php require("menu.php"); ?></td></tr>
                <tr><td height="30%" align="center" valign="middle"><?php require("banner_top.php"); ?></td></tr>
                <tr><td height="40%" align="center" valign="middle"><a class="menu" href="registration" style="font-size: 300%">А ты успел зарегистрироваться?</a> </td></tr>
                <tr><td height="30%" align="center" valign="middle"><?php require("banner_bottom.php"); ?></td></tr>
                <tr><td><?php require("counters.php"); ?></td></tr>
            </table>
        </body>
    </html>
<?php } elseif (!isset($_GET["lite"])) { ?>
    <html>
        <head>
            <title>Хранилище файлов</title>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <meta name="keywords" content="rapidshare, рапидшара, бесплатный хостинг файлов, free file hosting, хранилище файлов, mp3, wma, wmv, flv, video" />
            <meta name="description" content="Хранилище файлов" />
            <meta name="yandex-verification" content="5d73719fad3b35b2" />
            <link rel="stylesheet" type="text/css" href="/css/styles.css" />
            <script type="text/javascript" src="/js/upload/swfupload.js"></script>
            <script type="text/javascript" src="/js/upload/swfupload.speed.js"></script>
            <script type="text/javascript" src="/js/upload/handlers.js"></script>
            <script type="text/javascript">

                var swfu;

                window.onload = function () {

                    swfu = new SWFUpload({

                        // Backend settings
                        upload_url                   : "http://<?php if ($gv_beta)
        echo($gs_configuration['host_upload_beta']); else
        echo($gs_configuration['host_upload']); ?>/upload",
                        file_post_name               : "file",
<?php if ($go_user) { ?>
                                  post_params                  : { "sid" : "<?php echo($go_user->get_sid()); ?>" },
<?php } ?>

                              // Flash Settings
                              flash_url                    : "/js/upload/swfupload.swf",

                              // Flash file settings
                              file_size_limit              : "<?php echo($gv_size); ?> MB",
                              file_types                   : "*.*",
                              file_types_description       : "All Files",
                              file_upload_limit            : "0",
                              file_queue_limit             : "1",

                              // Event handler settings
                              swfupload_loaded_handler     : swfUploadLoaded,

                              file_dialog_start_handler    : fileDialogStart,
                              file_queued_handler          : fileQueued,
                              file_queue_error_handler     : fileQueueError,
                              file_dialog_complete_handler : fileDialogComplete,

                              upload_start_handler         : uploadStart,
                              upload_progress_handler      : uploadProgress,
                              upload_error_handler         : uploadError,
                              upload_success_handler       : uploadSuccess,
                              upload_complete_handler      : uploadComplete,

                              // Button Settings
                              button_placeholder_id        : "button_placeholder",
                              button_image_url             : "/images/button_upload.png",	// Relative to the SWF file
                              button_width                 : 74,
                              button_height                : 22,

                              custom_settings : {
                                  upload_successful          : false
                              },

                              // Debug settings
                              debug                        : false
                          });

                      };
            </script>
        </head>
        <body>
            <table class="main">
                <tr><td><?php require("menu.php"); ?></td></tr>
                <tr><td height="30%" align="center" valign="middle"><?php require("banner_top.php"); ?></td></tr>
                <tr><td height="40%" align="center" valign="middle">
                        <table border="0" width="100%">
                            <tr><td align="center">
                                    <div class="upload" id="upload">
                                        <form id="form_upload" name="form_upload" action="upload" enctype="multipart/form-data" method="post">
                                            <table border="0" width="100%"><tr>
                                                    <td width="100%"><input type="text" id="file_name" style="width: 100%;" readonly="readonly" /></td>
                                                    <td><div id="button_placeholder"></div></td>
                                                    <td><input type="submit" id="btnSubmit" value="Закачать" disabled="true" /></td>
                                                </tr></table>
                                            <input type="hidden" id="file_id" name="file_id" value="" />
                                        </form>
                                    </div>
                                </td></tr>
                            <tr><td height="50" align="center" valign="middle">
                                    <div class="progress" id="progress">Закачано&nbsp;<span id="uploaded">0 Mb</span>&nbsp;из&nbsp;<span id="total">0 Mb</span>&nbsp;(<span id="percent">0 %</span>). Средняя скорость&nbsp;<span id="speed">0 Kb/s</span>. Осталось <span id="time">0 секунд</span>.</div>
                                </td></tr>
                        </table>
                    </td></tr>
                <tr><td height="30%" align="center" valign="middle"><?php require("banner_bottom.php"); ?></td></tr>
                <tr><td><?php require("counters.php"); ?></td></tr>
            </table>
        </body>
    </html>
<?php } else { ?>
    <html>
        <head>
            <title>Хранилище файлов</title>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <link rel="stylesheet" type="text/css" href="/css/styles.css" />
        </head>
        <body>
            <table class="main">
                <tr><td><?php require("menu.php"); ?></td></tr>
                <tr><td height="30%" align="center" valign="middle"><?php require("banner_top.php"); ?></td></tr>
                <tr><td height="40%" align="center" valign="middle">
                        <form name="upload" action="http://<?php if ($gv_beta)
        echo($gs_configuration["host_upload_beta"]); else
        echo($gs_configuration["host_upload"]); ?>/upload" enctype="multipart/form-data" method="post">
                            <table border="0" width="60%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="100%"><input type="file" name="file" id="file" style="width: 100%;" title="Добавить файл" /></td>
                                    <td><input type="button" name="upload" value="Закачать" onclick="submit();"/></td>
                                </tr>
                            </table>
                            <input type="hidden" name="light" value="" />
                        </form>
                    </td></tr>
                <tr><td height="30%" align="center" valign="middle"><?php require("banner_bottom.php"); ?></td></tr>
                <tr><td><?php require("counters.php"); ?></td></tr>
            </table>
        </body>
    </html>
<?php } ?>