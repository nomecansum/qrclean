@php
	use Carbon\Carbon;
	($total = 0); 
	$clientes=$informe->pluck('id_cliente')->unique();
	$usuarios=$informe->pluck('id')->unique();
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$filas=$informe->count();
	$nombre_informe="Informe de estado de usuarios";
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
		$puestos=DB::table('puestos')->where('id_cliente',$cliente)->get();
		$plantas=DB::table('plantas')->where('id_cliente',$cliente)->get();
		$plantas_usuario=DB::Table('plantas_usuario')->get();
	@endphp
	@if($informe->count()>0)
		<tr>
			<td colspan="4" >
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		
	@endif

		<tr>
			<th></th>
			<th @if($r->output=="excel") style="background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Usuario</th>
			<th class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Plantas</th>
			<th class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Puesto asignado</th>
		</tr>

	@foreach ($inf as $dato)
		@php
			$pu=$plantas_usuario->where('id_usuario',$dato->id)->pluck('id_planta')->toArray();
			$pl=$plantas->wherein('id_planta',$pu)->pluck('abreviatura')->toArray();
		@endphp

			<tr>
				<td></td>
				<td>{{ $dato->name }}</td>
				<td>
					{{implode(", ",$pl)}}
				</td>
				<td>
				@php
					$data=json_decode($dato->list_puestos_preferidos);
					$preferidos=Collect($data)->where('tipo','pu');
					$puestos_preferidos=$preferidos->pluck('text')->toArray();
					$preferidos=Collect($data)->where('tipo','zo');
					$zonas_preferidas=$preferidos->pluck('text')->toArray();
				@endphp
				{{ implode(", ",$puestos_preferidos) }}
				@if($zonas_preferidas)
					<br>
					{{ implode(", ",$zonas_preferidas) }}
				@endif
				</td>
			</tr>

	@endforeach
	@if($r->output=="pdf" || $r->output=="excel")
		</tbody>
	</table>
	@endif
	@if($r->output=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach
<script>
	$('#resumen_informe').html(" {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('puestos');
</script>