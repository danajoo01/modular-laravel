<?php $time = microtime(true); 
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
            <div class="about-wrapper tnc-wrapper b-list">
                <h1>TAGS LIST</h1>

                <div class="brand-list-section">
                    
                    
                    @if($all_data)
                        @foreach($all_data as $keyData => $valueData)
                        <div class="brand-group-wrap">
                            <div class="brand-group-head">{{ ucwords(strtolower($keyData)) }}</div>
                            <ul class="brand-list">
                                @foreach($valueData as $key => $value)
                                    <li><a href="{{ url('/tag') }}/{{ $value->tag_url }}" style="background:none;padding:0;text-transform: none;color: inherit;letter-spacing: 0;">{{ ucwords(strtolower($value->tag_name)) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    @endif
                    
                </div>            
            </div>
        </div>
    </div>
</div>

@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
       @include('marketing-tag.hijabenka.desktop.tags')
    @endif
@endsection
