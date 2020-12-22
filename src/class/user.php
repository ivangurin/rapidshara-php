<?php

//-----------------------------------------------------------------------------
// User manager class
//-----------------------------------------------------------------------------
  class user_manager{

    static private
      $go_manager;

    private
      $go_db,
      $gt_users = array(),
      $gv_number = 0;

    static function get_instance(){
      if(empty(user_manager::$go_manager))
        user_manager::$go_manager = new user_manager();
      return user_manager::$go_manager;
    }

    public function __construct(){
      $this->go_db = db::get_instance();
    }

    public function __destruct(){

    }

    // Create
    public function create(){

      $lo_user = new user();

      $lv_id = $this->get_number_next();

      $this->gt_users[$lv_id] = $lo_user;

      return $lo_user;

    }

    // Get by ID
    public function get_by_id($iv_id){

      if(empty($iv_id))
        return false;

      if(isset($this->gt_users[$iv_id])){

        return $this->gt_users[$iv_id];

      }else{

        $lo_user = new user($iv_id);

        if(!$lo_user->exist())
          return false;

        $this->gt_users[$iv_id] = $lo_user;

        return $lo_user;

      }

    }

    // Get user by SID
    public function get_by_sid($iv_sid = ""){

      if(!empty($iv_sid))
        $lv_sid = $iv_sid;
      else
        $lv_sid = user_service::get_sid();

      if(empty($lv_sid))
        return false;

      $lt_users = $this->go_db->query("select id from users where sid = '$lv_sid'");

      if(!is_array($lt_users))
        return false;

      if(count($lt_users) != 1)
        return false;

      $lo_user = $this->get_by_id($lt_users[0]["id"]);

      if($lo_user->is_deleted())
        return false;

      return $lo_user;

    }

    // Get
    public function get(){
      return $this->gt_users;
    }

    // Set
    public function set($it_users){
      $this->gt_users = $it_users;
    }

    // Authorization
    public function authorize($iv_email, $iv_password){

      $lv_email    = $iv_email;
      $lv_password = $iv_password;

      if(empty($lv_email) || user_service::check_email($lv_email) === false)
        return 1;

      if(empty($lv_password) || user_service::check_password($lv_email, $lv_password) === false)
        return 2;

      $lt_users = $this->go_db->query("select id from Users where Email = '$lv_email' and Password = '$lv_password'");

      if(!is_array($lt_users))
        return false;

      if(count($lt_users) != 1)
        return false;

      $lo_user = $this->get_by_id($lt_users[0]["id"]);

      if($lo_user->is_deleted())
        return false;

      $lo_user->login();

      return 0;

    }

    // Save
    public function save(){

      foreach($this->gt_users as $lv_id => $lo_user){

        $lo_user->save();

        if(substr($lv_id, 0, 1) == "$"){
          $lv_id_new = $lo_user->get_id();
          $this->gt_users[$lv_id_new] = $lo_user;
          unset($this->gt_users[$lv_id]);
        }

      }

    }

    // Get number next
    private function get_number_next(){
      $this->gv_number++;
      return "$" . $this->gv_number;
    }

  }

//-----------------------------------------------------------------------------
// User class
//-----------------------------------------------------------------------------
  class user{

    private
      $go_db,
      $gs_user,
      $gv_exist,
      $gv_updkz;

    public function __construct($iv_id = ""){

      $this->go_db = db::get_instance();

      if(empty($iv_id)){

        $this->gv_updkz                       = "I";

        $this->gs_user["ID"]                  = "";
        $this->gs_user["Name"]                = "";
        $this->gs_user["Email"]               = "";
        $this->gs_user["Status"]              = "";
        $this->gs_user["Password"]            = "";
        $this->gs_user["Size"]                = 0;
        $this->gs_user["Keep"]                = 0;
        $this->gs_user["Deleted"]             = 0;
        $this->gs_user["Date_Created"]        = date("YmdHis");
        $this->gs_user["Date_Changed"]        = "";
        $this->gs_user["Date_Deleted"]        = "";
        $this->gs_user["Date_Entry"]          = "";
        $this->gs_user["Date_Exit"]           = "";
        $this->gs_user["SID"]                 = "";

      }else{

        return $this->get_by_id($iv_id);

      }

    }

    // Get user by ID
    public function get_by_id($iv_id){

      $lt_users = $this->go_db->query("select * from Users where ID = $iv_id");

      if(!is_array($lt_users))
        return false;

      if(count($lt_users) != 1)
        return false;

      $this->gs_user = $lt_users[0];

      $this->gv_exist = true;

      return true;

    }

    // Get ID
    public function get_id(){
      return $this->gs_user["ID"];
    }

    // Get name
    public function get_name(){
      return $this->gs_user["Name"];
    }

    // Get email
    public function get_email(){
      return $this->gs_user["Email"];
    }

    // Get password
    public function get_password(){
      return $this->gs_user["Password"];
    }

    // Get password
    public function get_size(){
      return $this->gs_user["Size"];
    }

    // Get sid
    public function get_sid(){
      return $this->gs_user["SID"];
    }

    // Get url
    public function get_url(){
      return "http://" . $_SERVER["HTTP_HOST"]   . "/profile/" .
                         $this->gs_user["ID"];
    }

    // Get
    public function get(){
      return $this->gs_user;
    }

    // Set
    public function set($is_user){

      if($this->gv_updkz == "D")
        return false;

      if($this->gs_user == $is_user)
        return true;

      if(empty($this->gv_updkz))
        $this->gv_updkz = "U";

    	$this->gs_user = $is_user;

      if($this->gv_updkz == "U")
        $this->gs_user["Date_Changed"] = date("YmdHis");

      return true;

    }

    // Delete
    public function delete(){

      if($this->gs_user["Deleted"])
        return true;

      if($this->gv_updkz == "D")
        return true;

      $this->gv_updkz                 = "D";

      $this->gs_user["Deleted"]       = "1";
      $this->gs_user["Date_Deleted"]  = date("YmdHis");

      return true;

    }

    // Save
    public function save(){

      // Insert
      if($this->gv_updkz == "I"){

        $lv_query = "insert into Users (  Name
                                        , Email
                                        , Password
                                        , SID
                                        , Deleted
                                        , Date_Created";

        if(!empty($this->gs_user["Date_Entry"]))
          $lv_query .= ", Date_Entry";

        $lv_query .= ")";

        $lv_query .= "value(  '" . $this->gs_user["Name"]                . "'
                            , '" . $this->gs_user["Email"]               . "'
                            , '" . $this->gs_user["Password"]            . "'
                            , '" . $this->gs_user["SID"]                 . "'
                            , '" . $this->gs_user["Deleted"]             . "'
                            , '" . $this->gs_user["Date_Created"]        . "'";

        if(!empty($this->gs_user["Date_Entry"]))
          $lv_query .= ", '" . $this->gs_user["Date_Entry"]              . "'";

        $lv_query .= ")";

        $this->go_db->query($lv_query);

        $this->gs_user["ID"] = $this->go_db->get_id();

      }

      // Update
      if($this->gv_updkz == "U" && $this->gs_user["ID"] != 0){

        $lv_query  = " update Users set";
        $lv_query .= "  Name = '" . $this->gs_user["Name"] . "'";
        $lv_query .= ", Email = '" . $this->gs_user["Email"] . "'";
        $lv_query .= ", Password = '" . $this->gs_user["Password"] . "'";
        $lv_query .= ", Deleted = '" . $this->gs_user["Deleted"] . "'";

        if(!empty($this->gs_user["Date_Created"]))
          $lv_query .= ", Date_Created = '" . $this->gs_user["Date_Created"] . "'";
        if(!empty($this->gs_user["Date_Changed"]))
          $lv_query .= ", Date_Changed = '" . $this->gs_user["Date_Changed"] . "'";
        if(!empty($this->gs_user["Date_Deleted"]))
          $lv_query .= ", Date_Deleted = '" . $this->gs_user["Date_Deleted"] . "'";
        if(!empty($this->gs_user["Date_Entry"]))
          $lv_query .= ", Date_Entry = '" .   $this->gs_user["Date_Entry"]   . "'";
        if(!empty($this->gs_user["Date_Exit"]))
          $lv_query .= ", Date_Exit =  '" .   $this->gs_user["Date_Exit"]    . "'";

        if(!empty($this->gs_user["SID"]))
          $lv_query .= ", SID = '" . $this->gs_user["SID"] . "'";
        else
          $lv_query .= ", SID = Null";

        $lv_query .= " where ID = '" . $this->gs_user["ID"] . "'";

        $this->go_db->query($lv_query);

      }

      // Delete
      if($this->gv_updkz == "D"  && $this->gs_user["ID"] != 0){

        $lv_query  = "update Users set";
        $lv_query .= "  Deleted       = '" . $this->gs_user["Deleted"]        . "'";
        $lv_query .= ", Date_Deleted  = '" . $this->gs_album["Date_Deleted"]  . "'";
        $lv_query .= " where ID = '" . $this->gs_user["ID"] . "'";

        $this->go_db->query($lv_query);

      }

      $this->gv_updkz = "";

    }

    // Login
    public function login(){

      if($this->gv_updkz == "D" || $this->gs_user["Deleted"])
        return false;

      if(empty($this->gv_updkz))
        $this->gv_updkz = "U";

      $lv_sid = md5("l" . $_SERVER["REMOTE_ADDR"] . time());

      $this->gs_user["SID"]        = $lv_sid;
      $this->gs_user["Date_Entry"] = date("YmdHis");

      user_service::set_sid($lv_sid);

      return true;

    }

    // Logout
    public function logout(){

      if(empty($this->gv_updkz))
        $this->gv_updkz = "U";

      $this->gs_user["SID"]       = "";
      $this->gs_user["Date_Exit"] = date("YmdHis");

      user_service::logout();

      return true;

    }

    // Check for exist
    public function exist(){
      return $this->gv_exist;
    }

    // Is deleted
    public function is_deleted(){
	    if($this->gv_updkz == "D" || $this->gs_user["Deleted"])
        return true;
      else
        return false;
    }

    // Is root
    public function is_root(){
	    if($this->gs_user["Status"] == "root")
        return true;
      return false;
    }

    // Is keep
    public function is_keep(){
	    return $this->gs_user["Keep"];
    }

  }

//-----------------------------------------------------------------------------
// User class
//-----------------------------------------------------------------------------
  class user_service{

    // Check email
    static function check_email($iv_email, $iv_id = ""){

      $lo_db = db::get_instance();

      if(empty($iv_id))
        $lt_users = $lo_db->query("select id from Users where Email = '$iv_email' and Deleted = 0");
      else
        $lt_users = $lo_db->query("select id from Users where Email = '$iv_email' and ID <> '$iv_id' and Deleted = 0");

      if(!is_array($lt_users))
        return false;

      if(count($lt_users) == 0)
        return false;

      return true;

    }

    // Check password
    static function check_password($iv_email, $iv_password){

      $lo_db = db::get_instance();

      $lt_users = $lo_db->query("select id from Users where Email = '$iv_email' and Password = '$iv_password' and Deleted = 0");

      if(!is_array($lt_users))
        return false;

      if(count($lt_users) != 1)
        return false;

      return true;

    }

    // Get SID
    static function get_sid(){
      if(isset($_COOKIE["SID"]))
        return db::escape($_COOKIE["SID"]);
    }

    // Set SID
    static function set_sid($iv_sid){
      setcookie("SID", $iv_sid, time()+60*60*24*30, "/", ".rapidshara.ru");
    }

    // Logout
    static function logout(){
    	setcookie("SID", "expired", time()-60*60*24*30, "/", ".rapidshara.ru");
    }

  }

?>