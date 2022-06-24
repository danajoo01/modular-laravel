<?php 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.mobile.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/mobile/css/home.css") }}">
@endsection

@section('content')

<!--TNC-->
<style>
.tnc{padding:20px;text-align:center;}
.tnc h1{text-align:center;display:inline-block;border-bottom:1px solid #000;margin:20px auto;font-family:'futura',serif;padding:0 5px 5px 5px;font-size:1.5em;}
.tnc p{line-height:1.5;text-align:left;margin:10px 0;letter-spacing:1px;font-family:'futura',serif;color:#555;}
.tnc ol{padding-left:20px;}
.tnc ol li,.tnc ol li ol li{text-align:left;list-style:decimal;font-family:'futura',serif;color:#555;letter-spacing:1px;line-height:1.5;}
.tnc ol li h2{margin:0;font-family:'futura',serif;text-transform:capitalize;letter-spacing:1px;}
</style>
<div class="content-home">
	<div class="tnc">
        	<h1>TERM AND CONDITION</h1>
        	<p>Welcome to Shopdeca.com. By visiting or shopping in our website, you are subject and accept to follow these terms. Please read carefully.</p>
          <ol class="term-of-use">
                  <li>
                    <strong>PRIVACY</strong>
                    <p>Go through and review our Privacy Policy, which also governs your visit to Shopdeca.com to understand our practices.</p>  
                  </li>
                  <li>
                    <strong>SITE CONTENTS</strong>
                    <p>The design of the Site, the Site as a whole, and all materials that are part of the Site are copyrights, trademarks, trade dress or other intellectual properties owned, controlled or licensed by Shopdeca or its subsidiaries and affiliates. The Contents are intended solely for your personal, noncommercial use. You may copy other Contents displayed on the Site for your personal, noncommercial use only. No right, title or interest in any Contents is granted or transferred to you as a result of any such copying. Except as noted above, you may not reproduce, publish, transmit, distribute, display, modify, create derivative works from, sell or participate in any sale of, or exploit in any way, any of the Contents or the Site. Unauthorized use of the Contents is expressly prohibited by law, and may result in severe civil and criminal penalties. Shopdeca reserves the right to refuse service, terminate accounts, remove or edit content, or cancel orders in its sole discretion.</p>
                  </li>
                  <li>
                    <strong>COMMENTS AND OTHER SUBMISSIONS</strong> 
                    <p>We welcome your comments and feedback regarding our Site, our products and our services. Any Comments posted or sent to the website shall be and will remain the exclusive property of Shopdeca. Submission of any Comments to the Website shall be deemed an assignment to Shopdeca of all intellectual property rights, titles and interest in and to such Comments. Such Comments will not be treated as confidential information. Shopdeca shall have all rights to use, reproduce, publish, distribute and otherwise disclose such Comments for any purpose without restriction without providing any compensation to you.</p>
                  </li>
                  <li>
                    <strong>RISK OF LOSS</strong>
                    <p>All items purchased from Shopdeca are made pursuant to a shipment contract. This means that the risk of loss and title for such items pass to you upon our delivery to the carrier.</p>
                  </li>
                  <li>
                    <strong>PRODUCT INFORMATIONS AND AVAILABILITY</strong> 
                    <p>Shopdeca attempts to be as accurate as possible. However, Shopdeca does not warrant that product descriptions or other content of this Site are accurate. In addition, the actual colors you see will depend on your monitor and may not be accurate. We apologize for any inconvenience this may cause you. If a product offered by Shopdeca itself is not as described, you may return it in its original condition. Please see our return policy. Product availability is not guaranteed. If a product is not available when your order processes we will notify you by email.</p>
                  </li>
                  <li>
                    <strong>PRICING</strong>
                    <p>Despite our best efforts, items on our Site may occasionally be mispriced. Shopdeca shall have the right to refuse or cancel any orders placed for product listed at the incorrect price. Shopdeca shall have the right to refuse or cancel any such orders whether or not the order has been confirmed and your credit card charged. If payment has already been made or if the credit card has already been charged for the purchase and the order is cancelled, Shopdeca shall promptly credit the credit card account in the amount of the incorrect price.</p>
                  </li>
                  <li>
                    <strong>LINKS TO OTHER WEBSITES AND SERVICES</strong>
                    <p>We may provide links to the sites of affiliated companies and certain other businesses. We are not responsible for examining or evaluating, and we do not warrant the offerings of, any of these businesses or individuals or the content of their Websites. Shopdeca does not assume any responsibility or liability for the actions, product, and content of all these and any other third parties. You should carefully review their privacy statements and other conditions of use.</p>
                  </li>
                  <li>
                    <strong>SITE POLICIES, MODIFICATION AND SEVERABILITY</strong>
                    <p>We reserve the right to make changes to our Site, policies, and these Terms at any time. If any of these terms shall be deemed invalid, void, or for any reason unenforceable, that term shall be deemed severable and shall not affect the validity and enforceability of any remaining term.</p>
                  </li>
                  <li>
                    <strong>TERMINATION</strong>
                    <p>These Terms are effective unless and until terminated by Shopdeca. Shopdeca may terminate these Terms without notice and at any time. In the event of termination, you are no longer authorized to access the Site and the restrictions imposed on you with respect to the Contents and the disclaimers, indemnities, and limitations of liabilities set forth in these Terms shall survive termination. Shopdeca shall also have the right without notice and at any time to terminate the Site or any portion thereof, or any products or services offered through the Site, or to terminate any individual's right to access or use the Site or any portion thereof.</p>
                  </li>
                  <li>
                    <strong>NOTICE</strong>
                    <p>Shopdeca may deliver notice to you by means of electronic mail, a general notice on the site, or by written communication delivered by mail services in Indonesia.</p>
                  </li>
                  <li>
                    <strong>DISCLAIMER, LIMITATION OF LIABILITY AND INDEMNITY</strong>
                    <p>EXCEPT AS OTHERWISE EXPRESSLY PROVIDED, THIS SITE, ALL CONTENTS AND ALL PRODUCTS AND SERVICES ARE PROVIDED ON AN 'AS IS' BASIS. SHOPDECA DISCLAIMS ALL WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION, IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. SHOPDECA DOES NOT WARRANT THAT YOUR USE OF THIS SITE WILL BE UNINTERRUPTED OR ERROR FREE, OR THAT THIS SITE OR ITS SERVER ARE FREE OF VIRUSES OR OTHER HARMFUL MATERIALS.</p>
                  </li>
                </ol>
        </div>
</div>
<!--TNC-->

@endsection



