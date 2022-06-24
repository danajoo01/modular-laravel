@extends('layouts.hijabenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/user.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/catalog-list.css') }}">

<style type="text/css">
    .btn-group button {
      margin-top: 10px;
      color: white;
      padding: 10px 24px; /* Some padding */
      cursor: pointer; /* Pointer/hand icon */
      float: left; /* Float the buttons side by side */
    }

    .btn-group .redeem {
        background-color: #3FC380;
        border: 1px #3FC380;
    }

    .btn-group .cancel {
        background-color: #EC3D40;
        border: 1px #EC3D40;
    }

    .btn-group button:not(:last-child) {
      border-right: none; /* Prevent double borders */
    }

    /* Clear floats (clearfix hack) */
    .btn-group:after {
      content: "";
      clear: both;
      display: table;
    }

    .deals-confirmation{
        margin-top: 40px;
        display: none;
    }

    .loading-redeem {
        display: none;
        background-color: #39B3D7; /* Green background */
        border: none;
        font-family: 'futura',arial;
        letter-spacing: 2px;
        font-size: 14px;
        text-transform: uppercase;
        color: white; /* White text */
        padding: 12px 30px; /* Some padding */
    }
</style>
@endsection

@section('content')
<input id="ajax_url" type="hidden" value="{{ url('/') }}" />

<div class="user-wrapper clearfix">
    <div class="wrapper">
        <div class="user-wrap">
            {!! get_view('account', 'account.leftmenu', array('page'=>'index','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard">
                    <h1 class="clearfix">
                        <i class="fa fa-dashboard"></i>Halaman Akun
                        <div class="right last-login"><b>Terakhir Login : </b>{{ indonesian_date(strtotime($user->last_login_date),'l, j F Y H:i:s') }}</div>
                    </h1>

                    <div class="user-dashboard-content">
                        <div class="stamp-wrapper">
                            <div class="stamp-menu">
                                {!! get_view('account', 'account.stampmenu', array('page'=>'deals')) !!}                                                    
                            </div>
                            <div class="stamp-deals">
                                @foreach ($deals as $row)
                                <div id="bb-stamp-detail-{{ $row->id }}" class="bb-stamp-detail">
                                    <div class="deals-wrapper">
                                        <img src="{{ IMAGE_DEALS_UPLOAD_PATH }}{{ $row->deals_image }}" alt="">
                                        <div class="deals-wording">
                                            <h6>{{ $row->deals_name}}</h6>
                                            <p>{{ $row->deals_description}}</p>
                                            <a href="#" class="redeem-stamp">Redeem Stamp</a>
                                            <button class="loading-redeem">
                                              <i class="fa fa-spinner fa-spin" style="color: white;"></i>&nbsp;&nbsp;Loading
                                            </button>

                                            <div class="deals-confirmation">
                                                <p>Anda yakin ingin redeem deals?</p>
                                                <div class="btn-group">
                                                    <button class="redeem" data-id="{{ $row->id}}">Redeem</button>
                                                    <button class="cancel">Cancel</button>
                                                </div>
                                            </div>
                                            <div id="redeem-note" class="redeem-note"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                <ul>
                                    @foreach ($deals as $row)
                                    <li>
                                        <a href="#bb-stamp-detail-{{ $row->id }}" class="fancybox">
                                            <img src="{{ IMAGE_DEALS_UPLOAD_PATH }}{{ $row->deals_image }}" alt="">
                                            <div class="deals-detail">
                                                <h5>{{ $row->deals_name}}</h5>
                                                <p><span><img src="/hijabenka/desktop/img/bb-stamp/bb-stamp.png" alt=""></span>{{ $row->stamp_price}} Berrybenka Stamp</p>
                                            </div>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="pagination deals-paging">
                            @include('pagination.desktop.paginationbb', ['paginator' => $deals, 'anchor' => 'stamp-deals'])
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
<!-- JS here -->
<script type="text/javascript">

$(document).ready(function(){
    var ajax_url = $('#ajax_url').val();

    var check_login = function(obj){
        if(obj.result == false && obj.need_refresh == true){
          location.reload();
        }
    };

    $(".fancybox").fancybox({ 

        afterLoad: function(current,previous){
            var id = $(current.content).attr('id');

            $('#' + id).find(".cancel").on('click',function(){
                $(".redeem-stamp").show();
                $(".deals-confirmation").hide();
            });

            $('#' + id).find(".redeem").on('click',function(){
                $(".loading-redeem").show();
                $(".deals-confirmation").hide();

                var deals_id = $(this).attr('data-id');

                $.ajax({
                    type:'post',
                    url:ajax_url + '/user/stamp/deals/redeem',
                    data:{'deals_id' : deals_id},
                    success:function(data){
                        var obj = jQuery.parseJSON(data);
                        check_login(obj);

                        $(".redeem-stamp").show();
                        $(".deals-confirmation").hide();
                        $(".loading-redeem").hide();

                        $('.redeem-note').empty();
                        $('.redeem-note').append(obj.result_message);

                    }
                });
            });

            $('#' + id).find(".redeem-stamp").on('click',function(){
                $(".redeem-stamp").hide();
                $(".deals-confirmation").show();
                $('.redeem-note').empty();
                $(".loading-redeem").hide();    
            });
        },

        beforeClose: function(){
            if ( $('.loading-redeem').css('display') == 'none' || $('.loading-redeem').css("visibility") == "hidden"){
                $('.redeem-note').empty();
                $(".redeem-stamp").show();
                $(".deals-confirmation").hide();
            }
        }
    });   
});
</script>
@endsection