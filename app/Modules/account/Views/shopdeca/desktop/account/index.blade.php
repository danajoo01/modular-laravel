@extends('layouts.shopdeca.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/user.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/catalog-list.css') }}">
@endsection

@section('content')

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
							<div class="benka-point-status">
								<div class="benka-poin-wrapper clearfix">
									<span class="left"><i class="fa fa-gift"></i></span>
									<div class="promo-info left">
										Benka Poin Anda Adalah<br>
										<p class="level">IDR {{ number_format($user->customer_credit,0,".",".") }}</p>
									</div>
								</div>
							</div>
							<div class="benka-point-history">
								<h1 class="clearfix point-history-header"><i class="fa fa-history" aria-hidden="true"></i>Benka Point History</h1>
								<table width="100%" border="1" cellspacing="0" cellpadding="0" class="bpoint-history">
									<thead>
										<tr>
											<td>#</td>
											<td>Tanggal</td>
											<td>Tipe</td>
											<td>Deskripsi</td>
											<td>Total</td>
										</tr>
									</thead>
									<tbody>
									@if ($credits_history)
										<?php $i = $credits_history->firstItem(); ?>
										@foreach ($credits_history as $row)										
										<tr>
											<td>{{ $i }}</td>
											<td>{{ $row->credithistory_date }}</td>
											<td>{{ $row->credithistory_type }}</td>
											<td>{{ $row->credithistory_desc }}</td>
											<td>IDR {{ $row->credithistory_amount }}</td>
										</tr>
										<?php $i++ ?>
										@endforeach
									@else 
										<tr>
											<td colspan="5">Histori Kredit Kosong</td>
										</tr>
									@endif
									</tbody>
								</table>
							</div>
							@if ($credits_history)
							<div class="pagination right">
								{!! $credits_history->links() !!}								
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection