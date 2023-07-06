<?php
	function httpPost($url,$params){
		$postData = '';
		foreach($params as $k => $v) { 
			$postData .= $k . '='.urlencode($v).'&'; 
		}
		$postData = rtrim($postData, '&'); 
		$ch = curl_init();   
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded;utf-8'));
		curl_setopt($ch, CURLOPT_POST, count($postData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);		 
		$output=curl_exec($ch); 
		curl_close($ch);    
		return $output;
	}	
	function makeSignature($mbrNo,$mbrRefNo,$amount,$apiKey,$timestamp) {		
		$message = $mbrNo ."|".$mbrRefNo."|".$amount."|".$apiKey."|".$timestamp;
		return hash("sha256", $message);	
	}
	
	function makeTimestamp() {	
		date_default_timezone_set('Asia/Seoul');
		return date_create('now')->format('YmdHis') . generateRandomString();				
	}
	function generateRandomString($length = 4) {
	    return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
	}
		
	function makeMbrRefNo($prefix) {
		return uniqid($prefix);
	}
	
	function pintLog($msg, $path){
		date_default_timezone_set('Asia/Seoul');
		$datetime = date_create('now')->format('Y-m-d H:i:s.u');
		$msg = "[".$datetime."] ".$msg."\n";
		error_log ($msg, 3, $path);
	}
?>