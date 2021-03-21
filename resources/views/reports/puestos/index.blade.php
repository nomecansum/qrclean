@extends('layout')
@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-6 col-12 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">{{trans('strings.reports')}}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{trans('strings.home')}}</a></li>
				<li class="breadcrumb-item">{{trans('strings.reports')}}</li>
				<li class="breadcrumb-item active">Informe de uso de puestos</li>
            </ol>
        </div>
        <div class="col-md-6 col-4 align-self-center">
            <a href="{{url('/')}}" class="btn float-right hidden-sm-down btn-warning"><i class="mdi mdi-chevron-double-left"></i> {{trans('strings.back')}}</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
				<h2>Informe de accesos</h2>
			<div class="card">
				@php
					$total = 0;
					//Solo para depuracion
					use Carbon\Carbon;
					if(config('app.manolo') == 1){
                        $startdate=Carbon::parse("2020-09-01");
                        $enddate=Carbon::parse("2020-09-31");
					}
					$controller="reports";//Este es el nombre de metodo a ejecutar para la programacion de informes
				@endphp
			    <div class="card-body">

			    	@if (checkPermissions(['Informes'],["R"]))
					<form action="{{url('/reports/puestos/filter')}}" method="POST" class="ajax-filter">
						{{csrf_field()}}
						<input type="hidden" value="{{Auth::user()->cod_cliente}}" name="id_cliente">
						@include('resources.combos_filtro',[$hide=['btn'=>1]])
						{{-- <div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Agrupar</label>
									<select name="type" class="form-control">
										<option value="empleado">Por empleado</option>
										<option value="nom_departamento">Por departamento</option>
										<option value="des_centro">Por centro</option>
									</select>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label>Ordenar por</label>
									<select name="order" class="form-control">
										<option value="ape_empleado">Nombre de empleado</option>
										<option value="cod_interno">ID de empleado</option>
									</select>
								</div>
							</div> --}}

							<div class="col-md-3">
								<div class="form-group">
									<label>Formato</label>
									<select name="document" id="document" class="form-control select2 select2-hidden-accessible icons_select2">
										<option value="pantalla" data-icon="mdi mdi-monitor"> Pantalla</option>
										<option value="pdf" data-icon="mdi mdi-file-pdf"> PDF</option>
										<option value="excel" data-icon="mdi mdi-file-excel"> Excel</option>
									</select>
								</div>
							</div>

							<div class="col-md-2" id="orientation" style="display: none">
								<div class="form-group">
									<label>Orientacion</label>
									<select name="orientation"  class="form-control select2 select2-hidden-accessible icons_select2" >
										<option value="h" data-icon="mdi mdi-crop-landscape">Horizontal</option>
										<option value="v" selected data-icon="mdi mdi-crop-portrait">Vertical</option>
									</select>
								</div>
							</div>
						</div>

						<div class="col-12">
							<button id="btn_submit" class="btn btn-primary btn-lg"><i class="mdi mdi-file-document"></i> Obtener informe</button>
						</div>
					</form>
				</div>
					@endif
					@include('resources.programacion_informe')
					<br>
			        <div class="table-responsive m-t-40 overflow-hidden">

						@include('resources.informes_imprimir_resumen')
			            <table class="table table-bordered table-condensed table-hover tablesaw" style="font-size: 12px">
			            	<tbody id="myFilter" >
			            	</tbody>
			            </table>
			        </div>
			    </div>
			</div>
        </div>
    </div>
</div>
@php
    $nombre_empresa = trans('strings._reports.report_accesos') . " ";
    $___cl = \DB::table('cug_clientes')->where('cod_cliente',Auth::user()->cod_cliente)->first();
    if(isset($___cl) && ($___cl->nom_cliente))
        $nombre_empresa .= $___cl->nom_cliente;
    else $nombre_empresa .= "SPOTDESKING";
@endphp
@endsection

@section('scripts2')
<script>
	document.title = '{{$nombre_empresa}}';

	function iformat(icon) {
		var originalOption = icon.element;
		return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
	}
	$('.icons_select2').select2({
		width: "100%",
		templateSelection: iformat,
		templateResult: iformat,
		allowHtml: true,
		minimumResultsForSearch: -1,
	});

	$('#document').change(function(){
        if($(this).val()=="pdf"){
            $('#orientation').show();
        } else $('#orientation').hide();
    })

</script>
@endsection
