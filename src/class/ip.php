<?php

//-----------------------------------------------------------------------------
// Host ip
//-----------------------------------------------------------------------------
  class ip{

    static private
      $gv_comment = "";

    // Is russian
    static function is_russian($iv_ip = ""){

      $lv_findme = "не";

      if(empty($iv_ip))
        $lv_ip = $_SERVER["REMOTE_ADDR"];
      else
        $lv_ip = $iv_ip;

      $lv_page = file_get_contents("http://noc.masterhost.ru/cgi-bin/rus-lookup.pl?IP=$lv_ip");
      $lv_page = strip_tags($lv_page);

      if(strpos($lv_page, $lv_findme) === false){
        return true;
      }else{
        return false;
      }

    }

    // Is upload allowed
    static function is_upload_allowed($iv_ip = ""){

      $lo_db = db::get_instance();

      if(empty($iv_ip))
        $lv_ip = $_SERVER["REMOTE_ADDR"];
      else
        $lv_ip = $iv_ip;

      $lt_ips = $lo_db->query("select * from ips where ip = '$lv_ip' and upload = 0 limit 1");

      if(!is_array($lt_ips))
        return true;

      if(count($lt_ips) > 0){

        $ls_ip = $lt_ips[0];

        self::$gv_comment = $ls_ip["upload_comment"];

        return false;

      }

      return true;

    }

    // Is download allowed
    static function is_download_allowed($iv_ip = ""){

      $lo_db = db::get_instance();

      if(empty($iv_ip))
        $lv_ip = $_SERVER["REMOTE_ADDR"];
      else
        $lv_ip = $iv_ip;

      $lt_ips = $lo_db->query("select * from ips where ip = '$lv_ip' and download = 0 limit 1");

      if(!is_array($lt_ips))
        return true;

      if(count($lt_ips) > 0){

        $ls_ip = $lt_ips[0];

        self::$gv_comment = $ls_ip["download_comment"];

        return false;

      }

      return true;

    }

    // Get last comment
    static function get_comment(){
      return self::$gv_comment;
    }

  }

?>             