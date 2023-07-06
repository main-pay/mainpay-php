<?php
	header('Content-Type: text/html; charset=utf-8');		
	require('utils.php'); // 유틸리티 포함
	$logPath = "c://app.log";     //디버그 로그위치 (windows)
	//$logPath = /home/app.log"; //디버그 로그위치 (리눅스)

    /********************************************************************************	
	 * 인증이 완료될 경우 PG사에서 호출하는 페이지 입니다. 	     
	 * PG로 부터 전달 받은 인증값을 받아 PG로 승인요청을 합니다.	
	 ********************************************************************************/
     
	$aid = $_REQUEST["aid"];
	$authToken = $_REQUEST["authToken"];
	$merchantData = $_REQUEST["merchantData"];
	$payType = $_REQUEST["payType"];
	$receiveParam = "## 인증결과수신 ## aid:".$aid.", authToken:".$authToken; 
	pintLog("RECEIVE-PARAM: ".$receiveParam, $logPath);
	echo $receiveParam; // 실운영시 제거
	
	/********************************************************************************
     *	reay에서 DB에 저장한 요청정보 값 조회해서 사용하세요.
     ********************************************************************************/		
	$API_BASE = "https://test-api-std.mainpay.co.kr";
	$apiKey = "U1FVQVJFLTEwMDAxMTIwMTgwNDA2MDkyNTMyMTA1MjM0";
	
	$version = "V001";		
	$mbrNo = "100011";
	/*$mbrRefNo = makeMbrRefNo($mbrNo);*/
	$mbrRefNo = "P021110001";
	$paymethod = "CARD";		
	$amount = "1000";				
	$goodsName = "카약-슬라이더406";	
	$goodsCode = "GOOD0001";		
	$approvalUrl = "https://상점도메인/mobile/_3_approval.php";	
	$closeUrl = "https://상점도메인/mobile/_3_close.php";				
	$customerName = "고객명";	
	$customerEmail = "test@spc.co.kr";				
	$timestamp = makeTimestamp();
	$signature = makeSignature($mbrNo,$mbrRefNo,$amount,$apiKey,$timestamp); 
	
	$parameters = array(
		'version' => $version,
		'mbrNo' => $mbrNo,
		'mbrRefNo' => $mbrRefNo,
		'paymethod' => $paymethod,
		'amount' => $amount,
		'goodsName' => $goodsName,
		'goodsCode' => $goodsCode,
		'approvalUrl' => $approvalUrl,
		'closeUrl' => $closeUrl,
		'customerName' => $customerName,
		'customerEmail' => $customerEmail,		
		'timestamp' => $timestamp,		
		'signature' => $signature
	);
	
	$parameters["aid"] = $aid;
	$parameters["authToken"] = $authToken;
	$parameters["merchantData"] = $merchantData;
	$parameters["payType"] = $payType;

    /********************************************************************************
	* 승인요청 API 호출         	
	*********************************************************************************/
	$PAY_API_URL = $API_BASE."/v1/payment/pay";
	$result = "";
	$errorMessage = "";
	
	try{
		pintLog("PAY-API: ".$PAY_API_URL, $logPath);
		pintLog("PARAM: ".print_r($parameters, TRUE), $logPath);
		$result = httpPost($PAY_API_URL, $parameters);
	} catch(Exception $e) {
		$errorMessage = "승인요청API 호출실패: " . $e;
		pintLog($errorMessage, $logPath);
		
		/*********************************************************************************
		* 망취소 처리(승인API 호출 도중 응답수신에 실패한 경우) 
		*********************************************************************************/
     	$NET_CANCEL_URL = $API_BASE."/v1/payment/net-cancel"; 
     	$result = httpPost($NET_CANCEL_URL, $parameters);			
		return;			
	}
	echo("<br>## 승인요청API 호출 결과 : <br>" . $result); 
	pintLog("RESPONSE: ".$result, $logPath);
	
	$obj = json_decode($result);				
	$resultCode = $obj->{'resultCode'};
	$resultMessage = $obj->{'resultMessage'};
	$data = $obj->{'data'};
	/* 추가 항목은 연동매뉴얼 참조*/
	$refNo = "";      // 거래번호
	$tranDate = "";	  // 거래일자
	$mbrRefNo = "";   // 주문번호
	$applNo = "";     // 승인번호
	$payType = "";    // 인증타입
	
	/*********************************************************************************
	* 승인결과 처리 (결과에 따라 상점 DB처리 및 화면 전환 처리)
	*********************************************************************************/
	if($resultCode != "200"){		
		/*호출실패*/
		$errorMessage = "<br>## 승인요청API 호출 결과: resultCode:".$resultCode . ", resultMessage". $resultMessage;
		echo $errorMessage; // 실운영시 제거
		return;
	} else {
		/*승인요청API 호출 성공*/		
		$refNo = $data->{'refNo'};
		$tranDate = $data->{'tranDate'};
		$mbrRefNo = $data->{'mbrRefNo'};
		$applNo = $data->{'applNo'};
		$payType = $data->{'payType'};
		
		/*== 가맹점 DB 주문처리 ==*/
	}

?>
<!DOCTYPE html>
<html>
<head>
<title>상점 도착페이지</title>
<meta name="viewport" content="width=device-width, user-scalable=no">
<script src="https://api-std.mainpay.co.kr/js/mainpay.pc-1.0.js"></script>
</head>
<body>
<script>
/* 결제 완료 페이지 호출 */
var resultCode = "<?=$resultCode ?>";
var resultMessage = "<?=$resultMessage ?>";
alert("resultCode:" + resultCode + ": " + resultMessage);

/* 결제처리 성공 유무에 따른 화면 전환 */
//location.href = "1_4_complete.php";	
</script> 
</body>
</html>