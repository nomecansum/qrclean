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


@if($clientes->isEmpty())
	@if($r->document=="pdf" || $r->document=="excel")
	<table class="table table-bordered table-condensed table-hover table-informes" style="font-size: 12px">
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
	@if($r->document=="pdf" || $r->document=="excel")
		<table class="table table-bordered table-condensed table-hover table-informes" style="font-size: 12px">
			<tbody id="myFilter" >
	@endif
	@php
		$inf=$informe->where('id_cliente',$cliente);
	@endphp	
	@if($informe->count()>0)
		<tr>
			<td @if($r->output!=="excel") colspan="2" @endif>
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		<tr>
			<td  @if($r->output!=="excel") colspan="2" class="text-center" @endif>
				<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
			</td>
		</tr>
	@endif
	
	@foreach ($usuarios as $u)
		@php
			$inf=$informe->where('id_user',$u);
			$fechas=$inf->pluck('fecha')->unique();
		@endphp	
		<tr>
			<td style="width: 60px" class="text-center">
				@if($r->output!=="excel")
					@if (isset($inf->first()->img_usuario ) && $inf->first()->img_usuario!='' && $r->output!=="excel" )
						<img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$inf->first()->img_usuario) }}" class="img-circle" style="height: 50px">
					@else
						{!! icono_nombre($inf->first()->name) !!}
					@endif
				@endif
			</td>
			<td>
				{{ $inf->first()->name }}
			</td>
		</tr>
		<tr>
			@if($r->output!=="excel")<td></td>@endif
			<td>
				@foreach($fechas as $f)
				<ul>
					@php
						$puestos=$inf->where('fecha',$f)->unique();
					@endphp	
					<li><b>{{ Carbon::parse($f)->format('d/m/Y') }}</b>: 
						<ul>
							@foreach($puestos as $p) <li>{{ $p->cod_puesto }} </li> @endforeach
						</ul>
					</li>
				</ul>
				@endforeach
			</td>
		</tr>
	@endforeach
	@if($r->document=="pdf" || $r->document=="excel")
		</tbody>
	</table>
	@endif
	@if($r->document=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach



<script>
	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('users');
</script>