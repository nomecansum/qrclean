@extends('layout')
@section('title')
<h1 class="page-header text-overflow pad-no">Pefiles</h1>
@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li><a href="{{url('/')}}"><i class="demo-pli-home"></i> </a></li>
    <li class="">Configuracion</li>
	<li class="breadcrumb-item">Permisos</li>
    <li class="active">perfiles</li>
</ol>

@endsection
@section('content')
<div class="row botones_accion mb-2">
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

<div class="container-fluid">

	<div class="row">
		<div class="col-md-12">
			<div class="panel box-solid" id="editor" style="display:none">
				
			</div>
		</div>
	</div>
    
	<div class="row">
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Perfiles de acceso</h3>
				</div>
			    <div class="panel-body collapse show">
			        {{-- <h2 class="panel-title float-left">{{trans('strings.profiles')}}</h2> --}}
			        {{-- @include('resources.combo_clientes') --}}
			        <div class="table-responsive mt-40">
			            <table id="myTable" class="table table-bordered  table-hover  table-striped">
			                <thead>
			                    <tr>
									<th>ID  </th>
									<th style="width: 2%">Nivel</th>
			                        <th style="width: 30%" class="text-center">Pefil</th>
									@if(isAdmin())<th style="width: 80px" class="text-center">Fijo</th>@endif
									<th style="width: 80px" class="text-center">Multiple</th>
									<th style="width: 80px" class="text-center">Sabados</th>
									<th style="width: 80px" class="text-center">Domingos</th>
									<th style="width: 80px" class="text-center">Festivos</th>
									<th style="width: 80px" class="text-center"><i class="fa-solid fa-user-group"></i> Usuarios</th>
									<th></th>
			                    </tr>
			                </thead>
			                <tbody>
			                	@foreach ($niveles as $nivel)
			                		<tr class="hover-this pad-all" data-perfil="{{$nivel->cod_nivel}}" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}">
										<td style="width:4%">{{$nivel->cod_nivel}}</td>
										
										<td>{{$nivel->val_nivel_acceso}}</td>
										<td>{{$nivel->des_nivel_acceso}}</td>
										@if(isAdmin())
										<td class="text-center">
											<input type="checkbox" class="form-control  magic-checkbox" readonly  value="S" {{ $nivel->mca_fijo=='S'?'checked':'' }}> 
											<label class="custom-control-label" readonly  for="mca_fijo{{ $nivel->cod_nivel }}"></label>
										</td>
										@endif
										<td class="text-center">
											<input type="checkbox" class="form-control  magic-checkbox" readonly  value="S" {{ $nivel->mca_reserva_multiple=='S'?'checked':'' }}> 
											<label class="custom-control-label" readonly  for="mca_reserva_multiple{{ $nivel->cod_nivel }}"></label>
										</td>
										<td class="text-center">
											<input type="checkbox" class="form-control  magic-checkbox" readonly  value="S" {{ $nivel->mca_reservar_sabados=='S'?'checked':'' }}> 
											<label class="custom-control-label" readonly  for="mca_reservar_sabados{{ $nivel->cod_nivel }}"></label>
										</td>
										<td class="text-center">
											<input type="checkbox" class="form-control  magic-checkbox" readonly  value="S" {{ $nivel->mca_reservar_domingos=='S'?'checked':'' }}> 
											<label class="custom-control-label" readonly  for="mca_reservar_domingos{{ $nivel->cod_nivel }}"></label>
										</td>
										<td class="text-center">
											<input type="checkbox" class="form-control  magic-checkbox" readonly  value="S" {{ $nivel->mca_reservar_festivos=='S'?'checked':'' }}> 
											<label class="custom-control-label" readonly  for="mca_reservar_festivos{{ $nivel->cod_nivel }}"></label>
										</td>
										<td  class="text-center">
											{{ $cuenta->where('cod_nivel',$nivel->cod_nivel)->count() }}
										</td>
			                			<td style="position: relative;">
											
                                            @if(($nivel->mca_fijo=='S' && isAdmin()) || $nivel->id_cliente==Auth::user()->id_cliente)
											<div class="floating-like-gmail pull-right pt-3" role="group">
                                                <a href="#" class="btn btn-info btn-xs btn_editar pt-2  add-tooltip" title="Editar perfil"  data-perfil="{{$nivel->cod_nivel}}" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>
                                                <a href="#eliminar-usuario-{{$nivel->cod_nivel}}" data-toggle="modal" data-perfil="{{$nivel->cod_nivel}}" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}" class="btn btn-danger  btn-xs add-tooltip" title="Borrar perfil" ><span class="fa fa-trash" aria-hidden="true"></span> Del</a>
                                            </div>
											@endif
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
	$('.configuracion').addClass('active active-sub');
	$('.menu_permisos').addClass('active active-sub');
	$('.perfiles').addClass('active-link');
	
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
		$('#editor').load("{{ url('profiles/edit') }}/0", function(data){
			$('.box-title').html("Crear perfil");
			$('#formperfil').attr("action","{{url('profiles/save')}}")
		});
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
		$('#editor').load("{{ url('profiles/edit') }}/"+$(this).data('perfil'), function(data){
			$('.box-title').html("Editar perfil");
			$('#formperfil').attr("action","{{url('profiles/update')}}")
		});
	});
	</script>
@endsection
