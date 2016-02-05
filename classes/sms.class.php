<?php
class sms{
	var $provider;
	var $obj;
	var $message;
	var $smsc = '085574670889';
	var $auto_responder_keywords = array(
		'password', 'saya', 'nim', 'help'
	);
	function __construct($sms_data = array()){
		$this->data = $sms_data;
		foreach($sms_data as $k => $v){
			$this->$k = $v;
		}
		
		switch($provider){
			default:
				include get_template_directory().'/classes/nexmo.class.php';
				$this->obj = new Nexmo;
			 break;
		}
	}
	function send($to, $text, $from=null){
		if(!$to || strlen($to) < 10)
			return 'error: nomor gak valid';
		
		$r = $this->obj->send_sms($to, $text, $from);
		
		if(is_array($r->messages)){
			foreach($r->messages as $m){
				$data = array(
					'to'=>$m->to,
					'message-id' => $m->{'message-id'},
					'message_id' => $r->message_id,
					'status' => $m->status,
					'cost' => $m->{'message-price'},
					'text'=>$text,
				);
				$r->tes[] = $this->save_outbound($data);
			}
		}
		
		return $r;
	}
	function save_inbound(){
		global $wpdb;
		$wpdb->insert('sms_inbound', $this->data);
		$id = $wpdb->insert_id;
		$this->auto_responder();
		return $id;
	}
	function save_outbound($data){
		global $wpdb;
		$r = $wpdb->insert('sms_outbound', $data);
		$id = $wpdb->insert_id;
		return $id;
	}
	
	function auto_responder(){
		$keyword = strtolower($this->keyword);		
		if(in_array($keyword, $this->auto_responder_keywords)){
			$this->get_user($this->msisdn);
			$method = 'responder_'.$keyword;
			$message = $this->$method();
			
			$message .= "\n\nstaff.stiba.ac.id\nSMSC $this->smsc";
			$this->send($this->msisdn, $message, 'NXSMS');
		}
	}
	function get_user($phone){
		global $wpdb;
		$phone = str_replace('62', '0', $phone);
		
		$q = "select user_id from $wpdb->usermeta where meta_key = 'phone' and meta_value='$phone'";
		$r = $wpdb->get_var($q);
		if(is_numeric($r)){
			$user = new WP_User($r);
			$this->user = $user;
			return $user;
		}else{
			return false;
		}
	}
	
	function responder_password(){
		$password = wp_generate_password();
		wp_set_password( $password, $this->user->ID );
		$text = "username: ".$this->user->user_login;
		$text .= "\nPassword ".$password;
		return $text;
	}
	function responder_saya(){
		$gender[1] = 'Laki-laki';
		$gender[2] = 'Perempuan';
		
		$text []= "Nama: ".$this->user->display_name;
		$text []= "Email: ".$this->user->user_email;
		$text []= "Hp: ".$this->msisdn;
		$text []= "JK: ".$gender[$this->user->gender];
		print_r($text);
		return implode("\n",$text);
	}
	function responder_nim(){
		$text = explode(" ", $this->data['text']);print_r($text);
		$nim = $text[1];
		
		global $wpdb;
		$q = "select user_id from $wpdb->usermeta where meta_key = '_nim_baru' and meta_value = '$nim'";
		$user_id = $wpdb->get_var($q);
		if(is_numeric($user_id)){
			$gender[1] = 'Laki-laki';
			$gender[2] = 'Perempuan';
			
			$m = new WP_User($user_id);
			$text [] = "Nama: ".$m->display_name;
			$text [] = "TTL: ".$m->tempat_lahi.", ".$m->tanggal_lahir;
			$text [] = "JK: ".$gender[$m->gender];
			$text [] = "Hp: ".$m->phone;
			$text = implode("\n",$text);
		}else{
			$text = 'Tidak ada mahasiswa dengan nim tersebut';
		}
		
		return $text;
	}
	
	function responder_help(){
		if($this->user){	
			
			$text [] = "Yth ".$this->user->display_name." kirim sms dengan keyword berikut ke SMSC\n";
			$text [] = "saya: data anda di sistem";
			$text [] = "password: reset password";
			$text = implode("\n",$text);
		}else{
			$text = "Maaf,nomor anda tidak terdaftar";
		}
		return $text;
	}
}