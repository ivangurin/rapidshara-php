<?php

//-----------------------------------------------------------------------------
// Configuration class
//-----------------------------------------------------------------------------
  class configuration{

    static private
      $go_configuration;

    private
      $go_db,
      $gs_values       = array(),
      $gs_descriptions = array(),
      $gs_comments     = array();

    static function get_instance(){
      if(empty(configuration::$go_configuration))
        configuration::$go_configuration = new configuration();
      return configuration::$go_configuration;
    }

    public function __construct(){

      global $gv_beta;

      $this->go_db = db::get_instance();

      $lt_values = $this->go_db->query("select * from configuration");

      $ls_value = array();
      if(count($lt_values) > 0){
        foreach($lt_values as $i => $ls_value){
          $this->gs_values[$ls_value["variable"]] = $ls_value["value"];
          $this->gs_descriptions[$ls_value["variable"]] = $ls_value["description"];
          $this->gs_comments[$ls_value["variable"]] = $ls_value["comment"];
        }
      }

      if($gv_beta){
        $this->gs_values["host"]        = $this->gs_values["host_beta"];
        $this->gs_values["host_upload"] = $this->gs_values["host_upload_beta"];
      }

    }

    public function get($iv_variable = ""){
      if(!empty($iv_variable))
        return $this->gs_values[$iv_variable];
      else
        return $this->gs_values;
    }

    public function get_description($iv_variable = ""){
      if(!empty($iv_variable))
        return $this->gs_descriptions[$iv_variable];
      else
        return $this->gs_descriptions;
    }

    public function get_comment($iv_variable = ""){
      if(!empty($iv_variable))
        return $this->gs_comments[$iv_variable];
      else
        return $this->gs_comments;
    }

    // Get host
    public function get_host(){
      return $this->get("host");
    }

    // Get download type
    public function get_download_type(){

      if($this->get("download_ranges"))
        return "ranges";
      if($this->get("download_readfile"))
        return "readfile";
      if($this->get("download_xsendfile"))
        return "xsendfile";
      return false;
    }

    // Get size
    public function get_size(){
      return $this->get("size");
    }

    // Get days after upload
    public function get_days_after_upload(){
      return $this->get("days_after_upload");
    }

    // Get days after download
    public function get_days_after_download(){
      return $this->get("days_after_download");
    }

    // Get days after restore
    public function get_days_after_restore(){
      return $this->get("days_after_restore");
    }

    // Get days after delete
    public function get_days_after_delete(){
      return $this->get("days_after_delete");
    }

  }
?>
