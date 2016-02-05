<?php
class Query_Builder{
	var $main_table;
	private $select = array();
	var $join = array();
	var $where = array();
	var $groupby;
	var $order;
	var $limit;
	var $result;
	var $get;
	var $post;
	
	function __construct(){
		
	}
        
  function select($array) {
      $this->select = $array;
      return $this;
  }
	
	function set_main_table($table){
		$this->main_table = $table;
    return $this;
	}

	function select_add($tag, $q){
		$this->select[$tag] = $q;
    return $this;
	}
	
	function join($tag, $q){
		$this->join[$tag] = $q;
    return $this;
	}
	
	function remove_join($tag){
		unset($this->join['tag']);
    return $this;
	}
	
	function where($tag, $q){
		$this->where[$tag] = $q;
    return $this;
	}
  
  function search($tag, $q){
      if($_GET[$tag])
          $this->where($tag, $q);
      return $this;
  }	
	
	function groupby($q){
		$this->groupby = $q;
    return $this;
	}
	
	function order($q){
		$this->order = $q;
    return $this;
	}
  
  function remove($tag){
      unset($this->$tag);
      return $this;
  }
	function remove_where($tag){
		unset($this->where[$tag]);
    return $this;
	}
	
	function limit($q){
		$this->limit = $q;
    return $this;
	}
  
	function build(){ 
		$q['select'] = "select ".implode(", \n\t", $this->select)."\nfrom $this->main_table"; 
		$q['join'] = "\n".implode("\n", $this->join);
		
		if($this->where)
			$q['where'] = "where ".implode(' ', $this->where);
		
		if($this->groupby)
			$q['groupby'] = "group by $this->groupby";
    
		if($this->order)
			$q['order'] = "order by $this->order";
		
		if($this->limit)
			$q['limit'] = $this->limit;
		else
			$q['limit'] = "\nlimit 0, 10";
		
    $r = implode("\n", $q);
    //echo $r;
		return $r;
	}
}