@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Avisos</h1>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('/plugins/html5-editor/bootstrap-wysihtml5.css') }}" />
<link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuracion</li>
		<li class="breadcrumb-item">parametrizacion</li>
		<li class="breadcrumb-item">utilidades</li>
        <li class="breadcrumb-item active">avisos</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion">
    <div class="col-md-4">

    </div>
    <div class="col-md-6">
        <br>
    </div>
    <div class="col-md-2 text-end">
        @if(checkPermissions(['Colectivos'],['C']))
        <div class="btn-group btn-group-sm pull-right" role="group">
			<a href="#" class="btn float-right hidden-sm-down btn-success btn_nuevo">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nuevo</span>
			</a>
        </div>
        @endif
    </div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="card">
    <div class="card-header">

    </div>
    <div class="card-body">
		<div class="table-responsive">
			<table id="tabladeps"  data-toggle="table" data-mobile-responsive="true"
				data-locale="es-ES"
				data-search="true"
				data-show-columns="true"
				data-show-toggle="true"
				data-show-columns-toggle-all="true"
				data-page-list="[5, 10, 20, 30, 40, 50, 75, 100]"
				data-page-size="50"
				data-pagination="true" 
				data-toolbar="#all_toolbar"
				data-buttons-class="secondary"
				data-show-button-text="true"
				>
				<thead>
					<tr>
						<th data-width="5%"  data-sortable="true" >ID</th>
						<th></th>
						<th data-sortable="true"  style="width: 80%" >Titulo</th>
						<th data-sortable="true"  style="width: 80%" >Texto</th>
						<th data-width="250" data-sortable="true"  >Cliente</th>
						<th data-width="1%" data-sortable="true"  >Activo</th>
						<th data-width="1%" data-sortable="true"  >Inicio</th>
						<th data-width="1%" data-sortable="true"  >Fin</th>
						<th data-width="1%" data-sortable="true"  >Perfiles</th>
						<th data-width="1%" data-sortable="true"  >Turnos</th>
						<th data-width="1%" data-sortable="true"  >Edificios</th>
						<th data-width="1%" data-sortable="true"  >Tipo Puesto</th>
						<th data-width="80" data-sortable="true" style="width: 1%" class="text-center" >Plantas</th>
					</tr>
				</thead>
				<tbody>
					@php $idcl=0; @endphp
					@foreach ($avisos as $dato)
						
						<tr class="hover-this">
							<td>{{$dato->id_aviso}}</td>
							<td class="text-center"><i class="{{ $dato->val_icono }} fa-2x" style="color:{{ $dato->val_color }}"></i></td>
							<td>{{ $dato->val_titulo }}</td>
							<td>{!!  $dato->txt_aviso !!}</td>
							<td>{{ $dato->nom_cliente }}</td>
							<td class="text-center">
								<div class="form-check fs-4 ml-4">
									<input  class="form-check-input ml-4" type="checkbox" {{$dato->mca_activo == "S" ? 'checked' : ''}} >
								</div>
							</td>
							<td>{!! beauty_fecha($dato->fec_inicio) !!}</td>
							<td>{!! beauty_fecha($dato->fec_fin) !!}</td>
							<td>
								@foreach(DB::table('niveles_acceso')->wherein('cod_nivel',explode(',',$dato->val_perfiles))->get() as $nivel)
									<li class="badge bg-info">{{ $nivel->des_nivel_acceso }}</li>
								@endforeach
							</td>
							<td>
								@foreach(DB::table('turnos')->wherein('id_turno',explode(',',$dato->val_turnos))->get() as $turno)
									<li class="badge bg-info">{{ $turno->des_turno }}</li>
								@endforeach
							</td>
							<td>
								@foreach(DB::table('edificios')->wherein('id_edificio',explode(',',$dato->val_edificios))->get() as $edificio)
									<li class="badge  bg-info">{{ $edificio->des_edificio }}</li>
								@endforeach
							</td>
							<td>
								@foreach(DB::table('puestos_tipos')->wherein('id_tipo_puesto',explode(',',$dato->val_tipo_puesto))->where('id_tipo_puesto','>',0)->get() as $tipo)
									<li class="badge  bg-info">{{ $tipo->des_tipo_puesto }}</li>
								@endforeach
							</td>
							<td style="position: relative;" class="text-center">
								@foreach(DB::table('plantas')->wherein('id_planta',explode(',',$dato->val_plantas))->get() as $planta)
									<li class="badge bg-info">{{ $planta->des_planta }}</li>
								@endforeach
								
								<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
									<div class="btn-group btn-group pull-right ml-1" role="group">
										@if (checkPermissions(['Colectivos'],["C"]))<a href="#" onclick="editar({{ $dato->id_aviso }})" class="btn btn-xs btn-info"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a></a>@endif
										@if (checkPermissions(['Colectivos'],["D"]))<a href="#eliminar-colectivo-{{$dato->id_aviso}}" onclick="del({{ $dato->id_aviso }})" data-toggle="modal" class="btn btn-xs btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> Del</a></a>@endif
									</div>
								</div>
								<div class="modal fade" id="eliminar-aviso-{{$dato->id_aviso}}">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
												<h1 class="modal-title text-nowrap">Borrar colectivo </h1>
												<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
													<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
												</button>
											</div>    
											<div class="modal-body">
												¿Borrar colectivo {{ $dato->des_colectivo }}?
											</div>
					
											<div class="modal-footer">
												<a class="btn btn-info" href="{{url('collective/delete',$dato->id_aviso)}}">{{trans('strings.yes')}}</a>
												<button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">{{trans('strings.cancel')}}</button>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
    </div>
</div>


@endsection


@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
		$('.menu_utilidades').addClass('active active-sub');
        $('.avisos').addClass('active');

		function editar(id){
			$('#editorCAM').load("{{ url('/avisos/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
		}

		function del(id){
			$('#eliminar-aviso-'+id).modal('show');
		}

		$('.btn_nuevo').click(function(){
            $('#editorCAM').load("{{ url('/avisos/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });

        });
    </script>
@endsection


