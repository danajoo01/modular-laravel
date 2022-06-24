<?php 
return[
    //define domain lists
    'domains' => [
        '1' => env('BERRYBENKA', 'front.onedeca.com'),
        '2' => env('BERRYBENKA_MOBILE', 'm-front.onedeca.com'),
        '3' => env('HIJABENKA', 'hijabenka.com'),
        '4' => env('HIJABENKA_MOBILE', 'm.hijabenka.com'),
        '5' => env('SHOPDECA', 'shopdeca.com'),
        '6' => env('SHOPDECA_MOBILE', 'm.shopdeca.com')
    ],
    'folders' => [
        '1' => 'berrybenka.desktop',
        '2' => 'berrybenka.mobile',
        '3' => 'hijabenka.desktop',
        '4' => 'hijabenka.mobile',
        '5' => 'shopdeca.desktop',
        '6' => 'shopdeca.mobile'
    ],
    'bb_mailchimp' => [
        "apikey" => "006194adea19aa1996da3da8a90e9153-us11",
        "id" => "3d125d5adb",
    ],
    'reset_password' => [
        'password_reset_expiration' => 1800,
        'password_reset_secret' => '',
        'password_reset_email' => 'no-reply@berrybenka.com'
    ],
    'confirm_email' => [
        'confirm_email_expiration' => 7200,
        'confirm_email_secret' => '',
        'confirm_email_email' => 'no-reply@berrybenka.com'
    ],
    'klikpay' => [
        'clearkey_klikpay' => 'KlikpayDev2Berry',
        'klikpaycode_klikpay' => '08BERR0045',
        'post_url_klikpay' => 'http://simpg.sprintasia.net:8779/klikpay/webgw'
    ],
    'veritrans' => [//Default if ENV not set is development KEY
        'VERITRANS_API' => env('VERITRANS_API', 'http://api.sandbox.veritrans.co.id/v2/'),
        'VERITRANS_JS' => env('VERITRANS_JS', 'https://api.sandbox.veritrans.co.id/v2/token'),
        'VERITRANS_ENDPOINT' => env('VERITRANS_ENDPOINT', 'http://api.sandbox.veritrans.co.id/v2/charge'),
        'VERITRANS_SERVER_KEY_BB' => env('VERITRANS_SERVER_KEY_BB', 'VT-server-Kf5mL8CVjrKo9l8OYVF19YYK'),
        'VERITRANS_SERVER_KEY_BCA_BB' => env('VERITRANS_SERVER_KEY_BCA_BB', NULL), //Belum terima key dari Midtrans
        'VERITRANS_CLIENT_KEY_BB' => env('VERITRANS_CLIENT_KEY_BB', 'VT-client-tI5PcsVA9YsyNM9R'),
        'VERITRANS_SERVER_KEY_HB' => env('VERITRANS_SERVER_KEY_HB', '507607b5-bc6d-44d4-b486-b4bc2caa5d4a'),
        'VERITRANS_SERVER_KEY_BCA_HB' => env('VERITRANS_SERVER_KEY_BCA_BB', NULL), //Belum terima key dari Midtrans
        'VERITRANS_CLIENT_KEY_HB' => env('VERITRANS_CLIENT_KEY_HB', 'a8dff0f9-bec2-4a6b-b6ba-f8a287edb088'),
        'VERITRANS_SERVER_KEY_SD' => env('VERITRANS_SERVER_KEY_SD', 'VT-server-Kf5mL8CVjrKo9l8OYVF19YYK'),
        'VERITRANS_SERVER_KEY_BCA_SD' => env('VERITRANS_SERVER_KEY_BCA_SD', NULL), //Belum terima key dari Midtrans
        'VERITRANS_CLIENT_KEY_SD' => env('VERITRANS_CLIENT_KEY_SD', 'VT-client-tI5PcsVA9YsyNM9R'),
        'VERITRANS_IS_PRODUCTION' => env('VERITRANS_IS_PRODUCTION', false),
        'VERITRANS_IS_SANITIZATION' => env('VERITRANS_IS_SANITIZATION', false),
    ],
    'mailchimp' => [
        'api_url'       => 'https://us11.api.mailchimp.com/3.0/',
        'api_url_bb'    => 'https://us11.api.mailchimp.com/3.0/',
        'api_url_hb'    => 'https://us16.api.mailchimp.com/3.0/',
        'api_url_sd'    => 'https://us16.api.mailchimp.com/3.0/',
        'apikey'        => env('MAILCHIMP_API_KEY', '263d586ab2e0e00b5916bdf1b2e13095-us16'),
        'apikey_bb'     => env('MAILCHIMP_API_KEY_BB', '263d586ab2e0e00b5916bdf1b2e13095-us16'),
        'apikey_hb'     => env('MAILCHIMP_API_KEY_HB', '263d586ab2e0e00b5916bdf1b2e13095-us16'),
        'apikey_sd'     => env('MAILCHIMP_API_KEY_SD', 'f22ad0c481172d6de38b902f257e83aa-us16'),       
        'listID_DEV'    => [
            'listID_bb'     => 'ca5442179e',
            'listID_hb'     => '38fbfa631f',
            'listID_sd'     => 'e1cc6c5cd7'    
        ],
        'listID_LIVE'   => [
            'listID_bb'     => '3d125d5adb',
            'listID_hb'     => 'f8ed0de900',
            'listID_sd'     => '6da15ed33b'              
        ]        
    ],
    'frontier' => [
        'api_url'       => 'http://api.netcoresmartech.com/apiv2',
        'apikey'        => '62ac2fa83e775a3466ba89fd6c95d4f4',
        'listID_DEV'    => [
            'listID_bb'     => '5',
            'listID_hb'     => '6',
            'listID_sd'     => '7'    
        ],
        'listID_LIVE'   => [
            'listID_bb'     => '5',
            'listID_hb'     => '6',
            'listID_sd'     => '7'              
        ]        
    ],
    'prism_merchant_id' => [
        'bb_live' => '72d81326-ed64-4433-a3db-f1be76bfc0b1',
        'bb_dev' => 'a2672355-3c7b-4690-86d6-a773354cf5fc',
        //'hb_live' => 'f4cf92c5-c2ba-4d42-8747-524ccea1f5ba',
        'hb_live' => 'ffc0a6ef-2b77-4d99-9d55-e9c2331bc275',
        'hb_dev' => '2e924182-ded9-437b-90d3-5788f44ec248',
        'sd_live' => '077daa05-81d3-4d64-93d9-b24b30e6ed10',
        'sd_dev' => '077daa05-81d3-4d64-93d9-b24b30e6ed10'
    ],
    
    'prism_client_key' => [
        'bb_live' => '0334aa20fe0c6b9ed81d6703755d8e46037ca875be3576634340b2476075f204',
        'bb_dev' => '0334aa20fe0c6b9ed81d6703755d8e46037ca875be3576634340b2476075f204',
        'hb_live' => '0334aa20fe0c6b9ed81d6703755d8e46037ca875be3576634340b2476075f204',
        'hb_dev' => 'ab641aa17da9d2cc7e69b1f84bffe7fbc27d0b8b22810d9b69ed01f9b8fd08a4',
        'sd_live' => '0334aa20fe0c6b9ed81d6703755d8e46037ca875be3576634340b2476075f204',
        'sd_dev' => '0334aa20fe0c6b9ed81d6703755d8e46037ca875be3576634340b2476075f204'        
    ],
    
    'prism_js_url' => [
        'bb_live' => 'https://prismapp-files.s3.amazonaws.com/widget/prism.js?',
        'bb_dev' => 'https://prismapp-sandbox.s3.amazonaws.com/files/widget/prism.js?',
        'hb_live' => 'https://prismapp-files.s3.amazonaws.com/widget/prism.js?',
        'hb_dev' => 'https://prismapp-sandbox.s3.amazonaws.com/files/widget/prism.js?',
        'sd_live' => 'https://prismapp-files.s3.amazonaws.com/widget/prism.js?',
        'sd_dev' => 'https://prismapp-sandbox.s3.amazonaws.com/files/widget/prism.js?'     
    ],
    'berrybenka_block_payment_id' => [],     
    'berrybenka_url_email' => "Berrybenka [dot] com",

    'gcr_merchant_id' => [
        'bb_live' => '100941103',
        'bb_dev' => '117819047',
        'hb_live' => '115092239',
        'hb_dev' => '117804612'      
    ],
    
    'KREDIVO' => [
        'SANDBOX' => [
            'BASEURL_REDIRECT' => 'https://sandbox.kredivo.com/kredivo', 
            'BERRYBENKA_SERVER_KEY' => 'Xy5nxdNu6vZMfU2F4Mhrr259KKHV7Uj6',
            'HIJABENKA_SERVER_KEY' => '44x6uz7zqNfjSzMt69vsdR3xCt9dB4M6',
            'SHOPDECA_SERVER_KEY' => 'zzZxcRTwmmSw9q9Q7B4SxkDqkZt7cQAf'
        ],
        'PRODUCTION' => [
            'BASEURL_REDIRECT' => 'https://api.kredivo.com/kredivo', //dummy, not yet received
            'BERRYBENKA_SERVER_KEY' => 'ffNwW6rSURZ5DZ5NZC28n6w2w8qPbqD3',
            'HIJABENKA_SERVER_KEY' => 'SX28dWsZtAMvWCt8zvjf84q6PDK69G6T',
            'SHOPDECA_SERVER_KEY' => 'suJ6z55X6SUYUbQg3hDnbqFf99XA2yrH'
        ]
    ]
];

?>
