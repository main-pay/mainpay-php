<?php
	header('Content-Type: application/json; charset=utf-8');	
	require('utils.php');                // 유틸리티 포함
	$logPath = "c://app.log";            //디버그 로그위치 (windows)
	//$logPath = /home/app.log";         //디버그 로그위치 (리눅스)

	/*****************************************************************************************
    * CANCEL API URL  (결제 취소 URL)    
    ******************************************************************************************
	- API 호출 도메인
    - ## 테스트 완료후 real 서비스용 URL로 변경  ## 
    - 리얼-URL : https://relay.mainpay.co.kr/v1/api/payments/payment/cancel
    - 개발-URL : https://dev-relay.mainpay.co.kr/v1/api/payments/payment/cancel  	 	 	 
	*/
	
	$CANCEL_API_URL = "https://dev-relay.mainpay.co.kr/v1/api/payments/payment/cancel";
		
    /*
      API KEY (비밀키)  
     - 생성 : http://biz.mainpay.co.kr 고객지원>기술지원>암호화키관리
     - 가맹점번호(mbrNo) 생성시 함께 만들어지는 key (테스트 완료후 real 서비스용 발급필요) */
    $apiKey = "U1FVQVJFLTEwMDAxMTIwMTgwNDA2MDkyNTMyMTA1MjM0"; // <===테스트용 API_KEY입니다. 100011		

	
	/*****************************************************************************************
    *	취소 요청 파라미터 
    ******************************************************************************************/
	$version = "1.0";		
    /* 가맹점 아이디(테스트 완료후 real 서비스용 발급필요)*/
	$mbrNo = "100011"; //<===테스트용 가맹점아이디입니다.
	/* 가맹점 주문번호 (가맹점 고유ID 대체가능) 6byte~20byte*/
	$mbrRefNo = makeMbrRefNo($mbrNo);
	/* 원거래번호 (결제완료시에 수신한 값)*/
	$orgRefNo = "123456789012";
	/* 원거래일자(결제완료시에 수신한 값) YYMMDD */
	$orgTranDate = "180912";
	/* 원거래 지불수단 (CARD:신용카드|VACCT:가상계좌|ACCT:계좌이체|HPP:휴대폰소액) */
	$paymethod = "CARD";
	/* 결제금액 */
	$amount = "1000";
	/* 결제타입 (결제완로시에 받은 값) */
	$payType = "I";
	/* 망취소 유무(Y:망취소, N:일반취소) (주문번호를 이용한 망취소시에 사용) */
	$isNetCancel = "N";
	/* 고객명 특수문자 사용금지, URL인코딩 필수 max 30byte */
	$customerName = urlencode("고객명");
	/* 고객이메일 이메일포멧 체크 필수 max 50byte */
	$customerEmail = "hong@sample.com";	
	
	/* timestamp max 20byte*/
	$timestamp = makeTimestamp();
	/* signature 64byte*/
	$signature = makeSignature($mbrNo,$mbrRefNo,$amount,$apiKey,$timestamp); 

	$parameters = array(
		'version' => $version,
		'mbrNo' => $mbrNo,
		'mbrRefNo' => $mbrRefNo,
		'orgRefNo' => $orgRefNo,
		'orgTranDate' => $orgTranDate,
		'paymethod' => $paymethod,
		'amount' => $amount,
		'payType' => $payType,
		'isNetCancel' => $isNetCancel,
		'customerName' => $customerName,
		'customerEmail' => $customerEmail,		
		'timestamp' => $timestamp,		
		'signature' => $signature		
	);

    /*****************************************************************************************
	* CANCEL API 호출
	*****************************************************************************************/
	$result = "";
	$errorMessage = "";
	try{
		pintLog("CANCEL-API: ".$CANCEL_API_URL, $logPath);
		pintLog("PARAM: ".print_r($parameters, TRUE), $logPath);
		
		$result = httpPost($CANCEL_API_URL, $parameters);
	} catch(Exception $e) {
		$errorMessage = "결제 취소 API 호출실패: " . $CANCEL_API_URL;
		pintLog("ERROR: ".$errorMessage, $logPath);
		throw new Exception($e);
		return;
	}
	
	pintLog("RESPONSE: ".$result, $logPath);
	
	$obj = json_decode($result);	
	$resultCode = $obj->{'resultCode'};
	$resultMessage = $obj->{'resultMessage'};
		
	if($resultCode = "200"){		
		$data = $obj->{'data'};
		// 하단 JSON TYPE RESPONSE 참고하여 데이터 저장
	}
	
	// JSON TYPE RESPONSE
	echo $result;
?>    
