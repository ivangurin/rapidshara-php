<?php

  class db{

    const
      gc_connection_error = "Could not connect to database. Please send mail to administrator.\n",
      gc_select_db_error  = "Could not select database. Please send mail to administrator.\n",
      gc_select_codepage  = "Could not select codepage. Please send mail to administrator.\n";

    static private
      $go_db;

    private
      $gv_db,
      $gv_host,
      $gv_database,
      $gv_codepage = "UTF8",
      $gv_debug    = false;

    static function get_instance(){
      if(empty(db::$go_db))
        db::$go_db = new db();
      return db::$go_db;
    }

    public function __construct(){

    }

    public function __destruct() {
      if($this->gv_db)
        $this->disconnect();
    }

    public function __toString(){
    	if(mysql_ping() === true)
        return "Database " . $this->gv_database . " OK: " . $this->gv_db . "<br />\n";
      else
        return "Database " . $this->gv_database . "ERROR";
    }

    public function connect($iv_host, $iv_login, $iv_password, $iv_database, $iv_codepage = ""){

      $this->gv_host     = $iv_host;
      $this->gv_database = $iv_database;

      if(!empty($iv_codepage))
        $this->gv_codepage = $iv_codepage;

      $lv_result = mysql_connect($this->gv_host, $iv_login, $iv_password);
      if($lv_result === false) die(self::gc_connection_error);

      $this->gv_db = $lv_result;

      $lv_result = $this->select_database();
      if($lv_result === false) die(self::gc_select_db_error);

      $lv_result = $this->select_codepage();
      if($lv_result === false) die(self::gc_select_codepage);

      mysql_query("set auto_increment_increment = 1", $this->gv_db);
      mysql_query("set auto_increment_offset = 1", $this->gv_db);

    }

    public function query($iv_query){

      if($this->gv_debug) print_r("Request: " . $iv_query . "<br />\n");

    	$lv_result = mysql_query($iv_query, $this->gv_db);

      if($lv_result === false){
        if($this->gv_debug)
          print_r("Respons FALSE. Error: " . $this->error() . "<br /><br />\n\n");
        return false;
      }

      if($lv_result === true){
        if($this->gv_debug)
          print_r("Respons TRUE. " . $this->info() . "<br /><br />\n\n");
        return true;
      }

      if($this->gv_debug)
        print_r("Respons: " . $lv_result . "<br /><br />\n\n");

      if($this->rows($lv_result) == 0){
        return $lv_result;
      }else{
        $lt_response = array();
        while($ls_row = $this->fetch($lv_result)){
          $lt_response[] = $ls_row;
        }
        return($lt_response);
      }

    }

    public function single($iv_query){

      $lv_result = $this->query($iv_query);

      if(!is_array($lv_result))
        return($lv_result);
      else
        return($lv_result[0]);

    }

    public function get_id(){
    	return mysql_insert_id($this->gv_db);
    }

    public function debug_on(){
    	$this->gv_debug = true;
    }

    public function debug_off(){
    	$this->gv_debug = false;
    }

    public function info(){
      return mysql_info($this->gv_db);
    }

    public function error(){
      return mysql_error($this->gv_db);
    }

    public function disconnect(){
      return mysql_close($this->gv_db);
    }

    private function select_database(){
      return mysql_select_db($this->gv_database, $this->gv_db);
    }

    private function select_codepage(){
    	return mysql_query("SET NAMES " . $this->gv_codepage, $this->gv_db);
    }

    private function fetch($it){
      return mysql_fetch_assoc($it);
    }

    private function rows($it){
      return mysql_num_rows($it);
    }

    static function escape($iv_query){
      return mysql_escape_string(trim(strip_tags($iv_query)));
    }

  }
?>