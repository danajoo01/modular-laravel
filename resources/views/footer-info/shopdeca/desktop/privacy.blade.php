<?php 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.desktop.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/error.css") }}">
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/about.css") }}">
@endsection

@section('content')

<div class="error-content thx-wrapper">
	<div class="error-overlay">
    <div class="error-inside">
    	<div class="about-wrapper tnc-wrapper">
        	<h1>PRIVACY POLICY</h1>
        	<p>Welcome to Shopdeca.com. Shopdeca collects customer information in an effort to improve our customers shopping experience, to communicate with our customers about our products, services and promotions, and to enhance and improve the performance and accuracy of our operations and website. We collect information such as your name, e-mail, address and credit card numbers that you provide to us when you place an order, when you save your information with us or when you participate in a promotion. Shopdeca may use your information collected online to process and fulfill your order. We also collect e-mail addresses at various locations within the site including when you order from the site so that we can send you any necessary e-mail messages related to your order. We may also contact you with marketing and promotional materials and other information that may be of interest to you. If you decide at any time that you no longer wish to receive such communications from us, please follow the unsubscribe instructions provided in any of the communications or contact us at shopdeca@berrybenka.com  . In addition, we maintain a record of your product interests, purchases and whatever else might enable us to enhance and personalize your shopping experience.</p>
          <p>To serve you better and improve our performance, we may also share your information with our affiliates and with third party service providers that provide support services to us or that help us market our products and services. We may also disclose your information when you ask us to do so or when we believe it is required by law. In the unlikely event of a sale of some or all of our business, Shopdeca may disclose your personal information to a purchaser that agrees to abide by the terms and conditions of this privacy policy.</p>
          <p>You can always review, update and remove your personal information by logging into the website and accessing your account. Please email hello@shopdeca.com if you need assistance with reviewing, updating or removing your personal information. When you update your personal information, we may keep a copy of the prior version for our records.</p>
          <p>Shopdeca may store some information on your computer in the form of a "cookie" or similar file. These files allow us to tailor our website to reflect your listed or historical preferences. Most web browsers allow web users to exercise control over such files on their computers by erasing them, blocking them, or notifying the user when such a file is stored. Please refer to your browser's instructions to learn about those functions. Please note that if you disable or delete these files you may not have access to certain features and services that make your online experience more efficient and enjoyable. These and other technologies also allow delivery of advertising that directly relates to offers that may be of interest to you. If, however, you'd prefer not to receive relevant banner advertisements, you may opt out at any time by visiting (www.networkadvertising.org/managing/opt_out.asp.)</p>
          <p>Shopdeca also uses other methods of automatic collection to determine information about visitors to our website, including your computer’s Internet Protocol (“IP”) address, browser type or the webpage you were visiting before you came to our Site, pages of our Site that you visit, the time spent on those pages, information you search for on our Site, access times and dates, and other statistics. We use the information obtained using these tools to provide better customer service, enhance our product offerings, and detect potential misuse or fraud.</p>                
          <ol class="term-of-use">
                  <li>
                    <strong>SECURITY</strong>
                    <p>Shopdeca works to protect the security of your information during order transmission by using Secure Sockets Layer (SSL) software, which encrypts order information you transmit. While we implement these and other security measures on our site, please note that 100% security is not always possible. You play a role in protecting your information as well. Because your password permits access to your personal information, please keep your password secret and do not disclose it to others.</p>
                  </li>
                  <li>
                    <strong>CHILDREN</strong>
                    <p>The Children's Online Privacy Protection Act imposes certain requirements on web sites directed at children under 13 that collect information on those children, and on web sites that knowingly collect information on children under 13. Shopdeca is not directed at children under 13, and does not knowingly collect any personal information from children under 13.</p>
                  </li>
                  <li>
                    <strong>CHANGES TO OUR PRIVACY POLICY</strong> 
                    <p>From time to time, we may review and revise our privacy policy. We reserve the right to change our privacy policy at any time and to notify you by publishing an updated version of the policy on the Shopdeca website.</p>
                  </li>
                </ol>
        </div>
    </div>
    </div>
</div>

@endsection



