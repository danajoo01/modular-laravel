<html>
	<head>
		<title>Redirecting to Klikpay</title>
	</head>
	<body>       
		Redirecting to BCA KlikPay... Please wait...<br/><br/>
		<a href="{{ URL::to('/new-arrival/women') }}">Return to Berrybenka.com</a><br/>
						
		<form action="{{ $postUrl }}" method="POST" style="display:none">
		<input type="hidden" name="klikPayCode" value="{{ $klikPayCode }}" /><br/>
		<input type="hidden" name="transactionNo" value="{{ $transactionNo }}" /><br/>
		<input type="hidden" name="totalAmount" value="{{ $totalAmount }}.00" /><br/>
		<input type="hidden" name="currency" value="{{ $currency }}" /><br/>
		<input type="hidden" name="payType" value="{{ $payType }}" /><br/>
		<input type="hidden" name="callback" value="{{ $callbackUrl }}" /><br/>
		<input type="hidden" name="transactionDate" value="{{ $transactionDateTime }}" /><br/>
		<input type="hidden" name="descp" value="" /><br/>
		<input type="hidden" name="miscFee" value="" /><br/>
		<input type="hidden" name="signature" value="{{ $signature }}"/><br/>
		<input style="display:none" type="submit" id="mnuSubmit" value="Submit" /><br/>
		</form>
		<script src="//code.jquery.com/jquery-1.10.2.js" type="text/javascript"></script>
    <script type='text/javascript'>
      $(document).ready(function() {
          $('#mnuSubmit').trigger('click');
      });
    </script>
	</body>
</html>