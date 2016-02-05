<?php
add_action('init', function(){
	if(!$_GET['ajax'])
		return;
	
	$a = new Ajax_Handler();
	
});

class Ajax_Handler{
	public $method;
	
	function __construct(){
		$method = $_GET['ajax'];
		
		foreach($_REQUEST as $k => $v){
			$this->$k = $v;
		}
		
		if(method_exists($this, $method)){
			$r = $this->$method();
			$this->log();
			header('Content-Type: application/json');
			echo json_encode($r);
			exit;
		}
	}
	
	function log(){
		$action = $_REQUEST['ajax'];
		$pagename = $this->pagename;
		$object_id = $this->object_id;
		log::insert($pagename, $action, get_current_user_id(), $object_id);
	}
	
	function get_profile(){
		extract($_REQUEST);
		global $wpdb;
		if(strlen($uid) == 9){
			// by nim
			$q = "select user_id from $wpdb->usermeta where meta_key = '_nim_baru' and meta_value = $uid";
			$r = $wpdb->get_var($q);
			
			if($r){
				
				$angkatan = substr($uid, 0, 2);
				if($angkatan == 15){
					$type = 'maba';
				}else{
					$type = 'mala';
				}
				
				$uid = $r;
			
			
			}else{
				return array('status'=>'invalid uid', 'uid'=>$uid);
			}
		}
				
		$user = new WP_USER($uid);
		
		
		
		$fid = $user->foto_baru;
		switch_to_blog(14);
		$img = wp_get_attachment_image_src( $user->foto_baru, 'medium');
		if(!$img){
			$img = wp_get_attachment_image_src( $user->foto, 'medium');
		}
		
		restore_current_blog();
		
		$data = array( 
			'post'=>$_POST,
			'ID'=>$user->ID,
			'foto_baru'=>$img[0],
			'display_name'=>ucwords($user->display_name),
			'kabupaten_asal'=>$user->kabupaten_asal,
			'provinsi_asal'=>$user->provinsi_asal,
			'phone'=>$user->phone,
			'email'=>$user->email,
			'gender'=>$user->gender,
			'type'=>$type,			
		);
		return $data;
	}
	/* mutasi */
	function set_transaction_category(){
		extract($_REQUEST);
		if(!$user_id)
			return array('status'=>false, 'detail'=>'no user id');
		
		global $wpdb;
		
		if($this->category == 'spp')
			$category = $category . '_' .$periode;
		
		$data = array(
			'object_id'=>$user_id,
			'category'=>$category
		);
		
		$where = array(
			'ID'=>$ref_id
		);
		$wpdb->update('mutasi', $data, $where);
		
		if($this->category == 'spp')
			update_user_meta($user_id, '_'.$periode.'_status', 'active');
		
		return array('post'=>$_POST);
	}
	
	function cancel_transaction(){
		extract($_REQUEST);
		global $wpdb;
		
		if(!$id)
			return array('status'=>false, 'detail'=>'no user id');
		
		$wpdb->update('mutasi', array('category'=>'', 'object_id'=>null), array('ID'=>$id));
		
		$q = "select * from mutasi where ID = $id";
		$r = $wpdb->get_row($q);
		return $r;
	}
	
	function resync_mutasi(){
		$b = new botnet_bsm($this->mth, $this->yr);
		$r = $b->sync();
		
		return array('req'=>$_REQUEST,'r'=>$r);
	}
	
	/* mahasiswa */
	function update_spp(){
		extract($_REQUEST);
		
		if(!$uid)
			return array('status'=>false, 'detail'=>'no user id');
		
		update_user_meta($uid, '_spp', $spp);
		return array('post', $_POST);
	}
	
	function set_alumni(){
		extract($_REQUEST);
		
		if(!$user_id)
			return array('status'=>false, 'detail'=>'no user id');
		
		update_user_meta($user_id, '_2015_1_status', 'alumni');
		return array('post', $_POST);
	}
	
	function set_maba(){
		extract($_REQUEST);
		
		if(!$user_id)
			return array('status'=>false, 'detail'=>'no user id');
		
		update_user_meta($user_id, '_2015_1_maba', 1);
		return array('post', $_POST);
	}
	function remove_user(){
		extract($_REQUEST);
		
		if(!$user_id)
			return array('status'=>false, 'detail'=>'no user id');
		
		global $wpdb;
		$r1 = $wpdb->delete($wpdb->users, array('ID'=>$user_id));
		$r2 = $wpdb->delete($wpdb->usermeta, array('user_id'=>$user_id));
		$wpdb->update('mutasi', array('category'=>'', 'object_id'=>null), array('object_id'=>$user_id));
		return array('post'=>$_REQUEST, 'status'=>$r1, 'status2'=>$r2);
	}
	
	/* bank mahasiswa */
	function create_mahasiswa(){
		extract($_REQUEST);
		if(!$nim)
			return array('status'=>false, 'detail'=>'no nim');
		
		global $wpdb;

		$wpdb->insert($wpdb->users, array(
			'display_name'=>$display_name,
			'user_nicename'=>$display_name
		));
		
		$user_id = $wpdb->insert_id;
		
		if($user_id){
			update_user_meta($user_id, '_nim_baru', $nim);
			update_user_meta($user_id, 'tempat_lahir', $tempat_lahir);
			update_user_meta($user_id, 'gender', $gender);
			
			$t = explode('/', $tanggal_lahir);
			if(strlen($t[0]) == 1){
				$t[0] = '0'.$t[0];
			}
			
			if(strlen($t[1]) == 1){
				$t[1] = '0'.$t[1];
			}
			
			update_user_meta($user_id, 'tanggal_lahir', $t[2] . '-' . $t[1] . '-' . $t[0]);
			
			return array('request'=>$_REQUEST, 'user_id'=>$user_id);
		}else{
			return array('status'=>false, 'desc'=>'failed to create user');
		}
		
	}
	
	function update_nim(){
		extract($_REQUEST);
		
		if(!$user_id)
			return array('status'=>false, 'desc'=>'no user_id');
		
		$nim_lama = get_user_meta($user_id, '_nim_baru', true);
		update_user_meta($user_id, '_nim_lama', $nim_lama);
		update_user_meta($user_id, '_nim_baru', $nim);
		
		return array('request'=>$_REQUEST);
		
	}
	
	
	function remove_nim(){
		extract($_REQUEST);
		
		if(!$user_id)
			return array('status'=>false, 'desc'=>'no user_id');
		
		global $wpdb;
		$wpdb->update($wpdb->usermeta, array('meta_key'=>'_nim_lama'), array('user_id'=>$user_id, 'meta_key'=>'_nim_baru'));
		
		return array('request'=>$_REQUEST);
		
	}
	/* group function */
	function member_add(){
		extract($_REQUEST);
		$user = new WP_User($user_id);
		$user->add_cap('view_'.$pagename);
		
		$this->object_id = $user_id;
		$this->pagename = $pagename;
		
		
		$sender = new WP_USER(get_current_user_id());
		$to = $user->phone;
		$text = "Yth $user->display_name, Anda baru saja ditambahkan di halaman $pagename oleh $sender->display_name.\n\nstaff.stiba.ac.id";
		$sms = new sms;
		$text .= "\nSMSC $sms->smsc\nkeyword: help";
		$r = $sms->send($user->phone, $text, 'NXSMS');
		
		return array('request'=>$_REQUEST, 'sms'=>$r);
	}
	
	function promote_member(){
		extract($_REQUEST);
		$user = new WP_User($user_id);
		$user->add_cap('edit_'.$pagename);
		
		$this->object_id = $user_id;
		$this->pagename = $pagename;
		
		$sender = new WP_USER(get_current_user_id());
		$to = $user->phone;
		$text = "Yth $user->display_name, sekarang Anda dijadikan admin di halaman $pagename oleh $sender->display_name.\n\nstaff.stiba.ac.id";
		$sms = new sms;
		$text .= "\nSMSC $sms->smsc\nkeyword: help";
		$r = $sms->send($user->phone, $text, 'NXSMS');
		
		return array('request'=>$_REQUEST, 'sms'=>$r);
	}
	
	function member_remove(){
		extract($_REQUEST);
		$user = new WP_User($user_id);
		
		/* admin shouldn't downgrade other admin. Only super user can do that */
		if($user->has_cap('edit_'.$pagename)){
			if(!current_user_can('manage_sites'))
				return array('status'=>'failed', 'desc'=>'admin cannot remove another admin');
		}
		
		$user->remove_cap('view_'.$pagename);
		$user->remove_cap('edit_'.$pagename);
		
		$this->object_id = $user_id;
		$this->pagename = $pagename;
		return array('request'=>$_REQUEST);
	}
	
	function member_downgrade(){
		extract($_REQUEST);
		$user = new WP_User($user_id);
		
		/* admin shouldn't downgrade other admin. Only super user can do that */
		if($user->has_cap('edit_'.$pagename)){
			if(!current_user_can('manage_sites'))
				return array('status'=>'failed', 'desc'=>'admin cannot remove another admin');
		}
		$user->remove_cap('edit_'.$pagename);
		
		$this->object_id = $user_id;
		$this->pagename = $pagename;
		return array('request'=>$_REQUEST);
	}
	
	/* pegawai */
	function update_data_pegawai(){
		global $wpdb;
		
		$data = array(
			'display_name'=>$this->display_name,
			'user_nicename'=>$this->display_name,
			'user_email'=>$this->user_email,
		);
		$where = array('ID'=>$this->user_id);
		$wpdb->update($wpdb->users, $data, $where);
		
		/* usermeta */
		$usermeta = array('phone', 'gender');
		foreach($usermeta as $v){
			update_user_meta($this->user_id, $v, $this->$v);
		}
		
		return array('request'=>$_REQUEST, 'data'=>$data, 'where'=>$where);
	}
	
	function send_sms(){
		$sender = new WP_USER(get_current_user_id());
		
		$user = new WP_User($this->user_id);
		$sms = new sms;
		if($user->ID == $sender->ID){
			$this->text .= "\n\nBy Anda sendiri :(";
		}else{
			$this->text .= "\n\nBy $sender->display_name $sender->phone";
		}
		
		$r = $sms->send($user->phone, $this->text, 'NXSMS');
		return array('request'=>$_REQUEST, 'r'=>$r, 'text'=>$this->text);
	}
	function send_sms_pegawai(){
		global $wpdb;
		$q = "
			select 
				meta.user_id ID,
				meta2.meta_value phone
			from $wpdb->usermeta meta
			
			left join $wpdb->usermeta meta2
			on meta2.user_id = meta.user_id and meta2.meta_key = 'phone'
			
			left join $wpdb->usermeta meta3
			on meta3.user_id = meta.user_id and meta3.meta_key = 'gender'
			
			where meta.meta_key = '".$wpdb->prefix."capabilities' and meta2.meta_value is not null
		";
		if($this->gender){
			$q .= " and meta3.meta_value = $this->gender";
		}
		
		$phone = $wpdb->get_results($q);
		if($phone){
			$sender = new WP_USER(get_current_user_id());
			$sms = new sms;
			
			foreach($phone as $v){				
				if($v->ID == $sender->ID){
					$this->text .= "\n\nBy Anda sendiri :(";
				}else{
					$this->text .= "\n\nBy $sender->display_name $sender->phone";
				}
				
				$r[] = $sms->send($v->phone, $this->text, 'NXSMS');
			}
		}
		return $r;
	}
	/* registrasi */
	function add_mahasiswa_penundaan(){
		extract($_REQUEST);
		if(!$user_id)
			return array('status'=>false, 'detail'=>'no user id');
		
		global $wpdb;
		
		update_user_meta($user_id, '_'.$periode.'_status', 'active');
		
		return array('post'=>$_POST);
	}
	function validasi_spp(){
		global $wpdb;
		
		$periode = '2015_2';
		$category = 'spp_'.$periode;
		
		$data = array(
			'object_id'=>$this->user_id,
			'category'=>$category
		);
		
		$where = array(
			'ID'=>$this->mutasi_id
		);
		$wpdb->update('mutasi', $data, $where);
		
		update_user_meta($this->user_id, '_'.$periode.'_status', 'active');
		
		return array('data'=>$data, 'where'=>$where);
	}
	function set_kelas(){
		foreach($this->cb as $user_id){
			update_user_meta($user_id, $this->periode.'_prodi', $this->prodi);
			update_user_meta($user_id, $this->periode.'_semester', $this->semester);
			update_user_meta($user_id, $this->periode.'_room', $this->room);
		}
		
		return array('data'=>$_POST);
		
	}
}