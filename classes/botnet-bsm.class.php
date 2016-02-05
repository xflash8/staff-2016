<?php
class Botnet_BSM{
	var $success_login;
	function __construct($month = null, $year=null){
		$this->rek = get_option('nomor_rekening_spp');
		$this->username = get_option('username_rekening_spp');
		$this->password = get_option('password_rekening_spp');
		if($month){
			if(strlen($month) == 1)
				$month = '0'.$month;
			$this->month = $month;
		}else{
			$this->month = date('m');
		}
		
		if($year){
			if(strlen($year) == 1)
				$year = '0'.$year;
			
			$this->year = $year;
		}else{
			$this->year = date('Y');
		}
	}
	
	function sync(){
		$rek = $this->rek;
		$username = $this->username;
		$password = $this->password;
		
		
		$url = 'https://bsmnet.syariahmandiri.co.id/cms/index.php' ;
		$cookie = 'PHPSESSID=8vetskonmnhec5a78rmom7n3c6; HTTPS=s';
		 
		$data = array(
			'sslverify'=>true,
			'timeout' => 120, 
			'headers' => array(
				'Cookie'=> $cookie,
				'Referer'=>'https://bsmnet.syariahmandiri.co.id/cms/index.php',
			),
		);
		
		#buka halaman login
		$r = wp_remote_get($url, $data);
		
		
		$txt = wp_remote_retrieve_body($r);
		$this->login_form = $txt;
		
		$x = preg_match_all('/type="text" name="(.*?)"/', $txt, $m1);
		$y = preg_match_all('/type="password" name="(.*?)"/', $txt, $m2);
		$username_field = $m1[1][0];
		$password_field = $m2[1][0];
		
		#login
		
		if(!$username_field || !$password_field){
			$this->success_login = true;
		}else{
			$this->success_login = false;
			$data = array(
				'sslverify'=>true,
				'timeout' => 120,
				'headers' => array(
					'Cookie'=> $cookie,
				),
				'body' => array( 
					$username_field => $username, 
					$password_field => $password,
					'cmd' => 'LOGIN_PROC' 
				),
			);
			
			$r = wp_remote_post( $url, $data);
			
			#print_r($data);print_r($r);
			#exit;
		}
		
		if ( is_wp_error( $r) ) {
			 $error_message = $response->get_error_message();
			 echo "Something went wrong: $error_message";
		} else {
			 
		# Always get current month from start to end
		$DATE_FROM_DD = '01'; 
		$DATE_FROM_MM = $this->month;
		$DATE_FROM_YY = $this->year;
		$DATE_UNTIL_DD = '31';
		$DATE_UNTIL_MM = $this->month;
		$DATE_UNTIL_YY = $this->year;
		
			 #post request
			$data = array(
				'sslverify'=>false,
				'timeout' => 120,
				'headers' => array(
					'Cookie'=> $cookie,
				),
				'body' => array( 
					'DATE_FROM_DD' => $DATE_FROM_DD, 
					'DATE_FROM_MM' => $DATE_FROM_MM,
					'DATE_FROM_YY' => $DATE_FROM_YY,
					'DATE_UNTIL_DD' => $DATE_UNTIL_DD, 
					'DATE_UNTIL_MM' => $DATE_UNTIL_MM,
					'DATE_UNTIL_YY' => $DATE_UNTIL_YY, 
					'DATA_DOWNLOAD' => 1, 
					'cmd' => 'CMD_REK_TAB_TRN_EXE', 
					'MY_ACC' => $rek, 
					'BAL' => '', 
					'CUR' => 'IDR', 
				),
			);
			$r = wp_remote_post( $url, $data);
			#print_r($r);
			
			# txt file
			$url = 'https://bsmnet.syariahmandiri.co.id/cms/DATA/'.$username.'_'.$rek.'%20%20_'.$DATE_FROM_YY.$DATE_FROM_MM.$DATE_FROM_DD.'_'.$DATE_UNTIL_YY.$DATE_UNTIL_MM.$DATE_UNTIL_DD.'.txt';
			
			#echo $url;
			
			
			
			$data = array(
				'sslverify'=>true,
				'timeout' => 120,
				'headers' => array(
					'If-Modified-Since'=> 'Sat, 23 May 2015 22:18:35 GMT',
					'Cookie'=> $cookie,
					'Referer'=> 'https://bsmnet.syariahmandiri.co.id/cms/index.php',

				),
			);
			
			$r = wp_remote_get($url, $data);
			
			$txt = wp_remote_retrieve_body($r);
			
			#logout before continue processsing results
			#echo '<br />logout';
			$r = wp_remote_get('https://bsmnet.syariahmandiri.co.id/cms/index.php?cmd=CMD_LOGOUT');
			
			
			$codes = $this->get_db();
			$a = explode("\n", $txt);
			$a = array_filter($a);
			foreach($a as $v){
				 $n = explode('|', $v);
				 $e = array(
					'date'=>substr($n[6], 0, 4).'-'.substr($n[6], 4, 2).'-'.substr($n[6], 6, 2).' '.$n[1].':00',
					'code'=>$n[2],
					'description'=>$n[3],
					'debit'=>0,
					'credit'=>0,
					'rekening' => $rek,
				 );
				 
				 $dk = explode(' ', $n[4]);
				 if($dk[0] == 'D'){
					$e['debit'] = $dk[1];
				 }else{
					$e['credit'] = $dk[1];
				 }
				 
				 $d[] = $e;
				 
				 if(!in_array($n[2], $codes)){
					 if($e['credit'] > 0){
						$new_credit[] = $e;
						$insert_id[] = $this->insert($e);
					 }else{
						 
					 }
				 }
				 
				 
			}			
			
			return array(
				'username'=>$this->username,
				'password'=>$this->password,
				'login_form'=>$this->login_form,
				'success_login'=>$this->success_login,
				'total'=>$current_count,
				'raw'=>$a,
				'data'=>array_reverse($d, true),
				'db'=>$db,
				'new_credit'=>$new_credit,
				'insert_id'=>$insert_id,
			);
		}
	}
	function get_db(){
		/* $db  */
		global $wpdb;
		$q = "select code from mutasi where month(date) = $this->month and year(date) = $this->year";
		$r = $wpdb->get_col($q);
		return $r;
	}
	
	function insert($mutasi){
		global $wpdb;
		$wpdb->insert('mutasi', $mutasi);
		return $wpdb->insert_id;
	}
}