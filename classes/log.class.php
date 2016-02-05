<?php
class Log{
	const TABLE = 'logs';
	var $limit = 10;
	var $pagename;
	var $user_id;
	
	public static function insert($pagename, $action, $user_id, $object_id = null){
		global $wpdb;
		$wpdb->insert(self::TABLE, array(
			'pagename'=>$pagename,
			'action'=>$action,
			'user_id'=>$user_id,
			'object_id'=>$object_id
		));
		
		return $wpdb->insert_id;
	}
	
	public function get(){
		global $wpdb;
		$table = self::TABLE;
		
		$q = "select log.*, users.display_name from $table log";
		
		$q .= " left join $wpdb->users users on users.ID = log.user_id";
		
		if($this->pagename){
			$q .= " where pagename = '$this->pagename' and action not like 'view'";
		}
		
		$q .= " order by datetime DESC";
		
		$q .= " limit 0, $this->limit";#echo $q;
		
		$r = $wpdb->get_results($q);
		
		return $r;
	}
	
}