<?php

echo("disabled"); exit;

  require_once("../cfg/config.php");
  require_once("../func/database.php");
  require_once("../func/configuration.php");
  require_once("../func/image.php");

  // Connect to BD
  $go_db = db::get_instance();
  $go_db->connect($gDatabaseHost, $gDatabaseLogin, $gDatabasePassword, $gDatabaseName);

  // Get configuration
  $go_configuration = configuration::get_instance();
  $gs_configuration = $go_configuration->get();

  $go_image_manager = image_manager::get_instance();


  for($lv_index = 0; $lv_index <= 47; $lv_index++){

    $gt_images = $go_image_manager->get_from_to($lv_index * 1000, $lv_index * 1000 + 999, 1);

    foreach($gt_images as $go_image){

      $gs_image = $go_image->get();


      $gv_extension = strtolower($gs_image["Extension"]);

      if($gs_image["Extension"] != $gv_extension){
        print_r($gs_image["ID"] . ":" . $gs_image["Extension"] . "->" . $gv_extension . "<br />\n");
        $gs_image["Extension"] = $gv_extension;
        $go_image->set($gs_image);
      }

    }

    $go_image_manager->save();

    $go_image_manager->free();

  }



?>
