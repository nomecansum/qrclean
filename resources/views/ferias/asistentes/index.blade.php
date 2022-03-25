@extends('layout')




@section('styles')
<link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de asistentes a feria</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
	<li class="breadcrumb-item">ferias</li>
	<li class="breadcrumb-item">asistentes</li>
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
		@if(checkPermissions(['Incidencias'],['C']))
		<div class="btn-group btn-group-sm pull-right" role="group">
				<a href="javascript:void(0)" id="btn_nueva" class="btn btn-success" data-toggle="modal" title="Nueva marca">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nueva</span>
			</a>
		</div>
		@endif
	</div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<form id="frm" name="frm" action="{{ url('/ferias/marcas/print_qr') }}" method="post">
	@csrf

<div class="row mt-2">
	<div class="col-12">
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Marcas</h3>
			</div>
			
			<div class="panel-body">
				<div id="all_toolbar">
					<input type="checkbox" class="form-control custom-control-input magic-checkbox" name="chktodos" id="chktodos"><label  class="custom-control-label"  for="chktodos">Todos</label>
					<div class="btn-group btn-group-xs pull-bottom" role="group">
						<div class="btn-group mr-3">
							<div class="dropdown">
								<button class="btn btn-warning dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" title="Acciones sobre la seleccion de marcas">
									<i class="fad fa-poll-people pt-2" style="font-size: 20px" aria-hidden="true"></i> Acciones <i class="dropdown-caret"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-bottom" style="" id="dropdown-acciones">
									<li><a href="#" class="btn_qr"><i class="fad fa-qrcode"></i> Imprimir QR</a></li>
									<li><a href="#" class="btn_export_qr"><i class="fad fa-file-export"></i></i> Exportar QR</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<table id="tabla"  data-toggle="table"
					data-locale="es-ES"
					data-search="true"
					data-show-columns="true"
					data-show-columns-toggle-all="true"
					data-page-list="[5, 10, 20, 30, 40, 50,100,200]"
					data-page-size="50"
					data-pagination="true" 
					data-show-pagination-switch="true"
					data-show-button-icons="true"
					data-toolbar="#all_toolbar"
					>
					<thead>
						<tr>
							<th></th>
							<th data-sortable="true">Id</th>
							<th data-sortable="true">Nombre</th>
							<th data-sortable="true">e-mail</th>
							<th data-sortable="true">Empresa</th>
							<th data-sortable="true">Fecha</th>
							<th data-sortable="true">Envío</th>
							<th data-sortable="true">Registrado por</th>
							<th data-sortable="true">Observaciones</th>
						</tr>
					</thead>

                    <tbody>
                        @foreach ($datos as $dato)
                            <tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
                                <td class="text-center">
                                    <input type="checkbox" class="form-control chkpuesto magic-checkbox" name="lista_id[]" data-id="{{ $dato->id_contacto }}" id="chkp{{ $dato->id_contacto }}" value="{{ $dato->id_contacto }}">
									<label class="custom-control-label"   for="chkp{{ $dato->id_contacto  }}"></label>
                                </td>
								<td>{{$dato->id_contacto}}</td>
                                <td>{{$dato->nombre}}</td>
								<td>{{$dato->email}}</td>
								<td>{{$dato->empresa}}</td>
								<td>{!!beauty_fecha($dato->fec_audit)!!}</td>
								<td>{{$dato->mca_enviar}}</td>
								<td>{{$dato->name}}</td>
                                <td style="position: relative; vertical-align: middle" class="pt-2">
                                    {{ $dato->mensaje}}
                                    <div class="floating-like-gmail mt-2 w-100" style="width: 100%">
                                        @if (checkPermissions(['Ferias marcas'],["W"]))<a href="#" title="Ver incidencia " data-id="{{ $dato->id_contacto }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $dato->id_contacto }})"><span class="fa fa-eye pt-1" aria-hidden="true"></span> Ver</a>@endif
                                        @if (checkPermissions(['Ferias marcas'],["D"]))<a href="#eliminar-incidencia-{{$dato->id_contacto}}" title="Borrar incidencia" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip "><span class="fa fa-trash pt-1" aria-hidden="true"></span> Del</a>@endif
                                        {{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$inc->id_incidencia}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
                                    </div>
                                    @if (checkPermissions(['Incidencias'],["D"]))
                                        <div class="modal fade" id="eliminar-incidencia-{{$dato->id_contacto}}">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                    <div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i>
                                                        ¿Borrar contacto de {{ $dato->nombre}}?
                                                    </div>
                                                    
                                                    <div class="modal-footer">
                                                        <a class="btn btn-info" href="{{url('/ferias/asistentes/delete',$dato->id_contacto)}}">Si</a>
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
</form>
@endsection

@section('scripts')
	
	
	<script>

	var lista_ficheros=new Array(0);

	$('.ferias').addClass('active active-sub');
	$('.ferias_asistentes').addClass('active-link');

	$('#btn-toggle').click(function(){
         $('#tabla').bootstrapTable('toggleView')
    })

	function edit(id){
		$('#editorCAM').load("{{ url('/ferias/asistentes/edit/') }}"+"/"+id, function(){
			animateCSS('#editorCAM','bounceInRight');
		});
	}

    $('#btn_nueva').click(function(){
        $('#editorCAM').load("{{ url('/ferias/asistentes/edit/0') }}", function(){
			animateCSS('#editorCAM','bounceInRight');
		});
    })

    $('.td').click(function(event){
        edit($(this).data('id'));
	})

	$('.btn_qr').click(function(){
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); 
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún elemento');
            exit();
        }
		
		//block_espere();
        $('#frm').attr('action',"{{url('/ferias/asistentes/print_qr')}}");
        $('#frm').submit();
    });

    $('.btn_export_qr').click(function(){
		var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); 
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún elemento');
            exit();
        }
        $('#frm').attr('action',"{{url('/ferias/asistentes/export_qr')}}");
        $('#frm').submit();
    });
	
	$("#chktodos").click(function(){
		$('.chkpuesto').not(this).prop('checked', this.checked);
	});

	</script>

@endsection