<?php

  // Get deleted file
  $go_file_manager = file_manager::get_instance();

  $gt_files_deleted = $go_file_manager->get_by_user_id($go_user->get_id(), 1);
  $gt_files_removed = $go_file_manager->get_by_user_id($go_user->get_id(), 2);

  $gt_files = array();

  if(is_array($gt_files_deleted))
    $gt_files += $gt_files_deleted;

  if(is_array($gt_files_removed))
    $gt_files += $gt_files_removed;

  // Sort
  krsort($gt_files);

?>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Мои удаленные файлы</title>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle">
    <?php if(count($gt_files) == 0){ ?>
      У вас нет удаленных
    <?php } else { ?>
      <table class="report" width="70%" border="1" cellspacing="0" cellpadding="0">
        <tr height="30" align="center" valign="middle">
          <td>&nbsp;<b>Номер</b>&nbsp;</td>
          <td width="100%">&nbsp;<b>Имя</b>&nbsp;</td>
          <td>&nbsp;<b>Размер</b>&nbsp;</td>
          <td>&nbsp;<b>Скачан</b>,&nbsp; &nbsp;<b>раз</b>&nbsp;</td>
          <td>&nbsp;<b>Дата</b>&nbsp;<br>&nbsp;<b>удаления</b>&nbsp;</td>
          <td>&nbsp;<b>Действие</b>&nbsp;</td>
        </tr>
        <?php foreach($gt_files as $lv_id => $lo_file) { ?>
        <tr height="25" valign="middle">
          <td align="center">&nbsp;<?php echo($lo_file->get_id()); ?>&nbsp;</td>
          <td>&nbsp;<?php echo($lo_file->get_name()); ?>&nbsp;</td>
          <td align="right">&nbsp;<?php echo($lo_file->get_size_text()); ?>&nbsp;</td>
          <td align="center">&nbsp;<?php echo($lo_file->get_counter()); ?>&nbsp;</td>
          <td align="center"><nobr>&nbsp;<?php echo($lo_file->get_date_deleted()); ?>&nbsp;</nobr></td>
          <td align="center"><?php if($lo_file->is_deleted()) { ?><nobr>&nbsp;<a href="<?php echo($lo_file->get_url_restore()); ?>">Востановить</a>&nbsp;</nobr><?php } ?></td>
        </tr>
        <?php } ?>
      </table>
    <?php } ?>
    </td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>