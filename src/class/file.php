<?php

//-----------------------------------------------------------------------------
// File manager class
//-----------------------------------------------------------------------------
  class file_manager{

    static private
      $go_manager;

    private
      $go_db,
      $gt_files  = array(),
      $gv_number = 0;

    static function get_instance(){
      if(empty(file_manager::$go_manager))
        file_manager::$go_manager = new file_manager();
      return file_manager::$go_manager;
    }

    public function __construct(){
      $this->go_db = db::get_instance();
    }

    public function __destruct(){

    }

    // Create
    public function create(){

      $lo_file = new file();

      $lv_id = $this->get_number_next();

      $this->gt_files[$lv_id] = $lo_file;

      return $lo_file;

    }

    // Get count
    public function get_count($iv_deleted = 0){
      $lt_files = $this->go_db->query("select count(*) from files where deleted = $iv_deleted");
      if(isset($lt_files[0]["count(*)"]))
        return $lt_files[0]["count(*)"];
      return false;
    }

    // Get downloaded
    public function get_count_downloaded($iv_deleted = 0){
      $lt_files = $this->go_db->query("select count(*) from files where deleted = $iv_deleted and counter > 0");
      if(isset($lt_files[0]["count(*)"]))
        return $lt_files[0]["count(*)"];
      return false;
    }

    // Get not downloaded
    public function get_count_not_downloaded($iv_deleted = 0){
      $lt_files = $this->go_db->query("select count(*) from files where deleted = $iv_deleted and counter = 0");
      if(isset($lt_files[0]["count(*)"]))
        return $lt_files[0]["count(*)"];
      return false;
    }


    // Get count by user
    public function get_count_by_user($iv_user_id, $iv_deleted = 0){

      if(empty($iv_user_id))
        return false;

      $lt_files = $this->go_db->query("select count(*) from files where UserID = $iv_user_id and Deleted = $iv_deleted");

      if(isset($lt_files[0]["count(*)"]))
        return $lt_files[0]["count(*)"];

      return false;

    }

    // Get by ID
    public function get_by_id($iv_id){

      if(empty($iv_id))
        return false;

      if(isset($this->gt_files[$iv_id])){

        return $this->gt_files[$iv_id];

      }else{

        $lo_file = new file($iv_id);

        if(!$lo_file->is_exist())
          return false;

        $this->gt_files[$iv_id] = $lo_file;

        return $lo_file;

      }

    }

    // Get by UserID
    public function get_by_user_id($iv_user_id, $iv_deleted = 0){

      if(empty($iv_user_id))
        return false;

      $lt_files = $this->go_db->query("select ID from files where UserID = $iv_user_id and Deleted = $iv_deleted");

      if(!is_array($lt_files))
        return false;

      if(count($lt_files) == 0)
        return false;

      $lt_result = array();

      foreach($lt_files as $lv_tabix => $ls_file){

        $lo_file = $this->get_by_id($ls_file["ID"]);

        if($lo_file)
          $lt_result[$ls_file["ID"]] = $lo_file;

      }

      return $lt_result;

    }

    // Get uploaded
    public function get_uploaded($iv_rows = 1000, $iv_offset = 0){
      return $this->get_all($iv_rows, $iv_offset, "", "id desc", 0);
    }

    // Get downloaded
    public function get_downloaded($iv_rows = 1000, $iv_offset = 0){
      return $this->get_all($iv_rows, $iv_offset, "and counter > 0", "date_download desc", 0);
    }

    // Get not downloaded
    public function get_not_downloaded($iv_rows = 1000, $iv_offset = 0){
      return $this->get_all($iv_rows, $iv_offset, "and counter = 0", "date_uploaded desc", 0);
    }

    // Get deleted
    public function get_deleted($iv_rows = 1000, $iv_offset = 0){
      return $this->get_all($iv_rows, $iv_offset, "", "date_deleted desc, id desc", 1);
    }

    // Get removed
    public function get_removed($iv_rows = 1000, $iv_offset = 0){
      return $this->get_all($iv_rows, $iv_offset, "", "date_deleted desc, id desc", 2);
    }

    // Get all
    public function get_all($iv_rows = 1000, $iv_offset = 0, $iv_where = "", $iv_sort = "id asc", $iv_deleted = 0){

      $lt_files = $this->go_db->query("select id from files where deleted = $iv_deleted $iv_where order by $iv_sort limit $iv_offset, $iv_rows");

      if(!is_array($lt_files))
        return false;

      if(count($lt_files) == 0)
        return false;

      $lt_result = array();

      foreach($lt_files as $lv_tabix => $ls_file){

        $lo_file = $this->get_by_id($ls_file["id"]);

        if($lo_file)
          $lt_result[$ls_file["id"]] = $lo_file;

      }

      return $lt_result;

    }

    // Get to delete
    public function get_to_delete(){

      // Get configuration
      $lo_configuration = configuration::get_instance();

      // Get user manager
      $lo_user_manager = user_manager::get_instance();

      $lv_days_u = $lo_configuration->get_days_after_upload();
      $lv_days_d = $lo_configuration->get_days_after_download();
      $lv_days_r = $lo_configuration->get_days_after_restore();

      $lv_query  = "select id from files where deleted = 0 and keep = 0 and ";
      $lv_query .= " ( (userid  = 0 and date_uploaded < now() - interval $lv_days_u day and ( date_restored is null or date_restored <= now() - interval $lv_days_r day ) and counter <= 1) or";
      $lv_query .= "   (userid != 0 and date_uploaded < now() - interval $lv_days_u day and ( date_restored is null or date_restored <= now() - interval $lv_days_r day ) and counter  = 0) or";
      $lv_query .= "   (                date_download < now() - interval $lv_days_d day and ( date_restored is null or date_restored <= now() - interval $lv_days_r day ) and counter <> 0)";
      $lv_query .= " ) order by date_download desc, date_uploaded desc";

      $lt_files = $this->go_db->query($lv_query);

      if(!is_array($lt_files))
        return false;

      if(count($lt_files) == 0)
        return false;

      $lt_result = array();

      foreach($lt_files as $lv_tabix => $ls_file){

        $lo_file = $this->get_by_id($ls_file["id"]);

        if($lo_file){

          $lt_result[$ls_file["id"]] = $lo_file;

          if($lo_file->is_owner()){
            $lo_user = $lo_user_manager->get_by_id($lo_file->get_user_id());
            if($lo_user && $lo_user->is_keep())
              unset($lt_result[$ls_file["id"]]);
          }

        }

      }

      if(count($lt_result) == 0)
        return false;

      return $lt_result;

    }

    // Get to remove
    public function get_to_remove(){

      // Get configuration
      $lo_configuration = configuration::get_instance();

      $lv_days_d = $lo_configuration->get_days_after_delete();

      $lv_query  = "select id from files where deleted = 1 and ";
      $lv_query .= " ( (userid  = 0                                                   ) or ";
      $lv_query .= "   (userid != 0 and date_deleted < now() - interval $lv_days_d day)    ";
      $lv_query .= " ) order by date_deleted desc, id desc";

      $lt_files = $this->go_db->query($lv_query);

      if(!is_array($lt_files))
        return false;

      if(count($lt_files) == 0)
        return false;

      $lt_result = array();

      foreach($lt_files as $lv_tabix => $ls_file){

        $lo_file = $this->get_by_id($ls_file["id"]);

        if($lo_file)
          $lt_result[$ls_file["id"]] = $lo_file;

      }

      if(count($lt_result) == 0)
        return false;

      return $lt_result;

    }

    // Get
    public function get(){
      return $this->gt_files;
    }
        
    // Save
    public function save(){

      foreach($this->gt_files as $lv_id => $lo_file){

        $lo_file->save();

        if(substr($lv_id, 0, 1) == "$"){
          $lv_id_new = $lo_file->get_id();
          $this->gt_files[$lv_id_new] = $lo_file;
          unset($this->gt_files[$lv_id]);
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
      $this->gt_files = array();
    }

  }

//-----------------------------------------------------------------------------
// File class
//-----------------------------------------------------------------------------
  class file{

    private
      $go_db,
      $gs_file     = array(),
      $gv_exist    = false,
      $gv_updkz    = "",
      $gv_increase = false,
      $gv_path_tmp = "";

    public function __construct($iv_id = ""){

      // Get database object
      $this->go_db = db::get_instance();

      if(empty($iv_id)){

        $this->gv_updkz                        = "I";

        $this->gs_file["ID"]                   = "";
        $this->gs_file["UserID"]               = "0";
        $this->gs_file["Host"]                 = "";
        $this->gs_file["Path"]                 = "";
        $this->gs_file["Type"]                 = "";
        $this->gs_file["Name"]                 = "";
        $this->gs_file["Size"]                 = "0";
        $this->gs_file["Description"]          = "0";
        $this->gs_file["Deleted"]              = "0";
        $this->gs_file["Date_Uploaded"]        = date("YmdHis");
        $this->gs_file["Date_Download"]        = "";
        $this->gs_file["Date_Changed"]         = "";
        $this->gs_file["Date_Deleted"]         = "";
        $this->gs_file["Date_Restore"]         = "";
        $this->gs_file["IP"]                   = $_SERVER["REMOTE_ADDR"];
        $this->gs_file["Mirrors"]              = "";
        $this->gs_file["Redirect"]             = "";
        $this->gs_file["Counter"]              = "0";

      }else{

        return $this->get_by_id($iv_id);

      }

    }

    // Get by ID
    private function get_by_id($iv_id){

      $lt_files = $this->go_db->query("select * from files where ID = $iv_id");

      if(!is_array($lt_files))
        return false;

      if(count($lt_files) != 1)
        return false;

      $this->gs_file = $lt_files[0];

      $this->gv_exist = true;

      return true;

    }

    // Get ID
    public function get_id(){
      return $this->gs_file["ID"];
    }

    // Get user ID
    public function get_user_id(){
      return $this->gs_file["UserID"];
    }

    // Get type
    public function get_host(){
      return $this->gs_file["Host"];
    }

    // Get type
    public function get_type(){
      return $this->gs_file["Type"];
    }

    // Get name
    public function get_name(){
      return $this->gs_file["Name"];
    }
    
    // Get extension
    public function get_extension(){
      return strtolower(file_service::get_extension($this->get_name()));
    }
    
    // Get size
    public function get_size(){
      return $this->gs_file["Size"];
    }

    // Get size text
    public function get_size_text(){
      if($this->gs_file["Size"] >= 1024 * 1024)
        $lv_size = round($this->gs_file["Size"]/1024/1024, 2) . "&nbsp;Mb";
      elseif($this->gs_file["Size"] >= 10)
        $lv_size = round($this->gs_file["Size"]/1024, 2) . "&nbsp;Kb";
      else
        $lv_size = $this->gs_file["Size"] . "&nbsp;b";
      return $lv_size;
    }

    // Get size text
    public function get_size_text_with_bytes(){
      if($this->gs_file["Size"] >= 1024 * 1024)
        $lv_size = round($this->gs_file["Size"]/1024/1024, 2) . "&nbsp;Mb";
      else
        $lv_size = round($this->gs_file["Size"]/1024, 2) . "&nbsp;Kb";

      $lv_size .= " (" . $this->gs_file["Size"] . " bytes)";
      
      return $lv_size;
    }

    // Get description
    public function get_description(){
      return $this->gs_file["Description"];
    }

    // Get description
    public function is_descriptioned(){
      if(empty($this->gs_file["Description"]))
        return false;
      else
        return true;
    }

    // Get password
    public function get_password(){
      return $this->gs_file["Password"];
    }

    // Get date upload
    public function get_date_upload(){
      return $this->gs_file["Date_Uploaded"];
    }

    // Get date downloaded
    public function get_date_download(){
      return $this->gs_file["Date_Download"];
    }
    
    // Get date deleted
    public function get_date_deleted(){
      return $this->gs_file["Date_Deleted"];
    }    

    // Get counter
    public function get_counter(){
      return $this->gs_file["Counter"];
    }

    // Get path
    public function get_path(){     
     
      // Get host
      $go_host_manager = host_manager::get_instance();
      $go_host         = $go_host_manager->get($this->gs_file["Host"]);
      
      return $go_host->get_path() . "/" .
             $this->get_id();
    }

    // Set temp path
    public function set_path_tmp($iv_path){
      $this->gv_path_tmp = $iv_path;
    }

    // Get temp path
    public function get_path_tmp(){
      return $this->gv_path_tmp;
    }

    public function get_url(){
      return "http://" . $_SERVER["HTTP_HOST"] . "/" . $this->get_id();
    }

    public function get_url_links($iv_host = ""){
      if(empty($iv_host))
        return "http://" . $_SERVER["HTTP_HOST"] . "/" . $this->get_id() . "/links";
      else
        return "http://" . $iv_host .              "/" . $this->get_id() . "/links";
    }

    public function get_url_change(){
      return "http://" . $_SERVER["HTTP_HOST"] . "/" . $this->get_id() . "/change";
    }

    public function get_url_delete(){
      return "http://" . $_SERVER["HTTP_HOST"] . "/" . $this->get_id() . "/delete";
    }

    public function get_url_remove(){
      return "http://" . $_SERVER["HTTP_HOST"] . "/" . $this->get_id() . "/remove";
    }

    public function get_url_restore(){
      return "http://" . $_SERVER["HTTP_HOST"] . "/" . $this->get_id() . "/restore";
    }

    public function get_url_download(){
      return "http://" . $_SERVER["HTTP_HOST"] . "/" . $this->get_id() . "/" . $this->get_name();
    }

    // get mirrors
    public function get_mirrors(){

      if(empty($this->gs_file["Mirrors"]))
        return false;

      $lt_mirrors = explode(",", $this->gs_file["Mirrors"]);

      return true;

      //[<b><a href="$mirror_url">������� $mirror_id</a></b>]

    }

    // Get
    public function get(){
      return $this->gs_file;
    }

    // Set
    public function set($is_file, $iv_udc = true){

      // If file deleted
      if($this->is_deleted())
        return false;

      // If file removed
      if($this->is_removed())
        return false;

      // No changes
      if($this->gs_file == $is_file)
        return true;

      // Set update flag
      if(empty($this->gv_updkz))
        $this->gv_updkz = "U";

      // Keep old data
      $ls_file = $this->gs_file;

      // Set data
    	$this->gs_file = $is_file;

      // Set update time
      if($this->gv_updkz == "U" && $iv_udc)
        $this->gs_file["Date_Changed"] = date("YmdHis");

      return true;

    }

    // Delete
    public function delete(){

      if($this->is_removed())
        return false;

      if($this->is_deleted())
        return true;

      $this->gv_updkz = "U";

      $this->gs_file["Deleted"]      = "1";
      $this->gs_file["Date_Deleted"] = date("YmdHis");

      return true;

    }

    // Remove
    public function remove(){

      if($this->is_removed())
        return true;

      $this->gv_updkz = "R";

      $this->gs_file["Deleted"]      = "2";
      $this->gs_file["Date_Deleted"] = date("YmdHis");

      return true;

    }
    
    // Restore
    public function restore(){
    
      // Is removed
      if($this->is_removed())
        return false;
      
      // Is not removed and not deleted    
      if(!$this->is_deleted())
        return true; 
       
      $this->gv_updkz = "U";     
        
      $this->gs_file["Deleted"]       = "0";     
      $this->gs_file["Date_Deleted"]  = "0";
      $this->gs_file["Date_Restored"] = date("YmdHis");    

      return true;

    }    

    // Increase counter
    public function increase_counter(){

      if($this->is_deleted())
        return false;

      if($this->is_removed())
        return false;

      $this->gv_increase = true;

    }

    // Download
    public function download(){

      // Get configuration
      $lo_configuration = configuration::get_instance();

      $lv_type = $lo_configuration->get_download_type();


      if($lv_type === false)
        return false;

      // Get host
      $go_host_manager = host_manager::get_instance();
      $go_host         = $go_host_manager->get();


      $lv_referer = "";
      if(isset($_SERVER["HTTP_REFERER"]))
        $lv_referer = $_SERVER["HTTP_REFERER"];

      $lv_range = false;
      if(isset($_SERVER["HTTP_RANGE"]))
        $lv_range = true;

      if($lv_type != "ranges" && $lv_range){
        file_service::http_403();
        return false;
      }

      if($lv_referer == "http://rapidshara.ru/js/player/player.swf"){

      }

      $this->increase_counter();
      $this->save();

      switch($lv_type){
        case "ranges":
          return file_service::download($this->get_path(), $this->get_name(), $this->get_type());
        case "readfile":
          return file_service::download_readfile($this->get_path(), $this->get_name(), $this->get_type());
        case "xsendfile":
          //return file_service::download_xsendfile($this->get_path(), $this->get_name(), $this->get_type());
        //case "x-accel-redirect":
                                                                                
          return file_service::download_xaccelredirect($this->get_path(), $this->get_name(), $this->get_type(), $go_host->get_path_download() . "/" . $this->get_id());
      }

    }

    // Save
    public function save(){

      if($this->gv_updkz == "I" && !$this->is_exist()){

        $lv_query = "insert into files ( UserID,
                                         Host,
                                         Path,
                                         Type,
                                         Name,
                                         Size,
                                         Deleted,
                                         Date_Uploaded,
                                         IP,
                                         Counter)
                      value('" . $this->gs_file["UserID"]               . "',
                            '" . $this->gs_file["Host"]                 . "',
                            '" . $this->gs_file["Path"]                 . "',
                            '" . $this->gs_file["Type"]                 . "',
                            '" . $this->gs_file["Name"]                 . "',
                            '" . $this->gs_file["Size"]                 . "',
                            '" . $this->gs_file["Description"]          . "',
                            '" . $this->gs_file["Date_Uploaded"]        . "',
                            '" . $this->gs_file["IP"]                   . "',
                            '" . $this->gs_file["Counter"]              . "')";

        $this->go_db->query($lv_query);

        $this->gv_exist       = true;
        $this->gs_file["ID"] = $this->go_db->get_id();
        $this->gs_file["ID"] = $this->gs_file["ID"];
        
        if($this->gs_file["ID"] != 0 && !empty($this->gv_path_tmp))
          file_service::replace($this->gv_path_tmp, $this->get_path());

      }

      if($this->gv_updkz == "U" && $this->is_exist()){

        $lv_query  = "update files set";
        $lv_query .= "  UserID               = '" . $this->gs_file["UserID"]               . "'";
        $lv_query .= ", Host                 = '" . $this->gs_file["Host"]                 . "'";
        $lv_query .= ", Path                 = '" . $this->gs_file["Path"]                 . "'";
        $lv_query .= ", Type                 = '" . $this->gs_file["Type"]                 . "'";
        $lv_query .= ", Name                 = '" . $this->gs_file["Name"]                 . "'";
        $lv_query .= ", Size                 = '" . $this->gs_file["Size"]                 . "'";
        $lv_query .= ", Description          = '" . $this->gs_file["Description"]          . "'";
        $lv_query .= ", Mirrors              = '" . $this->gs_file["Mirrors"]              . "'";
        $lv_query .= ", Password             = '" . $this->gs_file["Password"]             . "'";
        $lv_query .= ", Deleted              = '" . $this->gs_file["Deleted"]              . "'";
        if(!empty($this->gs_file["Date_Uploaded"]))
          $lv_query .= ", Date_Uploaded  = '" . $this->gs_file["Date_Uploaded"]  . "'";
        if(!empty($this->gs_file["Date_Changed"]))
          $lv_query .= ", Date_Changed  = '" . $this->gs_file["Date_Changed"]  . "'";
        if(!empty($this->gs_file["Date_Deleted"]))
          $lv_query .= ", Date_Deleted  = '" . $this->gs_file["Date_Deleted"]  . "'";
        if(!empty($this->gs_file["Date_Restored"]))
          $lv_query .= ", Date_Restored  = '" . $this->gs_file["Date_Restored"]  . "'";
        if(!empty($this->gs_file["Date_Download"]))
          $lv_query .= ", Date_Download = '" . $this->gs_file["Date_Download"] . "'";

        $lv_query .= " where id = '" . $this->get_id() . "'";

        $this->go_db->query($lv_query);

      }

      // Remove
      if($this->gv_updkz == "R"  && $this->is_exist()){

        $lv_query  = "update files set";
        $lv_query .= "  Deleted       = '" . $this->gs_file["Deleted"]       . "'";
        $lv_query .= ", Date_Deleted  = '" . $this->gs_file["Date_Deleted"]  . "'";
        $lv_query .= " where id = '" . $this->get_id() . "'";

        $this->go_db->query($lv_query);

        file_service::delete_file($this->get_path());

      }

      // Increase counter
      if($this->gv_increase && $this->is_exist()){
        $this->go_db->query("update files set Date_Download = now(), Counter = Counter + 1 where ID = '" . $this->get_id() . "'");
        $this->gv_increase = false;
      }

      $this->gv_updkz = "";

      return true;

    }


    // Is exist
    public function is_exist(){
      return $this->gv_exist;
    }

    // Is owner
    public function is_owner(){
	    if($this->gs_file["UserID"] != 0)
        return true;
      else
        return false;
    }

    // Is deleted
    public function is_deleted(){
	    if($this->gv_updkz == "D" || $this->gs_file["Deleted"] == 1)
        return true;
      else
        return false;
    }

    // Is removed
    public function is_removed(){
	    if($this->gv_updkz == "R" || $this->gs_file["Deleted"] == 2)
        return true;
      else
        return false;
    }

    // Is blocked
    public function is_blocked(){
	    if($this->gs_file["Blocked"])
        return true;
      else
        return false;
    }

    // Is protected
    public function is_protected(){
	    if(!empty($this->gs_file["Password"]))
        return true;
      else
        return false;
    }

    // Is sold
    public function is_for_sell(){
	    //if(!empty($this->gs_file[""]))
      //  return true;
      //else
      return false;
    }

  }

//-----------------------------------------------------------------------------
// File service
//-----------------------------------------------------------------------------
  class file_service{

    // Get name
    static function get_name($iv_name){
      $ls_info = pathinfo($iv_name);
      if(isset($ls_info["filename"]))
        return $ls_info["filename"];
    }

    // Get extension
    static function get_extension($iv_name){
      $ls_info = pathinfo($iv_name);
      if(isset($ls_info["extension"]))
        return $ls_info["extension"];
    }

    // Create directory
    static function create_directory($iv_path){
      $ls_info = pathinfo($iv_path);
      if(is_dir($ls_info["dirname"]))
        return true;
      return mkdir($ls_info["dirname"]);
    }

    // Delete directory
    static function delete_directory($iv_path){
      $ls_info = pathinfo($iv_path);
      if(is_dir($ls_info["dirname"]))
        return rmdir($ls_info["dirname"]);
      return true;
    }

    // Delete file
    static function delete_file($iv_path){
      if(file_exists($iv_path))
        return unlink($iv_path);
      return true;
    }

    // Repalce
    static function replace($iv_from, $iv_to){
      if(!empty($iv_from) && !empty($iv_to)){
        return rename($iv_from, $iv_to);
      }else{
        return false;
      }
    }

    function download($iv_path, $iv_name, $iv_type){

      define('BUF_SIZE', 1024*1);
      define('CON_STATUS_NORMAL',  0);
      define('CON_STATUS_ABORTED', 1);
      define('CON_STATUS_TIMEOUT', 2);

      if(!file_exists($iv_path)){
        file_service::http_404();
        return false;
      }

      $lo_handle = fopen($iv_path, "rb");

      if(!is_resource($lo_handle)){
        file_service::http_403();
        return false;
      }

      $lv_size = filesize($iv_path);
      $lv_time = filemtime($iv_path);

      $lv_type = $iv_type;
      if(empty($lv_type)){
        $lv_type = "application/octet-stream";
      }

      if(isset($_SERVER["HTTP_RANGE"])){
        $lv_range = $_SERVER["HTTP_RANGE"];
        $lv_range = str_replace("bytes=", "", $lv_range);
        $lv_range = str_replace("-", "", $lv_range);

        fseek($lo_handle, $lv_range);

        file_service::http_206($iv_name, $lv_range, $lv_size, $lv_time, $lv_type);

      }else{
        file_service::http_200($iv_name, $lv_size, $lv_time, $lv_type, true);
      }

      while(!feof($lo_handle) and connection_status() == CON_STATUS_NORMAL){
        $lv_content = fread($lo_handle, BUF_SIZE);
        print $lv_content;
      }

      fclose($lo_handle);

    }

    function download_readfile($iv_path, $iv_name, $iv_type){

      if(!file_exists($iv_path)){
        file_service::http_404();
        return false;
      }

      $lv_size = filesize($iv_path);
      $lv_time = filemtime($iv_path);

      $lv_type = $iv_type;
      if(empty($lv_type)){
        $lv_type = "application/octet-stream";
      }

      file_service::http_200($iv_name, $lv_size, $lv_time, $lv_type);

      readfile($iv_path);

      return true;

    }

    function download_xsendfile($iv_path, $iv_name, $iv_type){

      if(!file_exists($iv_path)){
        file_service::http_404();
        return false;
      }

      $lv_size = filesize($iv_path);
      $lv_time = filemtime($iv_path);

      $lv_type = $iv_type;
      if(empty($lv_type)){
        $lv_type = "application/octet-stream";
      }

      file_service::http_200($iv_name, $lv_size, $lv_time, $lv_type);

      header("X-Sendfile: $iv_path");

      return true;

    }

   
     function download_xaccelredirect($iv_path, $iv_name, $iv_type, $iv_path_download){

           
      if(!file_exists($iv_path)){
        file_service::http_404();
        return false;
      }

    
      $lv_size = filesize($iv_path);
      $lv_time = filemtime($iv_path);

      $lv_type = $iv_type;
      if(empty($lv_type)){
        $lv_type = "application/octet-stream";
      }

      
      file_service::http_200($iv_name, $lv_size, $lv_time, $lv_type);

    
      //echo($iv_path_download); die;
      
      header("X-Accel-Redirect: $iv_path_download");

      return true;

    }

    static function http_200($iv_name, $iv_size, $iv_time, $iv_type, $iv_ranges = false){
      header("HTTP/1.1 200 OK");
      header("Date: " . file_service::get_gmt());
      header("Expires: Mon, 27 Jan 1986 14:40:00 GMT");
      header("Last-Modified: " . file_service::get_gmt($iv_time));
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Pragma: Public");
      if($iv_ranges)
        header("Accept-Ranges: bytes");
      else
        header("Accept-Ranges: none");
      header("Content-Disposition: attachment;");
      header("Content-Type: " . $iv_type);
      header("Content-Description: File Transfer");
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: " . $iv_size);
      header("Proxy-Connection: close");
    }

    function http_206($iv_name, $iv_range, $iv_size, $iv_time, $iv_type){
      header("HTTP/1.1 206 Partial Content");
      header("Date: " . file_service::get_gmt());
      header("Expires: Mon, 27 Jan 1986 14:40:00 GMT");
      header("Last-Modified: " . file_service::get_gmt($iv_time));
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Pragma: Public");
      header("Accept-Ranges: bytes");
      header("Content-Disposition: attachment;");
      header("Content-Type: " . $iv_type);
      header("Content-Description: File Transfer");
      header("Content-Transfer-Encoding: binary");
      header("Content-Range: bytes " . $iv_range . "-" . ($iv_size - 1) . "/" . $iv_size);
      header("Content-Length: " . ($iv_size - $iv_range));
      header("Proxy-Connection: close");
    }

    function http_403(){
      header("HTTP/1.0 403 Forbidden", true, 403);
      header("Proxy-Connection: close");
    }

    function http_404(){
      header("HTTP/1.0 404 Not Found", true, 404);
      header("Proxy-Connection: close");
    }

    static function get_gmt($time=null){

      $offset = date('O');
      if ($offset{0} == '+'){
        $roffset = '-';
      }else{
        $roffset = '+';
      }

      $roffset .= $offset{1} . $offset{2};

      if (empty($time)){
        $time = time();
      }

      return (date('D, d M Y H:i:s', $time+$roffset*3600) . ' GMT');

    }

  }

?>