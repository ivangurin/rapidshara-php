<?php

//-----------------------------------------------------------------------------
// Host manager class
//-----------------------------------------------------------------------------
  class host_manager{

    static private
      $go_manager;

    private
      $go_db,
      $gt_hosts = array();

    static function get_instance(){
      if(empty(host_manager::$go_manager))
        host_manager::$go_manager = new host_manager();
      return host_manager::$go_manager;
    }

    public function __construct(){
      $this->go_db = db::get_instance();
    }

    public function __destruct(){

    }

    // Get by name
    public function get($iv_name = ""){

      if(empty($iv_name))
        $lv_name = $_SERVER["HTTP_HOST"];
      else
        $lv_name = $iv_name;

      if(empty($lv_name))
        return false;

      if(isset($this->gt_hosts[$lv_name])){

        return $this->gt_hosts[$lv_name];

      }else{


        $lo_host = new host($lv_name);

        if(!$lo_host->is_exist())
          return false;

        $this->gt_hosts[$lv_name] = $lo_host;

        return $lo_host;

      }

    }

  }

//-----------------------------------------------------------------------------
// Host class
//-----------------------------------------------------------------------------
  class host{

    private
      $go_db,
      $gs_host,
      $gv_exist = false,
      $gv_updkz = "";

    public function __construct($iv_name = ""){

      $this->go_db = db::get_instance();

      if(empty($iv_name)){

        $this->gv_updkz                       = "I";

        $this->gs_host["name"]                = "";
        $this->gs_host["path"]                = "";

      }else{

        return $this->get($iv_name);

      }

    }

    // Get host by name
    public function get($iv_name){

      $lt_hosts = $this->go_db->query("select * from hosts where name = '$iv_name'");

      if(!is_array($lt_hosts))
        return false;

      if(count($lt_hosts) != 1)
        return false;

      $this->gs_host = $lt_hosts[0];

      $this->gv_exist = true;

      return true;

    }

    // Get name
    public function get_name(){
      return $this->gs_host["name"];
    }
    
    // Get path
    public function get_path(){
      return $this->gs_host["path"];
    }    
    
    // Get path download
    public function get_path_download(){
      return $this->gs_host["path_download"];
    }

    // Get
    public function get_data(){
      return $this->gs_host;
    }

    // Set
    public function set_data($is_host){

      if($this->gv_updkz == "D")
        return false;

      if($this->gs_host == $is_host)
        return true;

      if(empty($this->gv_updkz))
        $this->gv_updkz = "U";

    	$this->gs_host = $is_host;

      return true;

    }

    // Delete
    public function delete(){

      if($this->gv_updkz == "D")
        return true;

      $this->gv_updkz = "D";

      return true;

    }

    // Check for exist
    public function is_exist(){
      return $this->gv_exist;
    }

    // Check for changed
    public function is_chenged(){
      if(empty($this->gv_updkz))
        return false;
      else
        return true;
    }

    // Save
    public function save(){

      if(!$this->is_changed())
        return true;

      // Insert
      if($this->gv_updkz == "I"){

        $lv_query = "insert into hosts (  name
                                        , path )";

        $lv_query .= "value(  '" . $this->gs_host["name"]                . "'
                            , '" . $this->gs_host["path"]                . "' )";

        $this->go_db->query($lv_query);

        $this->gv_exist = true;

      }

      // Update
      if($this->gv_updkz == "U"){

        $lv_query  = "update hosts set";
        $lv_query .= "  beta              = '" . $this->gs_host["beta"]             . "'";
        $lv_query .= ", path              = '" . $this->gs_host["path"]             . "'";
        $lv_query .= " where name = '" . $this->gs_host["name"] . "'";

        $this->go_db->query($lv_query);

      }

      // Delete
      if($this->gv_updkz == "D"){

        $lv_query  = "delete hosts where name = '" . $this->gs_host["name"] . "'";

        $this->go_db->query($lv_query);

      }

      $this->gv_updkz = "";

    }

  }

?>