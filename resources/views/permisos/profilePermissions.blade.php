@extends('layout')
@section('styles')
<style>

	.chk_verde .custom-control-input:checked~.custom-control-label::before{
	background-color:#28a745;
	}
</style>
@endsection
@section('title')
<h1 class="page-header text-overflow pad-no">Permisos</h1>
@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li><a href="{{url('/')}}"><i class="demo-pli-home"></i> </a></li>
    <li class="">Configuracion</li>
    <li class="active">Permisos</li>
</ol>

@endsection

@section('content')
@php
	function checkChecked($n,$s,$t,$permisos)
	{
		$type = "";
		if ($t == "R") {$type = "mca_read";}
		if ($t == "W") {$type = "mca_write";}
		if ($t == "C") {$type = "mca_create";}
		if ($t == "D") {$type = "mca_delete";}
		foreach ($permisos as $permiso)
		{
			//Debugbar::info($permiso);
			if ($n==$permiso->id_perfil && $s==$permiso->id_seccion){
				return $permiso->$type;
				break;
			}
		}

	}
	$primero=true;

@endphp
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
			<div class="panel">
			    <div class="panel-body">
			        {{-- <h2 class="panel-title">Asignacion de permisos</h2> --}}
			        {{-- @include('resources.combo_clientes') --}}

						<!--ul class="nav nav-tabs tabs-vertical" role="tablist"-->
						<ul class="nav nav-tabs customtab" role="tablist">
							@php $primero=true;	@endphp
    							@foreach($grupos as $grupo)
    							<li class="nav-item"> <a class="nav-link  {{ ($primero===true) ? "active" :"" }}" data-toggle="tab" href="#{{ $grupo->des_grupo }}" role="tab"><span><i class="{{ $grupo->icono }}"></i></span>&nbsp;&nbsp;{{ $grupo->des_grupo }}</a> </li>
    							@php $primero=false;	@endphp
							@endforeach
						</ul>
						<!-- Tab panes -->
						<div class="tabbable">
    						<div class="tab-content">
    							@php $primero=true;	@endphp
    							@foreach($grupos as $grupo)
    							<div class="tab-pane {{ ($primero===true) ? "active" :"" }}" id="{{ $grupo->des_grupo }}" role="tabpanel">
    									@php $primero=false;	@endphp
    									<!--h3>{{ $grupo->des_grupo }}</h3-->
    									<table class="table table-responsive table-hover   table-striped table-bordered" style="font-size:12px">
    										<thead>
    											<tr>
    												<td></td>
    												@foreach ($niveles as $n)
    													<td class="text-center celda_{{ $n->cod_nivel }}" style="font-size: 14px; font-weight: bold">
    													    {{$n->des_nivel_acceso}}
    													    @if (isAdmin()  && !session('cod_cliente')) ({{$n->nom_cliente}}) @endif
    													</td>
    												@endforeach
    											</tr>
    										</thead>
    										<tbody>
    											@foreach ($secciones as $sec)
    												@if($sec->des_grupo==$grupo->des_grupo)
    													<tr>
    														<td style="font-size: 14px">{{$sec->des_seccion}}</td>
    														@foreach ($niveles as $n)
    															<td style="vertical-align: middle" class="celda_{{ $n->cod_nivel }}" nowrap>
																	<div title="{{trans('strings._permissions.R')}}" class="custom-control custom-checkbox" style="display: inline-block; @if($sec->val_tipo=='Permiso') margin-left: 62px; @endif margin-right: 6px">
    																	<input @if(checkPermissions(['Permisos'],["W"])) readonly @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'R',$permisos) ? 'checked' : ''}} type="checkbox" data-type="R" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission custom-control-input magic-checkbox @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="read{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
    																	<label class="custom-control-label" for="read{{$sec->cod_seccion}}-{{$n->cod_nivel}}" style="padding-top: 4px">@if($sec->val_tipo=='Seccion')R @endif</label>
    																</div>
    																@if($sec->val_tipo=='Seccion')
    																<div title="{{trans('strings._permissions.W')}}" class="custom-control custom-checkbox" style="display: inline-block; margin-right: 6px">
    																	<input @if(checkPermissions(['Permisos'],["W"])) readonly @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'W',$permisos) ? 'checked' : ''}} type="checkbox" data-type="W" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission custom-control-input magic-checkbox  @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="write{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
    																	<label class="custom-control-label" for="write{{$sec->cod_seccion}}-{{$n->cod_nivel}}" style="padding-top: 4px">W</label>
    																</div>

    																<div title="{{trans('strings._permissions.C')}}" class="custom-control custom-checkbox " style="display: inline-block; margin-right: 6px">
    																	<input @if(checkPermissions(['Permisos'],["W"])) readonly @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'C',$permisos) ? 'checked' : ''}} type="checkbox" data-type="C" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission custom-control-input magic-checkbox @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="create{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
    																	<label class="custom-control-label" for="create{{$sec->cod_seccion}}-{{$n->cod_nivel}}" style="padding-top: 4px">C</label>
    																</div>

    																<div title="{{trans('strings._permissions.D')}}" class="custom-control custom-checkbox" style="display: inline-block; margin-right: 6px">
    																	<input @if(checkPermissions(['Permisos'],["W"])) readonly @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'D',$permisos) ? 'checked' : ''}} type="checkbox" data-type="D" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission custom-control-input magic-checkbox @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="delete{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
    																	<label class="custom-control-label" for="delete{{$sec->cod_seccion}}-{{$n->cod_nivel}}" style="padding-top: 4px">D</label>
    																</div>
    																@endif
    															</td>
    														@endforeach
    													</tr>
    												@endif
    											@endforeach
    										</tbody>
    									</table>
    							</div>
    							@endforeach
    						</div>
    				    </div>
					<div class="row">
						<div class="col-md-12">
							<label class="text-info">Perfiles</label>
							<select class="select2 col-12" style="width: 100%; " multiple="" tabindex="-1" aria-hidden="true" name="niveles" id="niveles">
								<option value=""></option>
								@foreach($niveles as $nivel)
								<option selected value="{{ $nivel->cod_nivel }}">
								    {{ $nivel->des_nivel_acceso }}
								    @if (isAdmin() && !session('cod_cliente')) ({{$nivel->nom_cliente}}) @endif
								</option>
								@endforeach
							</select>
						</div>
					</div>
			    </div>
			</div>
        </div>
    </div>
</div>

@endsection
@section('scripts')

<script>
	$('.check-permission').on('change', function(event){
		$(this).data('_token','{{csrf_token()}}');
		if ($(this).is(':checked')) {
			console.log($(this).data());
			$.post('{{url('addPermissions')}}', $(this).data(), function(data, textStatus, xhr) {
			});
		}else{
			$.post('{{url('removePermissions')}}', $(this).data(), function(data, textStatus, xhr) {
			});
		}
	});

	$('#niveles').on('select2:select', function (e) {
		var data = e.params.data;
		$(".celda_"+data.id).fadeToggle();
		console.log(data.id);
	});
	$('#niveles').on('select2:unselect', function (e) {
		var data = e.params.data;
		$(".celda_"+data.id).fadeToggle();
		console.log(data.id);
	});

	// $('.flat-green').iCheck({
    //   checkboxClass: 'icheckbox_flat-green',
    //   radioClass   : 'iradio_flat-green'
	// })

	// $('.flat-blue').iCheck({
    //   checkboxClass: 'icheckbox_flat-blue',
    //   radioClass   : 'iradio_flat-blue'
    // })
</script>

@endsection
