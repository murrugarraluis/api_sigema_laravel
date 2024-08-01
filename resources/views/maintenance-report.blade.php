<!DOCTYPE html>
<html lang="es">
<head>
	@include('includes.head')
</head>
<body>
<header>
	@include('includes.header',['title' => __('messages.title_maintenance_sheet_report')])
</header>
<div>
	<table class="table-info">
		<thead>
		<tr>
			<td scope="col" colspan="2">{{__('messages.start_date')}}: {{$data["start_date"]}}</td>
			<td scope="col" colspan="2">{{__('messages.end_date')}}: {{$data["end_date"]}}</td>
{{--			<td scope="col">{{__('messages.type')}}: {{$data["type"]}}</td>--}}
{{--			<td scope="col">{{__('messages.sort_by')}}: {{__("messages.".$data["sort_by"])}}</td>--}}
{{--			<td scope="col">{{__('messages.date')}}: {{date('Y-m-d H:i:s')}}</td>--}}
		</tr>
		<tr>
			<td scope="col" colspan="2">{{__('messages.type')}}: {{$data["type"]}}</td>
			<td scope="col" colspan="2">{{__('messages.sort_by')}}: {{__("messages.".$data["sort_by"])}}</td>
			<td scope="col">{{__('messages.date')}}: {{date('Y-m-d H:i:s')}}</td>
		</tr>
		</thead>
	</table>
</div>
<hr>
<div class="">
	<table class="table-data">
		<thead>
		<tr>
			{{--            <th scope="col">#</th>--}}
			<th scope="col">{{__('messages.serie_number')}}</th>
			<th scope="col">{{__('messages.machine')}}</th>
			<th scope="col">{{__('messages.brand')}}</th>
			<th scope="col">{{__('messages.model')}}</th>
			@if($data["type"] == "resumen")
				<th scope="col" style="width: 5%">{{__('messages.maintenance_count')}}</th>
				<th scope="col">{{__('messages.amount')}}</th>
			@else
				<th scope="col">{{__('messages.code')}}</th>
				<th scope="col">{{__('messages.date')}}</th>
				<th scope="col">{{__('messages.type')}}</th>
				<th scope="col">{{__('messages.supplier')}}</th>
				<th scope="col">{{__('messages.responsible')}}</th>
				<th scope="col" class="text-right">{{__('messages.amount')}}</th>
			@endif


		</tr>
		</thead>
		<tbody>
		@if($data["type"] == "resumen")
			@foreach($data["data"] as $item)
				<tr>
					{{--                <th scope="row">{{$key}}</th>--}}
					<td>{{$item["serie_number"]}}</td>
					<td>{{$item["name"]}}</td>
					<td>{{$item["brand"]}}
					<td>{{$item["model"]}}</td>
					<td>{{$item["maintenance_count"]}}</td>
					<td style="text-align: right">{{number_format((float)$item["amount"], 2, '.', '')}}</td>
				</tr>
			@endforeach
		@else
			@foreach($data["data"] as $item)
				@foreach($item["maintenance_sheets"] as $key => $item2)
					<tr>
						{{--                        @if($key == 0)--}}
						{{--                            <td rowspan="{{count($item["maintenance_sheets"])}}" class="align-middle">{{$item["serie_number"]}}</td>--}}
						{{--                            <td rowspan="{{count($item["maintenance_sheets"])}}" class="align-middle">{{$item["name"]}}</td>--}}
						{{--                            <td rowspan="{{count($item["maintenance_sheets"])}}" class="align-middle">{{$item["brand"]}}--}}
						{{--                            <td rowspan="{{count($item["maintenance_sheets"])}}" class="align-middle">{{$item["model"]}}</td>--}}
						{{--                        @endif--}}
						<td class="align-middle">{{$key == 0?$item["serie_number"]:''}}</td>
						<td class="align-middle">{{$key == 0?$item["name"]:''}}</td>
						<td class="align-middle">{{$key == 0?$item["brand"]:''}}
						<td class="align-middle">{{$key == 0?$item["model"]:''}}</td>

						<td>{{$item2["code"]}}</td>
						<td>{{$item2["date"]}}</td>
						<td>{{$item2["maintenance_type"]["name"]}}</td>
						<td>{{$item2["supplier"]["name"]}}</td>
						<td>{{$item2["responsible"]}}</td>
						<td style="text-align: right">{{number_format((float)$item2["amount"], 2, '.', '')}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="9"><strong>{{__('messages.number_maintenance')}}: {{count($item["maintenance_sheets"])}}</strong>
					</td>
					<td style="text-align: right"><strong>{{number_format((float)$item["amount"], 2, '.', '')}}</strong></td>
				</tr>
			@endforeach
		@endif
		<tr>
			<td colspan="{{$data["type"] == "resumen"?5:9}}">
				<strong style="margin-right: 7px">{{__('messages.number_machines')}}: {{$data["total_machines"]}} </strong>
				<strong>{{__('messages.number_maintenances')}}: {{$data["total_maintenances"]}} </strong>
			</td>
			<td style="text-align: right"><strong>{{number_format((float)$data["total_amount"], 2, '.', '')}}</strong></td>
		</tr>
		</tbody>
	</table>
</div>
</body>
</html>
