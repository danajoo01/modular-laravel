<!--<div id="prism-widget"></div>

script js
<script src="https://prismapp-files.s3.amazonaws.com/widget/prism.js"></script>
<script type="text/javascript">
(function(e, b, c) {
    e.Shamu = {
        merchant_id: '',
        initialize: function(a) {
            a.merchant_id ? this.merchant_id = a.merchant_id : console.log("Shamu: Please initialize Shamu with a merchat_id");
        },
        display: function() {
            if (this.merchant_id) {
                require('initialize');
            } else console.log("Skyfish: You need to initialize Skyfish with a merchant_id")
        }
    }
})(window, document, "script");
</script>
<script type="text/javascript">
    Shamu.initialize({'merchant_id': '{{ GetMerchantIdPrism() }}',});
    Shamu.display();
</script>-->
<?php 
$date = date('d-m-Y');
if($date != '01-06-2017'){
?>
<script type="text/javascript">
(function(p,r,i,s,m) {var a = 'v2';s = r.createElement('script');m = r.getElementsByTagName('body')[0].appendChild(s);s.src = '{{ GetJSUrlPrism() }}' + a.toString(); s.async = true;s.onload = function() {p.Shamu = new Prism('{{ GetMerchantIdPrism() }}');Shamu.display();}})(window, document);
</script>
<?php
}
?>