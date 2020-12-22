<?php

  class alert{

    static private
      $go_alert;

    private
      $go_db,
      $gt_alerts = array();

    static function get_instance(){
      if(empty(alert::$go_alert))
        alert::$go_alert = new alert();
      return alert::$go_alert;
    }

    public function __construct(){
      $this->go_db = db::get_instance();
    }

    public function __destruct(){

    }

    public function add($iv_type, $iv_message, $iv_par1 = "", $iv_par2 = "", $iv_par3 = "", $iv_par4 = ""){

      $ls_alert = array("id"         => "",
                        "date"       => date("YmdHis"),
                        "type"       => $iv_type,
                        "message"    => $iv_message,
                        "parameter1" => $iv_par1,
                        "parameter2" => $iv_par2,
                        "parameter3" => $iv_par3,
                        "parameter4" => $iv_par4,
                        "ip"         => "",
                        "user_agent" => "");

      if(isset($_SERVER["REMOTE_ADDR"]))
        $ls_alert["ip"]         = $_SERVER["REMOTE_ADDR"];

      if(isset($_SERVER["HTTP_USER_AGENT"]))
        $ls_alert["user_agent"] = $_SERVER["HTTP_USER_AGENT"];

      $this->gt_alerts[] = $ls_alert;

    }

    public function get($iv_id){

    }

    public function set($iv_id){

    }

    public function show(){
      print_r($this->gt_alerts);
    }

    public function save(){

      if(count($this->gt_alerts) == 0) return true;

      foreach($this->gt_alerts as $i => $ls_alert){
        if(empty($ls_alert["id"])){
          $this->go_db->query("insert into Alerts (date, type, message, parameter1, parameter2, parameter3, parameter4, ip, user_agent)
                                      value( '" . $ls_alert["date"]       . "',
                                             '" . $ls_alert["type"]       . "',
                                             '" . $ls_alert["message"]    . "',
                                             '" . $ls_alert["parameter1"] . "',
                                             '" . $ls_alert["parameter2"] . "',
                                             '" . $ls_alert["parameter3"] . "',
                                             '" . $ls_alert["parameter4"] . "',
                                             '" . $ls_alert["ip"]         . "',
                                             '" . $ls_alert["user_agent"] . "' )");
         $this->gt_alerts[$i]["id"]  = $this->go_db->get_id();
        }
      }

    }

  }

?>