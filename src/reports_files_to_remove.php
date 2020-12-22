<?php

  // Get files to delete
  $go_file_manager = file_manager::get_instance();
  $gt_files = $go_file_manager->get_to_remove();

?>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Удаленные файлы</title>
  <script type="text/javascript" src="/js/navigate.js"></script>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle">
    <?php if(!$gt_files){ ?>
      Нет файлов к удалению
    <?php } else { ?>
      <table class="report" width="70%" border="1" cellspacing="0" cellpadding="0">
        <tr height="30" align="center" valign="middle">
          <td>&nbsp;<b>Юзер</b>&nbsp;</td>
          <td>&nbsp;<b>Номер</b>&nbsp;</td>
          <td width="100%">&nbsp;<b>Имя</b>&nbsp;</td>
          <td>&nbsp;<b>Размер</b>&nbsp;</td>
          <td>&nbsp;<b>Скачан,</b>&nbsp; &nbsp;<b>раз</b>&nbsp;</td>
          <td>&nbsp;<b>Дата D</b>&nbsp;</td>
          <td>&nbsp;<b>Действие</b>&nbsp;</td>
        </tr>
        <?php foreach($gt_files as $lv_id => $lo_file) { ?>
        <tr height="25" valign="middle">
          <td align="center">&nbsp;<?php echo($lo_file->get_user_id()); ?>&nbsp;</td>
          <td align="center">&nbsp;<a href="<?php echo($lo_file->get_url()); ?>"><?php echo($lo_file->get_id()); ?></a>&nbsp;</td>
          <td>&nbsp;<?php echo($lo_file->get_name()); ?>&nbsp;</td>
          <td align="right">&nbsp;<?php echo($lo_file->get_size_text()); ?>&nbsp;</td>
          <td align="center">&nbsp;<?php echo($lo_file->get_counter()); ?>&nbsp;</td>
          <td align="center"><nobr>&nbsp;<?php echo($lo_file->get_date_deleted()); ?>&nbsp;</nobr></td>
          <td align="center"><nobr>&nbsp;<a href="<?php echo($lo_file->get_url_links()); ?>">L</a>&nbsp;|&nbsp;<a href="<?php echo($lo_file->get_url_change()); ?>">E</a>&nbsp;|&nbsp;<a href="<?php echo($lo_file->get_url_delete()); ?>">D</a>&nbsp;|&nbsp;<a href="<?php echo($lo_file->get_url_remove()); ?>">R</a>&nbsp;</nobr></td>
        </tr>
        <?php } ?>
      </table>
    </td></tr>
    <?php } ?>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>