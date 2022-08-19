@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Informe uso de espacios</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
		<li class="breadcrumb-item">Informes</li>
        <li class="breadcrumb-item">Informe uso de espacios</li>
    </ol>
@endsection
@section('content')
@php
	$total = 0;
	//Solo para depuracion
	use Carbon\Carbon;
	if(config('app.manolo') == 1){
		$f1=Carbon::parse("2022-08-01");
		$f2=Carbon::parse("2022-08-31");
	}
	$controller="reports";//Este es el nombre de metodo a ejecutar para la programacion de informes
@endphp
<div class="panel">
	<div class="panel-heading">
		<h3 class="panel-title">Informe de uso de puestos</h3>
		<span class="float-right" id="spin" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
	</div>
	<div class="panel-body">
		@if (checkPermissions(['Informes'],["R"]))
			<form action="{{url('/reports/heatmap/filter')}}" method="POST" class="ajax-filter">
				{{csrf_field()}}
				<input type="hidden" value="{{Auth::user()->id_cliente}}" name="id_cliente">
				@include('resources.combos_filtro',[$hide=['cli'=>1,'est'=>1,'head'=>1,'btn'=>1,'usu'=>1,'est_inc'=>1,'tip_mark'=>1,'tip_inc'=>1,'sup'=>1]])
				<div class="col-md-3" style="padding-left: 15px">
					@include('resources.combo_fechas')
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label>Formato</label>
						<select class="form-control selectpicker" required id="output" name="output">
							<option value="pantalla" data-content="<i class='fas fa-desktop' style='color: #4682b4'></i> Pantalla"> </option>
							<option value="pdf" data-content="<i class='fas fa-file-pdf' style='color: #b22222'></i> PDF"> </option>
							<option value="excel" data-content="<i class='fas fa-file-excel' style='color: #2e8b57'></i> Excel"> </option>
						</select>
					</div>
				</div>

				<div class="col-md-2" id="orientation" style="display: none">
					<div class="form-group">
						<label>Orientacion</label>
						<select class="form-control selectpicker" required id="orientation" name="orientation">
							<option value="pantalla" data-content="<i class='far fa-rectangle-landscape'></i> Horizontal"> </option>
							<option value="pdf" data-content="<i class='far fa-rectangle-portrait'></i> Vertical"> </option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-right">
						<button id="btn_submit" class="btn btn-primary btn-lg mb-2 mr-2" style="margin-top:10px"><i class="mdi mdi-file-document"></i> Obtener informe</button>
					</div>
				</div>
			</form>
		@endif
	</div>
</div>
@include('resources.programacion_informe')
<br>
<div class="table m-t-40 overflow-hidden table-vcenter">

	@include('resources.informes_imprimir_resumen')
	<table class="table table-bordered table-condensed table-hover  table-striped" style="font-size: 12px;">
		<tbody id="myFilter" >
		</tbody>
	</table>
</div>
@php
    $nombre_empresa = "Informe de uso de espacios" . " ";
    $___cl = \DB::table('clientes')->where('id_cliente',Auth::user()->id_cliente)->first();
    if(isset($___cl) && ($___cl->nom_cliente))
        $nombre_empresa .= $___cl->nom_cliente;
    else $nombre_empresa .= "SPOTDESKING";
@endphp
@endsection

@section('scripts4')

<script>
	document.title = '{{$nombre_empresa}}';
	$('.informes').addClass('active active-sub');
    $('.inf_puestos').addClass('active-link');

	$('#output').change(function(){
        if($(this).val()=="pdf"){
            $('#orientation').show();
        } else $('#orientation').hide();
    })
</script>
@endsection
