@extends('layout')




@section('styles')
	<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de turnos</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
	<li class="breadcrumb-item">parametrizacion</li>
	<li class="breadcrumb-item">turnos</li>
	{{-- <li class="breadcrumb-item">listado</li> --}}
	{{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
</ol>
@endsection

@section('content')
<div class="row botones_accion">
	<div class="col-md-4">

	</div>
	<div class="col-md-7">
		<br>
	</div>
	<div class="col-md-1 text-right">
		@if(checkPermissions(['Clientes'],['C']))
		<div class="btn-group btn-group-sm pull-right" role="group">
				<a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo turno">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nuevo</span>
			</a>
		</div>
		@endif
	</div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="row mt-2">
	<div class="col-12">
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Turnos</h3>
			</div>
			<div class="panel-body">
				<table id="tabla"  data-toggle="table"
                    data-locale="es-ES"
                    data-search="true"
                    data-show-columns="true"
                    data-show-columns-toggle-all="true"
                    data-page-list="[5, 10, 20, 30, 40, 50]"
                    data-page-size="50"
                    data-pagination="true" 
                    data-show-pagination-switch="true"
                    data-show-button-icons="true"
                    data-toolbar="#all_toolbar"
                    >
					<thead>
						<tr>
							<th>Id</th>
							<th>Cliente</th>
							<th>Nombre</th>
							<th>F.inicio</th>
							<th>F.fin</th>
                            <th>Color</th>
							<th>Dias</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($turnos as $dato)
							<tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
								<td>{{$dato->id_turno}}</td>
								<td>{{$dato->nom_cliente}}</td>
								<td>{{ $dato->des_turno}}</td>
								<td>{!! beauty_fecha($dato->fec_inicio,"2")!!}</td>
                                <td>{!! beauty_fecha($dato->fec_fin,"2")!!}</td>
                                <td style="background-color: {{ $dato->val_color }}"></td>
								<td style="position: relative;" class="pt-2">
									@php
										$dias=['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'];
										foreach(json_decode($dato->dias_semana)->dia as $dia){
											echo "<li>".$dias[$dia-1].'</li>';
										}
									@endphp
									<div class="floating-like-gmail">
										@if (checkPermissions(['Turnos'],["W"]))<a href="#" title="Editar turno" data-id="{{ $dato->id_turno }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $dato->id_turno }})"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
										@if (checkPermissions(['Turnos'],["D"]))<a href="#eliminar-{{$dato->id_turno}}" title="Borrar turno" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip "><span class="fa fa-trash pt-1" aria-hidden="true"></span> Del</a>@endif
										{{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$cus->id_cliente}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
									</div>
									@if (checkPermissions(['Turnos'],["D"]))
										<div class="modal fade" id="eliminar-{{$dato->id_turno}}">
											<div class="modal-dialog modal-md">
												<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
													<div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i><b>
														Borrar turno
													</div>
													
													<div class="modal-footer">
														<a class="btn btn-info" href="{{url('/turnos/delete',$dato->id_turno)}}">Si</a>
														<button type="button" data-dismiss="modal" class="btn btn-warning">Cancelar</button>
													</div>
												</div>
											</div>
										</div>
										
										
									@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
	<script>

	$('.configuracion').addClass('active active-sub');
    $('.menu_parametrizacion').addClass('active active-sub');
	$('.turnos').addClass('active-link');




	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/turnos/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	  // window.scrollTo(0, 0);
      //stopPropagation()
	});

	function edit(id){
		$('#editorCAM').load("{{ url('/turnos/edit/') }}"+"/"+id, function(){
			animateCSS('#editorCAM','bounceInRight');
		});
	}


    $('.td').click(function(event){
        editar( $(this).data('id'));
	})

	</script>

@endsection