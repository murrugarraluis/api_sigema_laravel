<!DOCTYPE html>
<html lang="es">

<head>
	@include('includes.head')
</head>

<body>
<header>
	@include('includes.header', ['title' => __('messages.' . $data['title'])])
</header>
<div>
	<table class="table-info">
		<tr>
			<td class="text-left">{{ __('messages.sort_by') }}: {{ __('messages.' . $data['sort_by']) }}</td>
		</tr>
	</table>
	<table class="table-info">
		<tr>
			<td class="text-left">{{ __('messages.start_date') }}: {{ $data['start_date'] }}</td>
			<td class="text-center">{{ __('messages.end_date') }}: {{ $data['end_date'] }}</td>
			<td class="text-right">{{ __('messages.report_date') }}: {{ $data['date_report'] }}</td>
		</tr>
	</table>
</div>
<hr>
<table class="table-data">
	<thead>
	<th scope="col" style="width: 5%">#</th>
	<th scope="col">{{ __('messages.lastname') }}</th>
	<th scope="col">{{ __('messages.name') }}</th>
	@if ($data['type'] == 'attended')
		<th scope="col" class="text-center" style="width: 10%">{{ __('messages.attendances') }}</th>
		<th scope="col" class="text-center" style="width: 10%">{{ __('messages.absences') }}</th>
		<th scope="col" class="text-center" style="width: 10%">{{ __('messages.justified_absences') }}</th>
		<th scope="col" class="text-center" style="width: 10%">{{ __('messages.unexcused_absences') }}</th>
		<th scope="col" class="text-center" style="width: 12%">{{ __('messages.working_hours') }}</th>
	@else
		<th scope="col" class="text-center">{{ __('messages.date') }}</th>
		<th scope="col" class="text-center">{{ __('messages.reason') }}</th>
		<th scope="col" class="text-center">{{ __('messages.description') }}</th>
	@endif
	</thead>
	<tbody>
	@if ($data['type'] == 'attended')
		@foreach ($data['employees']->jsonSerialize() as $key => $item)
			<tr>
				<td>{{ $key + 1 }}</td>
				<td>{{ $item['lastname'] }}</td>
				<td>{{ $item['name'] }}</td>
				<td class="text-center">{{ $item['attendances'] }}</td>
				<td class="text-center">{{ $item['absences']}}</td>
				<td class="text-center">{{ $item['justified_absences'] }}</td>
				<td class="text-center">{{ $item['unexcused_absences'] }}</td>
				<td class="text-center">{{ $item['working_hours'] }}</td>
			</tr>
		@endforeach
		<tr>
			<td colspan="3" class="text-center"><strong>{{ __('messages.total_employees') }}
					{{ $data['total_employees'] }}</strong></td>
			<td class="text-center"><strong>{{ $data['total_attendances'] }}</strong></td>
			<td class="text-center"><strong>{{ $data['total_absences'] }}</strong></td>
			<td class="text-center"><strong>{{ $data['total_justified_absences'] }}</strong></td>
			<td class="text-center"><strong>{{ $data['total_unexcused_absences'] }}</strong></td>
			<td class="text-center"></td>
		</tr>
	@else
		@php
			$number_employee = 0;
		@endphp
		@foreach ($data['employees']->jsonSerialize() as $key => $item)
			@if ($item['get_total_absences']->jsonSerialize())
				@php
					$number_employee += 1;
				@endphp
				@foreach ($item['get_total_absences']->jsonSerialize() as $key2 => $item2)
					<tr>
						{{--						@if ($key2 == 0) --}}
						{{--							<td rowspan="{{count($item["get_total_absences"])}}" class="align-middle">{{$number_employee}} --}}
						{{--							</td> --}}
						{{--							<td rowspan="{{count($item["get_total_absences"])}}" class="align-middle">{{$item["lastname"]}}</td> --}}
						{{--							<td rowspan="{{count($item["get_total_absences"])}}" class="align-middle">{{$item["name"]}}</td> --}}
						{{--						@endif --}}
						<td class="align-middle">{{ $key2 == 0 ? $number_employee : '' }}
						</td>
						<td class="align-middle">{{ $key2 == 0 ? $item['lastname'] : '' }}</td>
						<td class="align-middle">{{ $key2 == 0 ? $item['name'] : '' }}</td>
						<td>{{ $item2['date'] }}</td>
						<td style="font-style: italic;">{{ $item2['pivot']['missed_reason']?$item2['pivot']['missed_reason']:'NULL' }}</td>
						<td style="font-style: italic;">{{ $item2['pivot']['missed_description']? $item2['pivot']['missed_description']:'NULL' }}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="2"><strong>{{ __('messages.total_justified_absences') }}:
							{{ $item['justified_absences'] }}</strong></td>
					<td colspan="2"><strong>{{ __('messages.total_unexcused_absences') }}:
							{{ $item['unexcused_absences'] }}</strong>
					</td>
					<td colspan="2"><strong>{{ __('messages.total_absences') }}:
							{{ $item['absences'] }}</strong></td>
				</tr>
			@endif
		@endforeach
		<tr>
			<td colspan="6"><strong>{{ __('messages.total_employees') }} {{ $number_employee }}</strong>
			</td>
			{{--				<td class="text-center"><strong>{{$data["total_attendances"]}}</strong></td> --}}
			{{--				<td class="text-center"><strong>{{$data["total_absences"]}}</strong></td> --}}
			{{--				<td class="text-center"><strong>{{$data["total_justified_absences"]}}</strong></td> --}}
			{{--				<td class="text-center"></td> --}}
		</tr>
	@endif
	</tbody>
</table>
</body>

</html>
