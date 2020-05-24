@extends('layout')


@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de clientes</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">clientes</li>
        <li class="breadcrumb-item">listado</li>
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
				<h3 class="panel-title">Clientes</h3>
			</div>
			<div class="panel-body">
				<table id="myTable" class="table table-bordered table-condensed table-hover dataTable">
					<thead>
						<tr>
							<th>Id</th>
							<th></th>
							<th>Nombre</th>
							<th>Puestos</th>
							<th>Edificios</th>
							<th>Plantas</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($clientes as $cus)
							<tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
								<td>{{$cus->id_cliente}}</td>
								<td class="text-center no-sort">
									@isset($cus->img_logo)
										<img src="{{url('/img/clientes/images/',$cus->img_logo)}}" width="40px" alt="">
									@endif
								</td>
								<td>{{$cus->nom_cliente}}</td>
								<td>{{ $cus->puestos}}</td>
								<td>{{ $cus->edificios}}</td>
								<td>{{ $cus->plantas}}</td>
								<td style="position: relative;" class="pt-2">
									<div class="floating-like-gmail">
										@if (checkPermissions(['Clientes'],["C"]))<a href="#" title="Editar cliente" data-id="{{ $cus->id_cliente }}" class="btn btn-xs btn-success add-tooltip btn_edit "><span class="fa fa-pencil pt-1" aria-hidden="true"></span></a>@endif
										@if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-usuario-{{$cus->id_cliente}}" title="Borrar cliente" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip "><span class="fa fa-trash pt-1" aria-hidden="true"></span></a>@endif
										{{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$cus->id_cliente}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
									</div>
									@if (checkPermissions(['Clientes'],["D"]))
										<div class="modal fade" id="eliminar-usuario-{{$cus->id_cliente}}">
											<div class="modal-dialog modal-md">
												<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
													<div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i><b>
														Borrar cliente
													</div>
													<div class="modal-body text-left">
														El cliente tiene:<br>
														<ul>
															<li>{{ $cus->puestos }} Puestos</li>
															<li>{{ $cus->edificios }} Edificios</li>
															<li>{{ $cus->plantas }} Plantas</li>
														</ul>
													</div>
													<div class="modal-footer">
														<a class="btn btn-info" href="{{url('/clientes/delete',$cus->id_cliente)}}">Si</a>
														<button type="button" data-dismiss="modal" class="btn btn-warning">Cancelar</button>
													</div>
												</div>
											</div>
										</div>
										<div class="modal fade" id="eliminar-Cliente-{{$cus->id_cliente}}">
											<div class="modal-dialog modal-md">
												<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
													<div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i>
														<b>Esta opción no se podrá deshacer! Seguro que quiere seguir?</b>
													</div>
													<div class="modal-body text-left">
														El cliente tiene:<br>
														<ul>
															<li>{{ $cus->puestos }} Puestos</li>
															<li>{{ $cus->edificios }} Edificios</li>
															<li>{{ $cus->plantas }} Plantas</li>
														</ul>
													</div>
													<div class="modal-footer">
														<a class="btn btn-info" href="{{url('/clientes/deleteCompleto',$cus->id_cliente)}}">¡Si!</a>
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

	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/clientes/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	  // window.scrollTo(0, 0);
      //stopPropagation()
	});

	$('.btn_edit').click(function(){
		$('#editorCAM').load("{{ url('/clientes/edit/') }}"+"/"+$(this).data('id'), function(){
			animateCSS('#editorCAM','bounceInRight');
		});
	})


    $('.td').click(function(event){
        editar( $(this).data('id'));
	})
	
	$('.dataTable').dataTable({
			"lengthChange": false,
			"pageLength": 50,
			"responsive": true,
			"bSort": true,
			"language": {
				"paginate": {
				"previous": '<i class="demo-psi-arrow-left"></i>',
				"next": '<i class="demo-psi-arrow-right"></i>'
				}
			},
			columnDefs: [ { targets: 'no-sort', orderable: false } ],
		});

	</script>

@endsection