@extends('layout')
@section('title')
<h1 class="page-header text-overflow pad-no">Pefiles</h1>
@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li><a href="{{url('/')}}"><i class="demo-pli-home"></i> </a></li>
    <li class="">Configuracion</li>
    <li class="active">Perfiles</li>
</ol>

@endsection
@section('content')
<div class="container-fluid">

    <div class="row">
    	<div class="col-md-12">
			<div class="panel box-solid" id="editor" style="display:none">
				<div class="box-header with-border">
					<h3 class="box-title">@isset ($s)Editar perfil @else Crear perfil @endisset</h3>

				</div>
				<div class="panel-body">
					<form action="{{url('profiles/update')}}" method="POST" class="form-ajax" id="formperfil">
						<input type="hidden" name="id" id="id" value="{{ isset($n) ? $n->cod_nivel : 0}}">

						{{csrf_field()}}

						<div class="form-group col-md-6">
							<label for="">Descripcion</label>
							<input type="text" name="des_nivel_acceso" id="des_nivel_acceso" class="form-control" required value="{{isset($n) ? $n->des_nivel_acceso : ''}}">
						</div>

						<div class="form-group col-md-2">
							<label for="">Nivel</label>
							<input type="number" name="num_nivel_acceso" id="num_nivel_acceso" min="0" max="{{ isset($nivel_acceso) ? $nivel_acceso : '200' }}" class="form-control" required value="{{isset($n) ? $n->val_nivel_acceso : ''}}">
						</div>

						<div class="form-group col-md-3">
							<label>Hereda de</label>
							<select  name="hereda_de" class="form-control" id="nn">
								<option value="" selected=""></option>
								@foreach ($niveles as $n)
									<option value="{{$n->cod_nivel}}">{{$n->des_nivel_acceso}}</option>
								@endforeach
							</select>
						</div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary float-right    " style="margin-top: 25px">Guardar</button>
                        </div>

                    </form>

                </div>
                <div class="row alert alert-warning not-dismissable" id="warning_level" style="display: none">
                    <h3 class="text-warning col-md-12"><i class="fa fa-exclamation-triangle"></i> Atencion!!</h3> Si selecciona la opcion de heredar de, se borrarán todos los permisos que tuviera este perfil.
                </div>

			</div>


		</div>
	</div>
	<div class="row">

			<div class="panel">
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-7">
                        <br>
                    </div>
                    <div class="col-md-1 text-right">
                        <div class="btn-group btn-group-sm pull-right mt-2" role="group" style="margin-right: 20px;">
                                <a href="#" id="btn_nueva_seccion" class="btn btn-success" title="Nuevo perfil">
                                <span class="fa fa-plus-square pt-1" style="font-size: 20px" aria-hidden="true"></span> Nuevo
                            </a>
                        </div>
                    </div>
                </div>
			    <div class="panel-body collapse show">
			        {{-- <h2 class="panel-title float-left">{{trans('strings.profiles')}}</h2> --}}
			        {{-- @include('resources.combo_clientes') --}}
			        <div class="table-responsive mt-40">
			            <table id="myTable" class="table table-bordered table-condensed table-hover  table-striped table-bordered">
			                <thead>
			                    <tr>
									<th>ID  </th>
									<th style="width: 2%">Nivel</th>
			                        <th>Pefil</th>
			                    </tr>
			                </thead>
			                <tbody>
			                	@foreach ($niveles as $nivel)
			                		<tr class="hover-this" data-perfil="{{$nivel->cod_nivel}}" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}">
										<td style="width:4%">{{$nivel->cod_nivel}}</td>
										<td>{{$nivel->val_nivel_acceso}}</td>
			                			<td style="position: relative;">{{$nivel->des_nivel_acceso}}
                                            <div class="btn-group btn-group-xs pull-right floating-like-gmail pt-3" role="group">
                                                <a href="#" class="btn btn-info btn_editar pt-2  add-tooltip" title="Editar perfil"  data-perfil="{{$nivel->cod_nivel}}" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}"><span class="fa fa-pencil pt-1" aria-hidden="true"></span></a>
                                                <a href="#eliminar-usuario-{{$nivel->cod_nivel}}" data-toggle="modal" data-perfil="{{$nivel->cod_nivel}}" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}" class="btn btn-danger add-tooltip" title="Borrar perfil" ><span class="fa fa-trash" aria-hidden="true"></span></a>
                                            </div>
                                            <div class="modal fade" id="eliminar-usuario-{{$nivel->cod_nivel}}">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content"><div><img src="/img/logo_enaire_20.png" class="float-right"></div>
                                                        <div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i>
                                                           <h4>¿Borrar perfil {{$nivel->des_nivel_acceso}}?</h4>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a class="btn btn-info" href="{{url('profiles/delete',$nivel->cod_nivel)}}">{{trans('strings.yes')}}</a>
                                                            <button type="button" data-dismiss="modal" class="btn btn-warning">{{trans('strings.cancel')}}</button>
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

    </div>
</div>
@endsection

@section('scripts')
	<script>
		$(function(){
			$('#nn').change(function(event){
				if($('#nn').val()!=''){
					$('#warning_level').show();
				} else {
					$('#warning_level').hide();
				}
			});
		})

	$('#btn_nueva_seccion').click(function(){
		$('#editor').show();
		animateCSS('#editor','bounceInRight');
		$('.box-title').html("Crear perfil");
		$('#id').val(0);
		$('#des_nivel_acceso').val("");
		$('#num_nivel_acceso').val("");
		$('#formperfil').attr("action","{{url('profiles/save')}}")
	})

	$('.btn_editar').click(function(){
		$('#editor').show();
		animateCSS('#editor','bounceInRight');
		$('.box-title').html("Editar perfil");
		$('#id').val($(this).data('perfil'));
		console.log($(this).data('nombre'));
		$('#des_nivel_acceso').val($(this).data('nombre'));
		$('#num_nivel_acceso').val($(this).data('num'));
		$('#formperfil').attr("action","{{url('profiles/update')}}")
	});
	</script>
@endsection
