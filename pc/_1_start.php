<?php
    header('Content-Type: text/html; charset=utf-8');
	$READY_API_URL = "_2_ready.php"	;
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://api-std.mainpay.co.kr/js/mainpay.pc-1.1.js"></script>
<script type='text/javascript'> 
	var READY_API_URL = "<?=$READY_API_URL?>";
	function payment() {		
		var request = Mainpay.ready(READY_API_URL); 
		request.done(function(response) {
			if (response.resultCode == '200') {
				/* 결제창 호출 */
				Mainpay.open(response.data.nextPcUrl); //*주의* PC와 Mobile은 URL이 상이합니다.
				return false;
			}
			alert("ERROR : "+JSON.stringify(response));			 				
		});		
	}
	window.onpopstate = function(){ history.go(-1)};
	
	/* 결제 팝업이 닫혔을 경우 호출*/
	function mainpay_close_event() {
		alert("결제창이 닫혔습니다.");  
	}
</script>  
</head>
<body>
	<p>PC 버전 샘플 주문페이지(NoFrame)</p>
	<div>
		<!-- id 고정 -->
		<form id="MAINPAY_FORM">
			지불수단 <input type="text" name="paymethod" value="CARD"> <br>
			상품코드 <input type="text" name="goodsCode" value="GOOD0001"> <br> 
			상품명칭 <input type="text" name="goodsName" value="카약-슬라이더406"> <br><br>
		</form>
		<button type="button" class="btn_submit" onclick="payment()">결제요청</button>
	</div>
</body>
</html>

