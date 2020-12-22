<?php
  if(!isset($gv_page)){
  	header("Location: " . $_SERVER["HTTP_HOST"]);
    exit;
  }
?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
  <tr align="center" valign="middle">
    <?php if($go_user) { ?>
    <td width="140">&nbsp;Привет,&nbsp;<a class="menu" href="/profile"><?php echo($go_user->get_name()) ?></a>!&nbsp;</td>
    <?php if ($gv_page != "files") { ?>
    <td width="100">&nbsp;<a class="menu" href="/files/">Файлы</a>&nbsp;</td>
    <?php } ?>
    <?php if ($gv_page == "files") { ?>
    <td class="ActivePage" width="100">&nbsp;Файлы&nbsp;</td>
    <?php } ?>
    <?php if ($go_user->is_root() and $gv_page != "reports") { ?>
    <td width="100">&nbsp;<a class="menu" href="/reports/">Отчеты</a>&nbsp;</td>
    <?php } ?>
    <?php if ($go_user->is_root() and $gv_page == "reports") { ?>
    <td class="ActivePage" width="100">&nbsp;Отчеты&nbsp;</td>
    <?php } ?>
    <?php if ($go_user->is_root() and $gv_page != "customize") { ?>
    <td width="100">&nbsp;<a class="menu" href="/customize">Настройки</a>&nbsp;</td>
    <?php } ?>
    <?php if ($go_user->is_root() and $gv_page == "customize") { ?>
    <td class="ActivePage" width="100">&nbsp;Настройки&nbsp;</td>
    <?php } ?>
    <?php } ?>
    <?php if ($gv_page != "start") { ?>
    <td width="100">&nbsp;<a class="menu" href="/">Закачать!</a>&nbsp;</td>
    <?php } ?>
    <td height="30">&nbsp;</td>
    <?php if ($gv_page != "rules") { ?>
    <td width="100">&nbsp;<a class="menu" href="/rules">Правила</a>&nbsp;</td>
    <?php } ?>
    <?php if ($gv_page == "rules") { ?>
    <td class="ActivePage" width="100">&nbsp;Правила&nbsp;</td>
    <?php } ?>
    <?php if (!$go_user && $gv_page != "entry") { ?>
      <td width="100">&nbsp;<a class="menu" href="/entry">Войти</a>&nbsp;</td>
    <?php } ?>
    <?php if (!$go_user && $gv_page == "entry") { ?>
      <td class="ActivePage" width="100">&nbsp;Войти&nbsp;</td>
    <?php } ?>
    <?php if ($go_user) { ?>
    <td width="100">&nbsp;<a class="exit" id="exit" href="/exit">Выйти</a>&nbsp;</td>
    <?php } ?>
  </tr>
</table>
<?php if ($gv_page == "files") { ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr align="center" valign="middle">
    <td width="90" height="30">&nbsp;</td>
    <?php if($gv_report == "active") { ?>
    <td class="ActivePage" width="100">&nbsp;Активные&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="active">Активные</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_report == "deleted") { ?>
    <td class="ActivePage" width="100">&nbsp;Удаленные&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="deleted">Удаленные</a>&nbsp;</td>
    <?php } ?>
    <td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
<?php if ($gv_page == "reports") { ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr align="center" valign="middle">
    <td width="140" height="30">&nbsp;</td>
    <?php if($gv_section == "users") { ?>
    <td class="ActivePage" width="100">&nbsp;Пользователи&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="/reports/users">Пользователи</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_section == "files") { ?>
    <td class="ActivePage" width="100">&nbsp;Файлы&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="/reports/files/">Файлы</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_section == "connections") { ?>
    <td class="ActivePage" width="100">&nbsp;Соединения&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="/reports/connections">Соединения</a>&nbsp;</td>
    <?php } ?>
    <td>&nbsp;</td>
  </tr>
</table>
<?php if ($gv_section == "files") { ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr align="center" valign="middle">
    <td width="40" height="30">&nbsp;</td>
    <?php if($gv_report == "uploaded") { ?>
    <td class="ActivePage" width="100">&nbsp;Закаченные&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="uploaded">Закаченные</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_report == "downloaded") { ?>
    <td class="ActivePage" width="100">&nbsp;Скаченные&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="downloaded">Скаченные</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_report == "not_downloaded") { ?>
    <td class="ActivePage" width="100">&nbsp;Не скаченные&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="not_downloaded">Не скаченные</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_report == "to_delete") { ?>
    <td class="ActivePage" width="100">&nbsp;К удалению&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="to_delete">К удалению</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_report == "deleted") { ?>
    <td class="ActivePage" width="100">&nbsp;Удаленные&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="deleted">Удаленные</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_report == "to_remove") { ?>
    <td class="ActivePage" width="100">&nbsp;К стиранию&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="to_remove">К стиранию</a>&nbsp;</td>
    <?php } ?>
    <?php if($gv_report == "removed") { ?>
    <td class="ActivePage" width="100">&nbsp;Стертые&nbsp;</td>
    <?php } else { ?>
    <td width="100">&nbsp;<a class="menu" href="removed">Стертые</a>&nbsp;</td>
    <?php } ?>
    <td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
<?php } ?>