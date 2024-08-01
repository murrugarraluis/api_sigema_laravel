<!DOCTYPE html>
<html lang="es">
<head>
	@include('includes.head')
</head>
<body>
<header>
	@include('includes.header',['title' => __('messages.work_sheet')])
</header>
<div>
	<table class="table-info">
		<tr>
			<td class="text-left">{{__('messages.date')}}: {{$data["date"]}}</td>
		</tr>
	</table>
	<table class="table-info">
		<tr>
			<td class="text-left">{{__('messages.name')}}: {{$data["machine"]["name"]}}</td>
			<td class="text-left">{{__('messages.brand')}}: {{$data["machine"]["brand"]}}</td>
			<td class="text-left">{{__('messages.model')}}: {{$data["machine"]["model"]}}</td>
		</tr>
	</table>
	<hr>
	<table class="table-info">
		<tr>
			<td>{{__('messages.pre_check')}}:</td>
		</tr>
		<tr>
			<td class="text-left">{{$data["description"]}}</td>
		</tr>
	</table>
</div>
<hr>
<div class="">
	<table class="table-data">
		<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">{{__('messages.start_date')}}</th>
			<th scope="col">{{__('messages.end_date')}}</th>
			<th scope="col" class="text-right" style="text-align: right">{{__('messages.time')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($data["working_hours"] as $key=>$item)
			<tr>
				<td scope="row">{{$key+1}}</td>
				<td>{{$item["date_time_start"]}}</td>
				<td>{{$item["date_time_end"]}}</td>
				<td class="text-right"
						style="text-align: right">{{$item["date_time_diff"]["hours"].":".$item["date_time_diff"]["minutes"].":".$item["date_time_diff"]["secons"]}}</td>
			</tr>
		@endforeach
		<tr>
			<td colspan="3"><strong>Total</strong></td>
			<td class="text-right" style="text-align: right"><strong>{{$data["working_hours_total"]}}</strong></td>
		</tr>
		</tbody>
	</table>
</div>
</body>
</html>
