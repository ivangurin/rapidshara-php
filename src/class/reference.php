<?php

//-----------------------------------------------------------------------------
// reference manager class
//-----------------------------------------------------------------------------
  class reference_manager{

    static private
      $go_manager;

    private
      $go_db,
      $gt_references  = array(),
      $gv_number      = 0;

    static function get_instance(){
      if(empty(reference_manager::$go_manager))
        reference_manager::$go_manager = new reference_manager();
      return reference_manager::$go_manager;
    }

    public function __construct(){
      $this->go_db = db::get_instance();
    }

    public function __destruct(){

    }

    // Create
    public function create(){

      $lo_reference = new reference();

      $lv_id = $this->get_number_next();

      $this->gt_references[$lv_id] = $lo_reference;

      return $lo_reference;

    }

    // Get by id
    public function get_by_id($iv_id){

      if(empty($iv_id))
        return false;

      if(isset($this->gt_references[$iv_id])){

        return $this->gt_references[$iv_id];

      }else{

        $lo_reference = new reference($iv_id);

        if(!$lo_reference->is_exist())
          return false;

        $this->gt_references[$iv_id] = $lo_reference;

        return $lo_reference;

      }

    }

    // Get by file id
    public function get_by_file_id($iv_file_id){

      if(empty($iv_file_id))
        return false;

      $lt_references = $this->go_db->query("select id from `references` where file_id = $iv_file_id and ip = '" . $_SERVER["REMOTE_ADDR"] . "' limit 1");

      if(!is_array($lt_references))
        return false;

      if(count($lt_references) == 0)
        return false;

      $lv_id = $lt_references[0]["id"];

      $lo_reference = $this->get_by_id($lv_id);

      return $lo_reference;

    }

    // Get all
    public function get_all($iv_rows = 1000, $iv_offset = 0, $iv_sort = "asc"){

      $lt_references = $this->go_db->query("select id from `references` order by id $iv_sort limit $iv_offset, $iv_rows");

      if(!is_array($lt_references))
        return false;

      if(count($lt_references) == 0)
        return false;

      $lt_result = array();

      foreach($lt_references as $lv_tabix => $ls_reference){

        $lo_reference = $this->get_by_id($ls_reference["id"]);

        if($lo_reference)
          $lt_result[$ls_reference["id"]] = $lo_reference;

      }

      return $lt_result;

    }

     // Get
    public function get(){
      return $this->gt_references;
    }

    // Save
    public function save(){

      foreach($this->gt_references as $lv_id => $lo_reference){

        $lo_reference->save();

        if(substr($lv_id, 0, 1) == "$"){
          $lv_id_new = $lo_reference->get_id();
          $this->gt_references[$lv_id_new] = $lo_reference;
          unset($this->gt_references[$lv_id]);
        }

      }

    }

    // Get number next
    private function get_number_next(){
      $this->gv_number++;
      return "$" . $this->gv_number;
    }

    // Free
    public function free(){
      $this->gt_references = array();
    }

    // Delete
    public function delete(){
      $this->go_db->query("delete from `references` where date_created <= (now() - interval 1 day)");
    }

  }

//-----------------------------------------------------------------------------
// reference class
//-----------------------------------------------------------------------------
  class reference{

    private
      $go_db,
      $gs_reference = array(),
      $gv_exist     = false,
      $gv_updkz     = "",
      $gv_increase  = false;

    public function __construct($iv_id = ""){

      // Get database object
      $this->go_db = db::get_instance();

      if(empty($iv_id)){

        $this->gv_updkz                             = "I";

        $this->gs_reference["id"]                   = "";
        $this->gs_reference["file_id"]              = "";
        $this->gs_reference["ip"]                   = $_SERVER["REMOTE_ADDR"];
        $this->gs_reference["blocked"]              = 0;
        $this->gs_reference["date_created"]         = date("YmdHis");
        $this->gs_reference["date_required"]        = "";
        $this->gs_reference["counter"]              = 0;

      }else{

        return $this->get_by_id($iv_id);

      }

    }

    // Get by ID
    private function get_by_id($iv_id){

      $lt_references = $this->go_db->query("select * from `references` where id = $iv_id limit 1");

      if(!is_array($lt_references))
        return false;

      if(count($lt_references) == 0)
        return false;

      $this->gs_reference = $lt_references[0];

      $this->gv_exist = true;

      return true;

    }

    // Get id
    public function get_id(){
      return $this->gs_reference["id"];
    }

    // Get file id
    public function get_file_id(){
      return $this->gs_reference["file_id"];
    }

    // Get file id
    public function set_file_id($iv_file_id){
      $ls = $this->get();
      $ls["file_id"] = $iv_file_id;
      $this->set($ls);
    }

    // Get file name
    public function get_file_name(){

      $lo_file_manager = file_manager::get_instance();
      $lo_file = $go_file_manager->get_by_id($this->get_file_id());

      return $lo_file->get_name();

    }

    // Get url
    public function get_url(){

      $lo_file_manager = file_manager::get_instance();
      $lo_file = $lo_file_manager->get_by_id($this->get_file_id());

      return "http://" . $lo_file->get_host() . "/" . $lo_file->get_id() . "/" . $lo_file->get_name();

    }

    // Get
    public function get(){
      return $this->gs_reference;
    }

    // Set
    public function set($is_reference){

      // If reference deleted
      if($this->is_deleted())
        return false;

      // No changes
      if($this->gs_reference == $is_reference)
        return true;

      // Set update flag
      if(empty($this->gv_updkz))
        $this->gv_updkz = "U";

      // Set data
    	$this->gs_reference = $is_reference;

      return true;

    }

    // Delete
    public function delete(){

      if($this->is_deleted())
        return true;

      $this->gv_updkz = "D";

      return true;

    }

    // Increase counter
    public function increase_counter(){

      if($this->is_deleted())
        return false;

       $this->gv_increase = true;

    }

    // Save
    public function save(){

      if($this->gv_updkz == "I"){

        $lv_query = "insert into `references` ( file_id,
                                                ip,
                                                blocked,
                                                date_created,
                                                counter )
                      value('" . $this->gs_reference["file_id"]              . "',
                            '" . $this->gs_reference["ip"]                   . "',
                            '" . $this->gs_reference["blocked"]              . "',
                            '" . $this->gs_reference["date_created"]         . "',
                            '" . $this->gs_reference["counter"]              . "')";

        $this->go_db->query($lv_query);

        $this->gv_exist       = true;

        $this->gs_reference["id"] = $this->go_db->get_id();

      }

      if($this->gv_updkz == "U"){

        $lv_query  = "update `references` set";
        $lv_query .= "  file_id              = '" . $this->gs_reference["file_id"]            . "'";
        $lv_query .= ", ip                   = '" . $this->gs_reference["ip"]                 . "'";
        $lv_query .= ", blocked              = '" . $this->gs_reference["blocked"]            . "'";
        $lv_query .= ", date_required        = '" . $this->gs_reference["date_required"]      . "'";
        $lv_query .= " where id = '" . $this->get_id() . "'";

        $this->go_db->query($lv_query);

      }

      // Delete
      if($this->gv_updkz == "D"){

        $lv_query  = "delete `references` where id = '" . $this->get_id() . "'";

        $this->go_db->query($lv_query);

      }

      $this->gv_updkz = "";

      // Increase counter
      if($this->gv_increase){
        $this->go_db->query("update `references` set date_required = now(), counter = counter + 1 where id = '" . $this->get_id() . "'");
        $this->gv_increase = false;
      }

      return true;

    }

    // Is exist
    public function is_exist(){
      return $this->gv_exist;
    }

    // Is deleted
    public function is_deleted(){
	    if($this->gv_updkz == "D")
        return true;
      else
        return false;
    }

    // Is blocked
    public function is_blocked(){
	    if($this->gs_reference["blocked"])
        return true;
      else
        return false;
    }

  }

?>