@extends('layouts.hijabenka.mobile.main')

@section('css')
<!-- CSS here -->
<style>
	.container {
	    position: relative;
	    width: 960px;
	    margin: 0 auto;
	    padding: 0;
	}
	input[type="email"], input[type="text"], textarea, select {
		text-align: center;
	    border: 0px;
		padding: 5px;
	    outline: none;
	    font: 14px "Open Sans", "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif;
	    color: #777;
	    margin: 0;
	    display: block;
	    background-color: #dedede;
	    width: 100%;
	}
	.half-width {
	    width: 48%;
	    float: left;
	    display: block;
	}
	.pull-left {
	    float: left !important;
	}
	.mb20 {
	    margin-bottom: 20px !important;
	}
	.mt20 {
	    margin-top: 20px !important;
	}
	.row {
	    display: table;
	}
	.container .sixteen.columns {
	    width: 940px;
	}
	.container .column, .container .columns {
	    float: left;
	    display: inline;
	    margin-left: 10px;
	    margin-right: 10px;
	}
	.gathering-form form {
	    margin: 0;
	    padding: 0;
	    margin-bottom: 20px;
	}
	.text-center {
	    text-align: center !important;
	}
	h4 {
	    font-size: 21px;
	    line-height: 30px;
	    margin-bottom: 4px;
	}
	.btn-default, .btn-primary, .btn-success, .btn-info, .btn-warning, .btn-danger {
	    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.2);
	    -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
	    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
	}
	.btn-sm {
	    padding: 5px 10px !important;
	    font-size: 12px;
	    line-height: 1.5;
	}
	.btn-primary {
	    color: #ffffff !important;
	    background-color: #111 !important;
	}
    .btn {
	    font-family: "Open Sans", sans-serif;
	    display: inline-block;
	    padding: 8px 12px !important;
	    margin-bottom: 0;
	    font-size: 13px;
	    font-weight: 400;
	    line-height: 1.428571429 !important;
	    height: auto !important;
	    text-align: center;
	    white-space: nowrap;
	    vertical-align: middle;
	    cursor: pointer;
	    -webkit-user-select: none;
	    -moz-user-select: none;
	    -ms-user-select: none;
	    -o-user-select: none;
	    user-select: none;
	    background-clip: padding-box;
	    text-decoration: none;
	    position: relative;
	    border-width: 0px;
	}
	.loading {
	    position: fixed;
	    top: 51%;
	    left: 50%;
	    z-index: 9999;
	    margin-top: -50px;
	    margin-left: -50px;
	    display: none;
	}
</style>
@endsection

@section('content')
<div class="loading">
        <img src="{{ asset('berrybenka/desktop/img/bb-loading.gif') }}">
</div>

<div class="content">
	@if($data)
	    {!! $data["css_mob"] !!}
	    <span class="main_html">
	    {!! $data["main_html_mob"] !!}
	    </span>

	    <span class="success_html" style="display: none">
	    {!! $data["success_html_mob"] !!}
	    </span>


	    <span class="error_html" style="display: none">
	    {!! $data["error_html_mob"] !!}
	    </span>   
	@endif
	
	<?php $utm_campaign = isset($data["utm_campaign"]) ? $data["utm_campaign"] : ''; ?>
</div>

@endsection

@section('js')
<!-- JS here -->
<script>
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
</script>

@if($data["js"] !="")
{!! $data["js"] !!}}
@else
<script>
    $(document).ready(function () {
        $(".content input").removeAttr('required');

        var submitActor = null;
        var $button = $(".content button");
        var $form = $(".content form");

        $button.click(function (e) {
            var gender = "women";
            var class_button = $(this).attr('class');
            if (class_button.indexOf('women') != -1) {
                gender = "women";
            }else if (class_button.indexOf('men') != -1) {
                gender = "men";
            }

            var fill = "";
            var names = "";
            var phones = "";
            var emails = "";
            var citys = "";
            var validmail = false;
            $(".content form :input").each(function () {
                if ($(this).attr('name') == "name") {
                    names = $(this).val();
                    if (names.length == 0) {
                        fill = fill + "Nama, ";
                    }
                }
                if ($(this).attr('name') == "phone") {
                    phones = $(this).val();
                    if (phones.length == 0) {
                        fill = fill + "No. Telp, ";
                    }
                }
                if ($(this).attr('name') == "email") {
                    emails = $(this).val();
                    if (emails.length == 0) {
                        fill = fill + "Email, ";
                    } else {
                        if (!validEmail(emails)) {
                            validmail = true;
                        }
                    }
                }
                if ($(this).attr('name') == "city") {
                    citys = $(this).val();
                    if (citys.length == 0) {
                        fill = fill + "Nama Kota, ";
                    }
                }
            });

            if (fill.length > 0) {
                alert(fill + "harus diisi !");
                return false;
            }
            if (validmail) {
                alert("Masukan format email yang benar");
                return false;
            }

            if (fill.length == 0 && !validmail) {
                $.ajax({
                    type: "POST",
                    url: '/campaign/subscribe',
                    data: {
                        "referrer": '{{ $utm_campaign }}',
                        @if($domain_id == 1)
                        	"host_name": 'berrybenka.com',
                        @elseif($domain_id == 2)
                        	"host_name": 'hijabenka.com',
                        @else
                        	"host_name": 'shopdeca.com',
                        @endif
                        
                        "utm_campaign": '{{ $utm_campaign }}',
                        
                        @if($domain_id == 1)
                        	"utm_campaign_bb": '{{ $utm_campaign }}',
                        @elseif($domain_id == 2)
                        	"utm_campaign_hb": '{{ $utm_campaign }}',
                        @else
                        	"utm_campaign_sd": '{{ $utm_campaign }}',
                        @endif
                        
                        "form_location": "newsletter",
                        "subscriber_email": emails,
                        "subscriber_first_name": names,
                        "subscriber_telp": phones,
                        "subscriber_city" : citys,
                        "subscriber_type": gender,
                        "subscriber_gender": gender,
                        "is_get_voucher": 0
                    },
                    beforeSend: function () {
                        $(".loading").fadeIn('slow');
                    },
                    success: function (data) {
                        $(".main_html").hide();
                        var json = JSON.parse(data);
                        if (json.result == "success") {
                            $(".error_html").hide();
                            $(".success_html").fadeIn(1000);
                        } else {
                            $(".success_html").hide();
                            $(".error_html").fadeIn(1000);
                        }

                        $(".loading").fadeOut(500);
                    }
                });
                return false;
            }

            return false;
        });

        $('a:contains("Kembali"), a:contains("kembali")').click(function (event) {
            event.preventDefault();
            $('.success_html').hide();
            $('.error_html').hide();
            $('.main_html').show();
        });

    });

    function validEmail(v) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return !regex.test(v) ? false : true;
    }
</script>
@endif
@endsection