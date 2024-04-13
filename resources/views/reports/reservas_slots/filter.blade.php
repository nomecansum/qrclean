@php
	use Carbon\Carbon;
	use Carbon\CarbonPeriod;
	($total = 0); 
	$clientes=$informe->pluck('id_cliente')->unique();
	$usuarios=$informe->pluck('id_user')->unique();
	$cnt_fechas=$informe->pluck('fecha')->unique()->count();  
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$filas=$informe->count();
	$nombre_informe="Informe de uso de puestos";
	$date = explode(" - ",$r->fechas);
	$f1 = adaptar_fecha($date[0]);
	$f2 = adaptar_fecha($date[1]);
	$edificios=$informe->pluck('id_edificio')->unique();
	$periodo = CarbonPeriod::create(Carbon::parse($f1), Carbon::parse($f2));
	$tipos_puestos=$informe->pluck('id_tipo_puesto')->unique();
	//Vamos a calcular el numero maximo de slots por dia de la semana que determinara el maximo de columnas de la tabla
	$max_slots=0;
	foreach($tipos_puestos as $tipo){
		$datos_tipo=\App\Models\puestos_tipos::find($tipo);
		for($i=0;$i<7;$i++){
			$slots=Collect(json_decode($datos_tipo->slots_reserva));
			$slots=$slots->where('dia_semana',$i);
			$max_slots=max($max_slots,$slots->count());
		}
	}
@endphp
@if($r->output=="pdf" || (isset($r->email_schedule)&&$r->email_schedule==1))
<style type="text/css">
	thead {
		display: table-row-group;
	}
	tr {
		page-break-before: always;
		page-break-after: always;
		page-break-inside: avoid;
	}
	table {
		word-wrap: break-word;
	}
	table td {
		word-break: break-all;
	}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ url('/css/nifty.min.css') }}" rel="stylesheet">
<link href="{{ url('/css/themes/type-e/theme-navy.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL('/css/materialdesignicons.min.css') }}">
<link href="{{ asset('/plugins/fontawesome6/css/all.min.css') }}" rel="stylesheet">
<link href="{{ url('/css/mosaic.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL('/plugins/bootstrap-table/bootstrap-table.min.css') }}">

<div class="table-responsive m-t-40 overflow-hidden">			
<span id="resumen_informe"></span>
@endif


@if($clientes->isEmpty())
	@if($r->output=="pdf" || $r->output=="excel")
	<table class="table table-bordered table-condensed table-hover table-informes" style="font-size: 12px">
		<tbody id="myFilter" >
	@endif
	<div class="text-center">
		<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>		
		<h3 class="text-warning">No hay datos para mostrar</h3>
	</div>
	@if($r->output=="pdf" || $r->output=="excel")
		</tbody>
	</table>
	@endif
@endif


@foreach($clientes as $cliente)
	@if($r->output=="pdf" || $r->output=="excel")
		<table class="table table-bordered table-condensed table-hover table-informes table-striped" style="font-size: 12px">
			<tbody id="myFilter" >
	@endif
	@php
		$inf=$informe->where('id_cliente',$cliente);
	@endphp	
	@if($informe->count()>0)
		<tr>
			<td colspan="11" >
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		<tr>
			<td colspan="11">
				<h4 class="text-muted text-center">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
			</td>
		</tr>
	@endif
	@foreach($periodo as $fecha)	
		<tr>
			<td colspan="11">
				<h4 class="text-muted text-center"><h3>Dia {!! beauty_fecha($fecha,0) !!}</h3></h4>
			</td>
		</tr>
		@foreach($tipos_puestos as $tipo)
			@php
				$datos_tipo=\App\Models\puestos_tipos::find($tipo);
				
			@endphp
			@foreach($edificios as $edificio)
				@php
					$datos_edificio=\App\Models\edificios::find($edificio);
					$inf=$informe->where('id_cliente',$cliente)->where('id_edificio',$edificio);
					$aforo_max=$inf->where('id_tipo_puesto',$tipo)->count();
					$slots=Collect(json_decode($datos_tipo->slots_reserva));
					$diasemana=Carbon::parse($fecha)->dayOfWeek;
					$slots=$slots->where('dia_semana',$diasemana-1)->sortby('hora_inicio');
					$total_reservas=0;
					$total_aforo=0;
				@endphp
				<tr>
					<th  class="text-bold text-center">{{ $datos_tipo->des_tipo_puesto }}</th>
					<th  class="text-center" >Aforo Max</th>
					@foreach($slots as $slot)
						<th class="text-center">{{ $slot->hora_inicio }} - {{ $slot->hora_fin }}<br>{{ $slot->etiqueta }}</th>
					@endforeach
					{{-- Y ahora rellenaremos con celdas vacios hasta el maximo de slots --}}
					@for($i=0;$i<($max_slots-$slots->count());$i++)
						<th></th>
					@endfor
					<th  class="text-center" >Total reservas</th>
				</tr>
				<tr>
					<td  class="text-end">{{ $datos_edificio->abreviatura }}</td>
					<td  class="text-center">{{ $aforo_max }}</td>
					@foreach($slots as $slot)
						@php
							$total_aforo+=$aforo_max;
							$fecha=Carbon::parse($fecha)->format('Y-m-d');
							$cnt_reservas=$reservas->where('id_tipo_puesto',$tipo)->where('id_edificio',$edificio)->where('fec_reserva',Carbon::parse($fecha.' '.$slot->hora_inicio))->count();
							$total_reservas+=$cnt_reservas;
						@endphp
						<td class="text-center">{{ $cnt_reservas }}</td>
					@endforeach
					{{-- Y ahora rellenaremos con celdas vacios hasta el maximo de slots --}}
					@for($i=0;$i<($max_slots-$slots->count());$i++)
						<td class="text-center">-</td>
					@endfor
					<td  class="text-center">{{ $total_reservas }}</td>
				</tr>
			@endforeach
		@endforeach
	@endforeach

	

	@foreach ($inf as $puesto)
		
		
			{{-- <tr class="text-center">
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>
					@isset($puesto->icono_tipo)
						<i class="{{ $puesto->icono_tipo }} fa-2x" style="color: {{ $puesto->color_tipo }}"></i>
					@endisset
					{{ $puesto->cod_puesto }}
				</td>
				<td>{{ $puesto->usado }}</td>
				<td>{{ $puesto->disponible }}</td>
				<td>{{ $puesto->limpieza }}</td>
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>{{ $puesto->cambios }}</td>
				<td>{{ $puesto->incidencias_abiertas }}</td>
				<td>{{ $puesto->incidencias_cerradas }}</td>
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>{{ ($puesto->incidencias_abiertas??0+$puesto->incidencias_cerradas??0)==0?'':$puesto->incidencias_abiertas??0+$puesto->incidencias_cerradas??0 }}</td>
				<td>{{ $puesto->reservas_usadas }}</td>
				<td>{{ $puesto->reservas_anuladas }}</td>
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>{{ $puesto->reservas }}</td> 
			</tr> --}}

	@endforeach
	@if($r->output=="pdf" || $r->output=="excel")
		</tbody>
	</table>
	@endif
	@if($r->output=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach
<script>
	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('puestos');
</script>