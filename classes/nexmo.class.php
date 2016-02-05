<?php
class Nexmo{
	function __construct(){
		
	}
	function send_sms($to, $text, $from='nxsms'){
		if(!$from)
			$from = $this->number;
			
	$to = preg_replace("/^0/", '62', $to);
	$api_key = 'aecda205';
	$api_secret = '6daf5e5a';
	$text = urlencode(stripslashes($text));
	$base_url = 'https://rest.nexmo.com/sms/json';
	$par = "?api_key=$api_key&api_secret=$api_secret&from=$from&to=$to&text=$text";
	$url = $base_url.$par;
	$r = wp_remote_get($url, array('sslverify'=>false));
	$json = wp_remote_retrieve_body($r);
	$data = json_decode($json);
	
	return $data; 
}
}