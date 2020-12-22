<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <title><?php echo($gv_title); ?></title>
</head>
<body>
  <table class="main" border="0" cellpadding="0" cellspacing="0">
    <tr><td><?php require("menu.php"); ?></td></tr>
    <tr><td height="30%" align="center" valign="middle"><?php require("banner_top.php"); ?></td></tr>
    <tr><td height="40%" align="center" valign="middle">
      <p><?php if (isset($gv_data)) echo($gv_data); ?></p>
    </td></tr>
    <tr><td height="30%" align="center" valign="middle"><?php require("banner_bottom.php"); ?></td></tr>
    <tr><td align="center"><?php require("counters.php"); ?></td></tr>
  </table>
</body>
</html>