@extends('layout')
@section('title')
<h1 class="page-header text-overflow pad-no">Secciones</h1>
@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li><a href="{{url('/')}}"><i class="demo-pli-home"></i> </a></li>
    <li class="">Configuracion</li>
    <li class="active">Secciones</li>
</ol>

@endsection
@section('content')
<div class="container-fluid">


    <div class="row">
			<div class="panel panel-default col-md-12" id="editor" style="display:none">
				<div class="panel-header with-border">
					<h3 class="panel-title">@isset ($s)Editar seccion @else Crear seccion @endisset</h3>
				</div>
				<div class="panel-body">
						<form action="{{url('sections/save')}}" method="POST" class="form-ajax"  id="formseccion">
							<input type="hidden" name="id" value="0" id="id">
						{{csrf_field()}}
						<div class="row">
							<div class="form-group col-md-4">
								<label for="">Nombre</label>
								<input type="text" name="des_seccion" id="des_seccion" class="form-control" required value="{{isset($s) ? $s->des_seccion : ''}}">
							</div>
							<div class="form-grou col-md-2">
								<label for="">Tipo</label>
								<select class="form-control" id="val_tipo" data-placeholder="Seleccione tipo" style="width: 100%; color #000;" tabindex="-1" aria-hidden="true" name="val_tipo">
									@foreach($tipos as $nivel)
									<option {{ isset($s->val_tipo) && $nivel==$s->val_tipo ? 'selected' : '' }}  value="{{ $nivel }}">{{ $nivel }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group col-md-4">
								<label for="">Grupo</label>
								<select  name="des_grupo" class="form-control" id="des_grupo" data-placeholder="Seleccione grupo" style="width: 100%; color #000;" tabindex="-1" aria-hidden="true">
									@foreach($grupos as $nivel)
									<option {{ isset($s->des_grupo) && $nivel->des_grupo==$s->des_grupo ? 'selected' : '' }} data-icon="{{ $nivel->icono }}"  value="{{ $nivel->des_grupo }}">{{ $nivel->des_grupo }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2 pt-2">
								<button type="submit" class="btn btn-primary" style="margin-top: 23px">Guardar</button>
							</div>
						</div>
					</form>
				</div>
			</div>

	</div>
	<div class="row">
		<div class="formfestivo">
			<div class="panel">
			    <div class="panel-body">
			        <div class="row">
                        <div class="col-md-4">

                        </div>
                        <div class="col-md-7">
                            <br>
                        </div>
                        <div class="col-md-1 text-right">
                            <div class="btn-group btn-group-sm pull-right" role="group">
                                    <a href="#" id="btn_nueva_seccion" class="btn btn-success" title="Nueva seccion">
                                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                                    <span>Nueva</span>
                                </a>
                            </div>
                        </div>
                    </div>
			        <div class="table-responsive m-t-40">
			            <table id="myTable" class="table table-bordered nowrap table-hover table-striped table-bordered">
			                <thead>
			                    <tr>
			                        <th>ID</th>
									<th class="sorting">Seccion</th>
									<th class="sorting">Tipo</th>
									<th class="sorting">Grupo</th>
			                        {{-- <th></th> --}}
			                    </tr>
			                </thead>
			                <tbody>
			                	@foreach ($secciones as $secc)
			                		<tr class="hover-this" data-href="{{url('sections/edit',$secc->cod_seccion)}}">
										<td>{{$secc->cod_seccion}}</td>
										<td>{{$secc->des_seccion}}</td>
										<td class="text-{{ $secc->val_tipo=='Seccion' ? 'info' : 'success' }}">{{ $secc->val_tipo}}</td>
			                			<td style="position: relative;"><i class="{{ $secc->icono }}" style="font-size: 24px"></i> {{$secc->des_grupo}}
			                				<div class="btn-group btn-group-xs pull-right floating-like-gmail" role="group">
			                					<a href="#"  class="btn btn-info btn_editar add-tooltip" title="Editar seccion" data-seccion="{{ $secc->cod_seccion }}" data-nombre="{{$secc->des_seccion}}" data-tipo="{{ $secc->val_tipo}}" data-grupo="{{$secc->des_grupo}}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span></a>
			                					<a href="#eliminar-usuario-{{$secc->cod_seccion}}" data-target="#eliminar-usuario-{{$secc->cod_seccion}}" title="Borrar seccion" data-toggle="modal" class="btn btn-danger add-tooltip"><span class="fa fa-trash" aria-hidden="true"></span></a>
			                				</div>
			                				<div class="modal fade" id="eliminar-usuario-{{$secc->cod_seccion}}" style="display: none;">
			                					<div class="modal-dialog">
			                						<div class="modal-content">
													<div class="modal-header">

															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															  <span aria-hidden="true">×</span></button>
															  <div><img src="/img/logo_enaire_20.png" class="float-right"></div>
															<h4 class="modal-title">¿Borrar seccion {{$secc->des_seccion}}?</h4>
														  </div>
			                							<div class="modal-footer">
			                								<a class="btn btn-info" href="{{url('sections/delete',$secc->cod_seccion)}}">{{trans('strings.yes')}}</a>
			                								<button type="button" data-dismiss="modal" class="btn btn-warning">{{trans('strings.cancel')}}</button>
			                							</div>
			                						</div>
			                					</div>
											</div>

			                			</td>
			                			{{-- <td>

			                			</td> --}}
			                		</tr>
			                	@endforeach
			                </tbody>
			            </table>
			        </div>
			    </div>
			</div>
        </div>
	</div>
</div>
<div class="modal fade" id="modal-default" style="display: none;">
		<div class="modal-dialog">
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">×</span></button>
			  <h4 class="modal-title">Default Modal</h4>
			</div>
			<div class="modal-body">
			  <p>One fine body…</p>
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			  <button type="button" class="btn btn-primary">Save changes</button>
			</div>
		  </div>
		  <!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	  </div>
@stop
@section('scripts')
<script>
	function iformat(icon) {
		var originalOption = icon.element;
		return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
	}
	$('.icons_select2').select2({
		width: "100%",
		templateSelection: iformat,
		templateResult: iformat,
		allowHtml: true
	});

	$('#btn_nueva_seccion').click(function(){
		$('#editor').show();
		animateCSS('#editor','bounceInRight');
		$('#id').val(0);
		$('#des_seccion').val("");
		$('#val_tipo').val("");
		$('#des_grupo').val("");
		$('#formseccion').attr("action","{{url('sections/save')}}")
	})

	$('.btn_editar').click(function(){
		$('#editor').show();
		animateCSS('#editor','bounceInRight');
		//console.log($(this).data('seccion'));
		$('#id').val($(this).data('seccion'));
		$('#des_seccion').val($(this).data('nombre'));
		$('#val_tipo').val($(this).data('tipo'));
		$('#des_grupo').val($(this).data('grupo'));
		$('#des_grupo').select2('data', { a_key: $(this).data('grupo')});
		$('#formseccion').attr("action","{{url('sections/update')}}")
		$("#des_grupo").select2("val", $(this).data('grupo'));
	});
</script>
@endsection
