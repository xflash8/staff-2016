<?php
class SMS_Inbound_Card extends Card{
	function __construct(){
		$this->handle_inbox();
		parent::__construct();
	}
	function handle_inbox(){
		if(!$_REQUEST['msisdn'])
			return;
		
		extract($_REQUEST);
		
		$data = array(
			'text'=>$text,
			'message_id'=>$messageId,
			'msisdn'=>$msisdn,
			'type'=>$type,
			'to'=>$to,
			'keyword'=>$keyword,
			'timestamp'=>$_REQUEST['message-timestamp']
		);
		
		$sms = new sms($data);
		$sms->save_inbound();
		header("HTTP/1.1 200 OK");
		exit;
	}
	function display_list(){
		echo 'ok';
	}
	
}