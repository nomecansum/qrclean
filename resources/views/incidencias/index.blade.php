@extends('layout')




@section('styles')

@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de incidencias</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
	<li class="breadcrumb-item">mantenimiento</li>
	<li class="breadcrumb-item">incidencias</li>
	{{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
</ol>
@endsection

@section('content')
@php
    //dd($incidencias);
@endphp

<div class="row botones_accion">
	<div class="col-md-4">

	</div>
	<div class="col-md-7">
		<br>
	</div>
	<div class="col-md-1 text-right">
		<div class="btn-group btn-group-sm pull-right" role="group">
				<a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva cliente">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nuevo</span>
			</a>
		</div>
	</div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="row mt-2">
	<div class="col-12">
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Incidencias abiertas</h3>
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
                            <th>Tipo</th>
							<th>Incidencia</th>
							<th>Puesto</th>
							<th>Edificio</th>
                            <th>Planta</th>
                            <th>Fecha</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($incidencias as $inc)
							<tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
								<td>{{$inc->id_incidencia}}</td>
								<td>{{$inc->des_tipo_incidencia}}</td>
								<td>{{ $inc->des_incidencia}}</td>
								<td>{{ $inc->des_puesto}}</td>
                                <td>{{ $inc->des_edificio}}</td>
                                <td>{{ $inc->des_planta}}</td>
                                <td>{!! beauty_fecha($inc->fec_apertura)!!}</td>
								<td style="position: relative; width: 300px" class="pt-2" nowrap="nowrap">
									<div class="floating-like-gmail mt-2">
										@if (checkPermissions(['Incidencias'],["W"]))<a href="#" title="Ver incidencia " data-id="{{ $inc->id_incidencia }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $inc->id_incidencia }})"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                        @if (checkPermissions(['Incidencias'],["W"]))<a href="#cerrar-incidencia-{{$inc->id_incidencia}}" title="Borrar incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip "><span class="fad fa-thumbs-up pt-1" aria-hidden="true"></span> OK</a>@endif
                                        @if (checkPermissions(['Incidencias'],["D"]))<a href="#eliminar-incidencia-{{$inc->id_incidencia}}" title="Borrar incidencia" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip "><span class="fa fa-trash pt-1" aria-hidden="true"></span> Del</a>@endif
										{{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$inc->id_incidencia}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
									</div>
									@if (checkPermissions(['Incidencias'],["D"]))
										<div class="modal fade" id="eliminar-incidencia-{{$inc->id_incidencia}}">
											<div class="modal-dialog modal-md">
												<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
													<div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i><b>
														¿Borrar incidencia {{ $inc->des_incidencia}}?
													</div>
													<div class="modal-footer">
														<a class="btn btn-info" href="{{url('/incidencias/delete',$inc->id_incidencia)}}">Si</a>
														<button type="button" data-dismiss="modal" class="btn btn-warning">Cancelar</button>
													</div>
												</div>
											</div>
										</div>
                                    @endif
                                    @if (checkPermissions(['Incidencias'],["W"]))
										<div class="modal fade" id="cerrar-incidencia-{{$inc->id_incidencia}}">
											<div class="modal-dialog modal-md">
												<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
													<div class="modal-header"><i class="mdi mdi-thumb-up text-success mdi-48px"></i><b>
														Cerrar incidencia {{ $inc->des_incidencia}}
													</div>
													<div class="modal-footer">
														<a class="btn btn-info" href="{{url('/incidencias/delete',$inc->id_incidencia)}}">Si</a>
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
	$('.clientes').addClass('active-link');

	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/clientes/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	  // window.scrollTo(0, 0);
      //stopPropagation()
	});

	function edit(id){
		$('#editorCAM').load("{{ url('/clientes/edit/') }}"+"/"+id, function(){
			animateCSS('#editorCAM','bounceInRight');
		});
	}


    $('.td').click(function(event){
        editar( $(this).data('id'));
	})

	</script>

@endsection