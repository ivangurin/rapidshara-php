<?php

//-----------------------------------------------------------------------------
// Navigation class
//-----------------------------------------------------------------------------
  class navigation{

    private
      $gv_path,
      $gv_page_first,
      $gv_page_prev,
      $gv_page,
      $gv_page_next,
      $gv_page_last,
      $gv_all,
      $gv_rows,
      $gv_offset;


    public function __construct($iv_path, $iv_page = 1, $iv_all, $iv_rows = 100){

      $this->gv_path = $iv_path;
      $this->gv_page = $iv_page;
      $this->gv_all  = $iv_all;
      $this->gv_rows = $iv_rows;

      // Calc offset
      $this->gv_offset = $iv_page * $iv_rows - $iv_rows;

      // Set first page
      $this->gv_page_first = 1;

      // Set previous page
      if($iv_page > $this->gv_page_first)
        $this->gv_page_prev  = $iv_page - 1;
      else
        $this->gv_page_prev = $this->gv_page_first;

      // Set last page
      $this->gv_page_last  = ceil( $iv_all / $iv_rows );

      // Set next page
      if($iv_page < $this->gv_page_last)
        $this->gv_page_next  = $iv_page + 1;
      else
        $this->gv_page_next = $this->gv_page_last;

    }

    public function get_offset(){
      return $this->gv_offset;
    }

    public function get_rows(){
      return $this->gv_rows;
    }

    public function get(){

      $lv_navigation = "&#8592; ";

      if($this->gv_page_first != $this->gv_page){
        $lv_navigation .= "<a class=\"menu\" href=\"" . $this->gv_path . "/page" . $this->gv_page_first . "\" title=\"Первая  (Ctrl + &#8593;)\">Первая</a>";
        $lv_navigation .= "<link rel=\"up\" id=\"link_up\" href=\"". $this->gv_path . "/page" . $this->gv_page_first . "\" />";
      }else{
        $lv_navigation .= "Первая";
      }

      $lv_navigation .= " | ";

      if($this->gv_page_prev != $this->gv_page){
        $lv_navigation .= "<a class=\"menu\" href=\"" . $this->gv_path . "/page" . $this->gv_page_prev . "\" title=\"Предыдущая (Ctrl + &#8592;)\">Предыдущая</a>";
        $lv_navigation .= "<link rel=\"left\" id=\"link_left\" href=\"". $this->gv_path . "/page" . $this->gv_page_prev . "\" />";
      }else{
        $lv_navigation .= "Предыдущая";
      }

      $lv_navigation .= " | ";

      $lv_navigation .= $this->gv_page;

      $lv_navigation .= " | ";

      if($this->gv_page_next != $this->gv_page){
        $lv_navigation .= "<a class=\"menu\" href=\"" . $this->gv_path . "/page" . $this->gv_page_next . "\" title=\"Следующая (Ctrl + &#8594;)\">Следующая</a>";
        $lv_navigation .= "<link rel=\"right\" id=\"link_right\" href=\"". $this->gv_path . "/page" . $this->gv_page_next . "\" />";
      }else{
        $lv_navigation .= "Следующая";
      }

      $lv_navigation .= " | ";

      if($this->gv_page_last != $this->gv_page){
        $lv_navigation .= "<a class=\"menu\" href=\"" . $this->gv_path . "/page" . $this->gv_page_last . "\" title=\"Последняя (Ctrl + &#8595;)\">Последняя</a>";
        $lv_navigation .= "<link rel=\"down\" id=\"link_down\" href=\"". $this->gv_path . "/page" . $this->gv_page_last . "\" />";
      }else{
        $lv_navigation .= "Последняя";
      }

      $lv_navigation .= " &#8594";

      return $lv_navigation;

    }

  }

?>