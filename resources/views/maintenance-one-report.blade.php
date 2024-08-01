<!DOCTYPE html>
<html lang="es">
<head>
	@include('includes.head')
</head>
<body>
<header>
	@include('includes.header',['title' => __('messages.title_maintenance_sheet')])
</header>
<div>
	<table class="table-info">
		<tr>
			<td class="text-left">{{__('messages.date')}}: {{$data["date"]}}</td>
			<td class="text-center">{{__('messages.type')}}: {{__("messages.".strtolower($data["maintenance_type"]["name"]
			))}}</td>
			<td class="text-right">{{__('messages.responsible')}}: {{$data["responsible"]}}</td>
		</tr>
	</table>
	<table class="table-info">
		<tr>
			<td class="text-left">{{__('messages.supplier')}}: {{$data["supplier"]["name"]}}</td>
			<td class="text-left">{{__('messages.technical')}}: {{$data["technical"]}}</td>
			<td class="text-right">{{__('messages.machine')}}: {{$data["machine"]["name"]}}</td>
		</tr>
	</table>
	<hr>
	<table class="table-info">
		<tr>
			<td>{{__('messages.description')}}:</td>
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
			<th scope="col" style="width: 5%">#</th>
			<th scope="col">{{__('messages.serie_number')}}</th>
			<th scope="col">{{__('messages.description')}}</th>
			<th scope="col" class="text-right" style="width: 10%">{{__('messages.price')}}</th>
			<th scope="col" class="text-right" style="width: 10%">{{__('messages.quantity')}}</th>
			<th scope="col" class="text-right" style="width: 12%">{{__('messages.import')}}</th>
		</tr>
		</thead>
		<tbody>
		@foreach($data["detail"]->jsonSerialize() as $key=>$item)
			<tr>
				<td scope="row">{{$key+1}}</td>
				<td>{{$item["article"]?$item["article"]["serie_number"]:"XXXXXXXXXXXXX"}}</td>
				<td>{{$item["article"]?$item["article"]["name"]:$item["description"]}}</td>
				<td class="text-right" style="text-align: right">{{number_format((float)$item["price"], 2, '.', '')}}
				<td class="text-right" style="text-align: right">{{$item["quantity"]}}</td>
				<td class="text-right" style="text-align: right">{{number_format((float)$item["price"]*$item["quantity"], 2, '.', '')}}</td>
			</tr>
		@endforeach
		<tr>
			<td colspan="5"><strong>Total</strong></td>
			<td class="text-right" style="text-align: right" ><strong>{{number_format((float)$data["amount"], 2, '.', '')}}</strong></td>
		</tr>
		</tbody>
	</table>
</div>
</body>
</html>
