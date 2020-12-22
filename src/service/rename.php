<?php

  echo("disabled"); exit;

  $lv_path = "D:/Rapidshara Files";

  $lt_dirs = scandir($lv_path);

  sort($lt_dirs);

  foreach($lt_dirs as $lv_dir){

    if($lv_dir != "." && $lv_dir != ".." && $lv_dir != "cache" && $lv_dir != "0"){

      $lt_subdirs = array();
      $lt_subdirs = scandir($lv_path . "/" . $lv_dir);

      //print_r($lt_subdirs);

      if(count($lt_subdirs) ==  3){

        foreach($lt_subdirs as $lv_index => $lv_subdir){
          if($lv_subdir == "." || $lv_subdir == "..")
            unset($lt_subdirs[$lv_index]);
        }

        $lt_subdirs = array_values($lt_subdirs);

        $lv_file_old = $lt_subdirs[0];

        $ls_pathinfo = pathinfo($lv_file_old);

        $lv_file_new = $lv_dir;

        if($lv_file_old != $lv_file_new){
          print_r($lv_dir . ": " . $lv_file_old . " -> " . $lv_file_new . "<br />\n");
          //rename($lv_path . "/" . $lv_dir . "/" . $lv_file_old, $lv_path . "/" . $lv_dir . "/" . $lv_file_new);
        }

      }

    }

  }

?>
