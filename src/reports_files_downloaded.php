<?php

  // Set page
  $lv_page = 1;
  if(isset($_GET["page"]) && is_numeric($_GET["page"]))
    $lv_page = db::escape($_GET["page"]);

  // Get file manager
  $go_file_manager = file_manager::get_instance();

  // Get count of downloaded files
  $gv_files = $go_file_manager->get_count_downloaded();

  // Create navigation
  $go_navigation = new navigation("/$gv_page/$gv_section/$gv_report", $lv_page, $gv_files);

  // Get downloaded files
  $gt_files = $go_file_manager->get_downloaded($go_navigation->get_rows(), $go_navigation->get_offset());

?>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title>Скаченные файлы</title>
  <script type="text/javascript" src="/js/navigate.js"></script>
  <script type="text/javascript" src="/js/jquery/jquery.js"></script>
  <script type="text/javascript" src="/js/jquery/jquery.tablesorter.js"></script>
	<script type="text/javascript">
	$(function() {
		$("#files").tablesorter()
	});
  </script>    	  
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="100%" align="center" valign="middle">
    <?php if(!$gt_files){ ?>
      Нет скаченный файлов
    <?php } else { ?>
      <table class="report" id="files" width="70%" border="1" cellspacing="0" cellpadding="0">
        <thead> 
          <tr height="30" align="center" valign="middle">
            <th>&nbsp;<b>Юзер</b>&nbsp;</th>
            <th>&nbsp;<b>Номер</b>&nbsp;</th>
            <th width="100%">&nbsp;<b>Имя</b>&nbsp;</th>
            <th>&nbsp;<b>Размер</b>&nbsp;</th>
            <th>&nbsp;<b>Скачан</b>&nbsp;</th>
            <th>&nbsp;<b>Дата</b>&nbsp;</th>
            <th>&nbsp;<b>Действие</b>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($gt_files as $lv_id => $lo_file) { ?>
        <tr height="25" valign="middle">
          <td align="center">&nbsp;<?php echo($lo_file->get_user_id()); ?>&nbsp;</td>
          <td align="center">&nbsp;<a href="<?php echo($lo_file->get_url()); ?>"><?php echo($lo_file->get_id()); ?></a>&nbsp;</td>
          <td>&nbsp;<?php echo($lo_file->get_name()); ?>&nbsp;</td>
          <td align="right">&nbsp;<?php echo($lo_file->get_size_text()); ?>&nbsp;</td>
          <td align="center">&nbsp;<?php echo($lo_file->get_counter()); ?>&nbsp;</td>
          <td align="center"><nobr>&nbsp;<?php echo($lo_file->get_date_download()); ?>&nbsp;</nobr></td>
          <td align="center"><nobr>&nbsp;<a href="<?php echo($lo_file->get_url_links()); ?>">L</a>&nbsp;|&nbsp;<a href="<?php echo($lo_file->get_url_change()); ?>">E</a>&nbsp;|&nbsp;<a href="<?php echo($lo_file->get_url_delete()); ?>">D</a>&nbsp;|&nbsp;<a href="<?php echo($lo_file->get_url_remove()); ?>">R</a>&nbsp;</nobr></td>
        </tr>
        <?php } ?>
        </tbody>
      </table>
    </td></tr>
    <tr><td height="30" align="center" valign="center"><?php echo($go_navigation->get()); ?></td></tr>
    <?php } ?>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>