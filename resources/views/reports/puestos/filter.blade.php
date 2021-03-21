@php
	use Carbon\Carbon;
	($total = 0); 
	$clientes=$informes->pluck('cod_cliente')->unique();
	$cnt_empleados=$informes->pluck('cod_empleado')->unique()->count();  
	$cnt_fechas=$informes->pluck('fecha')->unique()->count();  
	$cnt_clientes=$informes->pluck('cod_cliente')->unique()->count();
	$filas=$informes->count();
	$nombre_informe="Informe de accesos";
	$date = explode(" - ",$r->rango);
	$f1 = adaptar_fecha($date[0]);
	$f2 = adaptar_fecha($date[1]);
	$cod_departamento=0;
	$cod_centro=0;
@endphp
@if($r->document=="pdf" || (isset($r->email_schedule)&&$r->email_schedule==1))
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="{{url('monster-admin')}}/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"  media="all">
<link href="{{url('datatables')}}/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" media="all" />
<link href="{{url('monster-admin')}}/assets/plugins/tablesaw-master/dist/tablesaw.css" rel="stylesheet" media="all">
<link href="{{url('monster-admin/main')}}/css/style_{{ Auth::user() ? Auth::user()->css_style : ""  }}.css" rel="stylesheet" media="all">
<link href="{{ url('monster-admin/main') }}/css/colors/blue.css" id="theme" rel="stylesheet" media="all">
<link href="{{url('monster-admin')}}/assets/plugins/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" media="all">
<link href="{{url('css')}}/cucoweb.css" rel="stylesheet" type="text/css"  media="all"/>
<div class="table-responsive m-t-40 overflow-hidden">			
<span id="resumen_informe"></span>
@endif

@if($r->document=="pdf" || $r->document=="excel")
<table class="table table-bordered table-condensed table-hover table-informes" style="font-size: 12px">
	<tbody id="myFilter" >
@endif
@if($clientes->isEmpty())
<div class="text-center">
	<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>		
	<h3 class="text-warning">No hay datos para mostrar</h3>
</div>
@endif
@if($r->document=="pdf" || $r->document=="excel")
	</tbody>
</table>
@endif

@foreach($clientes as $cliente)
	@if($r->document=="pdf" || $r->document=="excel")
		<table class="table table-bordered table-condensed table-hover table-informes" style="font-size: 12px">
			<tbody id="myFilter" >
	@endif
	@php
		$inf=$informes->where('cod_cliente',$cliente);
	@endphp	
	@if($informes->count()>0)
		<tr>
			<td colspan="8">
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		<tr>
			<td colspan="8" class="text-center">
				<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
			</td>
		</tr>
		<tr>
			@if (showCustomerColumn())<th style="width: 10%">{{ trans('strings.client') }}</th>@endif
			<th  @if($r->document!="excel")style="width: 40px"@else style="width: 5px" @endif>{{trans('strings.id')}}</th>
			<th  @if($r->document!="excel")style="width: 30%"@else style="width: 50px" @endif>{{trans('strings.employee')}}</th>
			@if(permiso_cliente('selector_centro_departamento'))
				<th @if($r->document!="excel")style="width: 15%"@else style="width: 20px" @endif>{{trans('strings.center')}}</th>
				<th @if($r->document!="excel")style="width: 15%"@else style="width: 20px" @endif>{{trans('strings._centers.department')}}</th>
			@endif
			@if(permiso_cliente('mostrar_dispositivos_empleado'))
				<th @if($r->document!="excel")style="width: 20%"@else style="width: 20px" @endif>{{trans('strings.device')}}</th>
			@endif
			<th @if($r->document!="excel")style="width: 10%"@else style="width: 15px" @endif>Fecha</th>
		</tr>
	@endif
	
	@foreach ($inf as $i)
	{{-- @php dd($i); @endphp --}}
		{{-- Agrupacion por departamento o centro ponemos una fila de titulo --}}
		@if($r->type=="nom_departamento"&&$i->cod_departamento!=$cod_departamento)
			<tr>
				<td colspan="8" class="bg-light font-20 font-weight-bold">{{$i->nom_departamento}}</td>
			</tr>
		@endif
		@if($r->type=="des_centro"&&$i->cod_centro!=$cod_centro)
			<tr>
				<td colspan="8" class="bg-light font-20 font-weight-bold">{{$i->des_centro}}</td>
			</tr>
		@endif
		<tr>
			@if(showCustomerColumn())<td>{{ $i->nom_cliente }}</td>@endif
			<td>{{ $i->cod_interno  }}</td>
			<td>{{ $i->nom_empleado  }} {{ $i->ape_empleado  }}</td>
			@if(permiso_cliente('selector_centro_departamento'))
				<td>{{ $i->des_centro}}</td>
				<td>{{ $i->nom_departamento  }}</td>
			@endif
			@if(permiso_cliente('mostrar_dispositivos_empleado'))
				<td>{{ $i->nom_dispositivo}}</td>
			@endif
			<td>{{\Carbon\Carbon::parse($i->fec_marcaje)->format('d/m/Y H:i')}}</td>		
		</tr>
		@php
			$cod_departamento=$i->cod_departamento;
			$cod_centro=$i->cod_centro;	
		@endphp
	@endforeach
	@if($r->document=="pdf" || $r->document=="excel")
		</tbody>
	</table>
	@endif
	@if($r->document=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach

@if($r->document=="pdf" || $r->document=="excel")
		</tbody>
	</table>
@endif

<script>
	$('#resumen_informe').html("{{ $cnt_clientes }} Empresas | {{ $cnt_empleados }} Empleados | {{ $cnt_fechas }} Dias | {{ $filas }} Filas ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('accesos');
</script>