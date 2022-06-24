<!-- prism v1-->
<!--<div id="sfe.app"><sfe-widget></sfe-widget></div>
<script type="text/javascript">
!function(a,b,c){a.Skyfish={ client_key:"{{ GetClientKeyPrism() }}",vt_client_key:"123",merchant_id:"{{ GetMerchantIdPrism() }}",display:function(){var a=b.createElement(c);a.src="https://lib.skyfish.id/dist/js/s.js",a.onload=a.onreadystatechange=function(){var a=this.readyState;if(!a||"complete"==a||"loaded"==a)try{(void 0).display()}catch(a){}angular.element(document).ready(function(){angular.bootstrap(b.getElementById("sfe.app"),["sfe.app"])})};var d=b.getElementsByTagName(c)[0];d.parentNode.insertBefore(a,d)}}}(window,document,"script"),window.hasOwnProperty("Skyfish")&&Skyfish.display();
</script>-->
<!-- end prism v1-->

<script type="text/javascript">
(function(p,r,i,s,m) {var a = 'v2';s = r.createElement('script');m = r.getElementsByTagName('body')[0].appendChild(s);s.src = '{{ GetJSUrlPrism() }}' + a.toString(); s.async = true;s.onload = function() {p.Shamu = new Prism('{{ GetMerchantIdPrism() }}');Shamu.display();}})(window, document);
</script>