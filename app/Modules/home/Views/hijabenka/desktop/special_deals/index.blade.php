@extends('layouts.hijabenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/home.css') }}">
@endsection

@section('content')

<div class="content" style="margin-top: 100px;">
	<div class="wrapper">
		<div class="bank-deals">
			<h1>Promotion</h1>
	        <div class="tab-menu">
	        	<ul>
	        		<?php $i = 1; ?>
	        		@foreach($category as $data)
		            	<li @if($i == 1) class=current @endif><a href="#{{ $data->category_url }}">{{ $data->category_name }}</a></li>
		            	<?php $i++; ?>
		            @endforeach
	            </ul>
	        </div>
	        <div class="tab">
	        	<?php $i = 1; ?>

	        	@foreach($category as $data)
		            <div class="list-deals tab-content" id="{{ $data->category_url }}" @if($i != 1) style=display:none; @endif>
		                <ul>
		                	@foreach($special_deals as $row)
		                		<?php
			                		// Define Start Date & End Date
				                  	$now 		= strtotime(date('Y-m-d H:i:s'));
				                  	$startdate 	= strtotime(date($row->special_deal_start_date));
				                  	$enddate 	= strtotime(date($row->special_deal_end_date));
				                ?>

				                @if($now >= $startdate && $now <= $enddate)
				                	@if($data->category_id == $row->special_deal_category)
					                    <li>
					                        <a href="{{ $row->special_deal_url }}">
					                        	<img src="<?php echo IMAGE_SPECIAL_DEAL_UPLOAD_PATH.$row->special_deal_image; ?>" alt="{{ $row->special_deal_name }}">
					                        </a>
					                    </li>
					                @endif
					            @endif
		                    @endforeach
		                </ul>
		            </div>
		            <?php $i++; ?>
		        @endforeach
	         </div>
	    </div>
	</div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection