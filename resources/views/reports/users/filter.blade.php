@php
	use Carbon\Carbon;
	($total = 0); 
	$clientes=$informe->pluck('id_cliente')->unique();
	$usuarios=$informe->pluck('id_user')->unique();
	$cnt_fechas=$informe->pluck('fecha')->unique()->count();  
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$filas=$informe->count();
	$nombre_informe="Informe de actividad de usuarios";
	$date = explode(" - ",$r->fechas);
	$f1 = adaptar_fecha($date[0]);
	$f2 = adaptar_fecha($date[1]);
@endphp

@if($r->document=="pdf" || (isset($r->email_schedule)&&$r->email_schedule==1))
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
<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">


@if($clientes->isEmpty())
	@if($r->document=="pdf" || $r->document=="excel")
	<table class="table table-bordered table-condensed table-hover table-informes overflow-hidden table-vcenter" style="font-size: 12px">
		<tbody id="myFilter" >
	@endif
	<div class="text-center">
		<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>		
		<h3 class="text-warning">No hay datos para mostrar</h3>
	</div>
	@if($r->document=="pdf" || $r->document=="excel")
		</tbody>
	</table>
	@endif
@endif


@foreach($clientes as $cliente)
<table class="table table-bordered table-condensed table-hover table-informes overflow-hidden table-vcenter" style="font-size: 12px">
	@php
		$inf=$informe->where('id_cliente',$cliente);
	@endphp	
	<thead>
	@if($informe->count()>0)
		<tr>
			<td @if($r->output!=="excel") colspan="9" @endif>
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		<tr>
			<td  @if($r->output!=="excel") colspan="9" class="text-center" @endif>
				<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
			</td>
		</tr>
	@endif
	
		<th></th>
		<th></th>
		<th  class="text-center font-bold">Acciones</th>
		<th class="text-center font-bold">Capturar</th>
		<th class="text-center font-bold">Liberar</th>
		<th class="text-center font-bold">Reservas</th>
		<th class="text-center font-bold">Usadas</th>
		<th class="text-center font-bold">Anuladas</th>
		<th class="text-center font-bold">Incidencias</th>
	</thead>
	<tbody>
	
	
	@foreach ($usuarios as $u)
		@php
			$inf=$informe->where('id_user',$u);
			$fechas=$inf->pluck('fecha')->unique();
			$inc_usuario=$incidencias->where('id_usuario',$u);
			$reservas_usuario=$reservas->where('id_usuario',$u);
			$reservas_anuladas=$reservas->where('id_usuario',$u)->where('mca_anulada','S');
			$reservas_usadas=$reservas->where('id_usuario',$u)->wherenotnull('fec_utilizada');
			$utilizados=$inf->where('id_estado',1);
			$dejados=$inf->where('id_estado',2);
		@endphp	
		<tr class="text-center">
			<td style="width: 60px" class="text-center" rowspan="2">
				@if($r->output!=="excel")
					@if (isset($inf->first()->img_usuario ) && $inf->first()->img_usuario!='' && $r->output!=="excel" )
						<img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$inf->first()->img_usuario) }}" class="img-circle" style="height: 50px">
					@else
						{!! icono_nombre($inf->first()->name) !!}
					@endif
				@endif
			</td>
			<td rowspan="2">
				{{ $inf->first()->name }}
			</td>
			<td class="add_tooltip" title="{{ $inf->count() }} Escaneos de puesto">
				{{ $inf->count() }}
			</td>
			<td  class="add_tooltip" title="{{ $utilizados->count() }} Puestos capturados">
				{{ $utilizados->count() }}
			</td>
			<td  class="add_tooltip" title=" {{ $dejados->count() }} Puestos liberados">
				{{ $dejados->count() }}
			</td>
			<td  class="add_tooltip" title="{{ $reservas_usuario->count() }} Total reservas">
				{{ $reservas_usuario->count() }}
			</td>
			<td  class="add_tooltip" title="{{ $reservas_usadas->count() }} Reservas usadas">
				{{ $reservas_usadas->count() }}
			</td>
			<td  class="add_tooltip" title="{{ $reservas_anuladas->count() }} Reservas anuladas">
				{{ $reservas_anuladas->count() }}
			</td>
			<td  class="add_tooltip" title="{{ $inc_usuario->count() }} Incidencias abiertas">
				{{ $inc_usuario->count() }}
			</td>
		</tr>
		<tr>
			<td colspan="7">
				<div class="d-flex flex-wrap">
					@foreach($fechas as $f)
					<div class="pad-all" style="width: 20%">
						@php
							$puestos=$inf->where('fecha',$f)
						@endphp	
						<span class="w-100 text-center" style="font-size: 16px; font-weight: bold">{{ Carbon::parse($f)->format('d/m/Y') }}</span> 
							
						@foreach($puestos as $p) 	
						 <li>
								@if($p->id_estado==1) <i class="fas fa-sign-out-alt text-success"></i> @else <i class="fas fa-sign-in-alt text-danger"></i>@endif
								{{ $p->cod_puesto }}<span style="font-size: 10px"> [{{ Carbon::parse($p->fecha_log)->format('H:i') }}]</span>
								
						 </li> 
						 @endforeach
						
					</div>
					@endforeach
				</div>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
	@if($r->document=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach



<script>
	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('users');
</script>