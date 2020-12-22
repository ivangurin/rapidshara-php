<?php

  echo("disabled"); exit;

  $lv_path = "D:/Rapidshara Files";

  $lt_dirs = scandir($lv_path);

  sort($lt_dirs);

  foreach($lt_dirs as $lv_dir){

    if($lv_dir != "." && $lv_dir != ".." && $lv_dir != "cache"){

      $lt_subdirs = array();
      $lt_subdirs = scandir($lv_path . "/" . $lv_dir);

      if(count($lt_subdirs) == 2 || count($lt_subdirs) > 3){
      print_r($lv_dir . ":");
      print_r("(");
      foreach($lt_subdirs as $lv_subdir){

        $lv_index = 0;
        if($lv_subdir != "." && $lv_subdir != ".."){
          $lv_index = $lv_index + 1;
          if($lv_index == 1)
            print_r($lv_subdir);
          else
            print_r(", " . $lv_subdir);
        }

      }
      print_r(")\n");
      }

    }

  }     

?>
