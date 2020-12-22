<?php
exit;	
  require_once("cfg/config.php");
  require_once("func/database.php");

  Connect($gDatabaseHost, $gDatabaseLogin, $gDatabasePassword, $gDatabaseName);

  $Files = Query("select * from files where DelMark = 0 order by FileID");

  $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>". "\r\n";
  $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">" . "\r\n";

  while ($File = Fetch($Files)) {
  	$xml .= "  <url>" . "\r\n";
  	$xml .= "    <loc>" . "http://rapidshara.ru/" . $File["FileID"] . "</loc>" . "\r\n";
    $xml .= "  </url>" . "\r\n";
  }

  $xml .= "</urlset>";

  file_put_contents("sitemap.xml", $xml);

  if(isset($_GET["debug"]) && $_GET["debug"])
    print_r($xml);

?>