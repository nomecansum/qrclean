@extends('layout')




@section('styles')
<link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de incidencias</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
	<li class="breadcrumb-item">mantenimiento</li>
	<li class="breadcrumb-item">incidencias</li>
	{{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
</ol>
@endsection

@section('content')
@php
    //dd($incidencias);
@endphp
<script type="text/javascript"  src="{{url('/plugins/dropzone/dropzone.js')}}"></script>
<script src="{{url('plugins')}}/amcharts4/core.js"></script>
<script src="{{url('plugins')}}/amcharts4/charts.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/material.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/animated.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/kelly.js"></script>
<script src="{{url('plugins')}}/amcharts4/lang/es_ES.js"></script>


<div class="row botones_accion">
	<div class="col-md-4">

	</div>
	<div class="col-md-6">
		<br>
	</div>
	<div class="col-md-2 text-end">
		@if(checkPermissions(['Incidencias'],['C']))
		<div class="btn-group btn-group-sm pull-right" role="group">
				<a href="#nueva-incidencia" id="btn_nueva_incidencia" class="btn btn-success" data-toggle="modal" title="Nueva incidencia">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nueva</span>
			</a>
		</div>
		@endif
	</div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="row mt-2">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Incidencias</h3>
			</div>
			<form method="post" name="form_puestos" id="formbuscador" action="{{ url('/incidencias') }}" class="form-horizontal ajax-filter">
				@csrf
				<input type="hidden" name="document" value="pantalla">
				<input type="hidden" name="output" value="pantalla">
				@include('resources.combos_filtro',[$hide=['est_mark'=>1,'tip_mark'=>1],$show=['proc'=>1]])
				<div class="row">
					<div class=" col-md-4 text-nowrap">
						@include('resources.combo_fechas')
					</div>
					<div class="col-md-3" style="padding-left: 15px">
						<div class="form-group">
							<label>Situacion</label>
							<select class="form-control" id="ac" name="ac">
									<option value="A" >Abiertas</option>
									<option value="C" >Cerradas</option>
									<option value="B" >Todas</option>
							</select>
						</div>
					</div>
				</div>
			</form>
			<div class="card-body">
				{{-- <div id="all_toolbar">
					<div class="input-group">
						<input type="text" class="form-control pull-left" id="fechas" name="fechas" style="height: 40px; width: 200px" value="{{ $f1->format('d/m/Y').' - '.$f2->format('d/m/Y') }}">
						<span class="btn input-group-text btn-secondary"  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
						<button id="btn-toggle" class="btn btn-secondary float-right ml-3 add-tooltip" title="Cambiar vista tabla/tarjetas"><i class="fal fa-table"></i> | <i class="fal fa-credit-card-blank mt-1"></i></button>
					</div>
				</div> --}}
				<table id="tabla"  data-toggle="table" data-mobile-responsive="true"
					data-locale="es-ES"
					data-search="true"
					data-show-columns="true"
					data-show-columns-toggle-all="true"
					data-page-list="[5, 10, 20, 30, 40, 50]"
					data-page-size="50"
					data-pagination="true" 
					data-show-toggle="true"
					data-show-button-text="true"
					data-toolbar="#all_toolbar"
					>
					<thead>
						<tr>
							<th data-sortable="true">Id</th>
							<th data-sortable="true">Tipo</th>
							<th data-sortable="true">Puesto</th>
							<th data-sortable="true">Edificio</th>
							<th data-sortable="true">Planta</th>
							<th data-sortable="true">Fecha</th>
							<th data-sortable="true">Situacion</th>
							
							<th style="width: 30%" data-sortable="true">Incidencia</th>
						</tr>
					</thead>
					<tbody  id="myFilter">
					@include('incidencias.fill_tabla_incidencias')
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>




<div class="modal fade" id="cerrar-incidencia" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form method="POST" action="{{ url('/incidencias/cerrar') }}" accept-charset="UTF-8" class="form-horizontal form-ajax">	
		@csrf
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					
					<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
					<h1 class="modal-title text-nowrap">Cerrar incidencia</h1>
					<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
						<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
					</button>
				</div>
				<div class="modal-body text-start" id="body_cierre">
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-info btn_cerrar_incidencia">Cerrar</button>
					<button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cancelar</button>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal fade" id="accion-incidencia">
	<form method="POST" action="{{ url('/incidencias/accion') }}" accept-charset="UTF-8" class="form-horizontal form-ajax" enctype="multipart/form-data">	
		@csrf
		<input type="hidden" name="adjuntos[]" id="adjuntos" value="">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<span class="float-right" id="spinner_acc" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
					<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
					<h1 class="modal-title text-nowrap">Añadir accion</h1>
					<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
						<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
					</button>
				</div>
				<div class="modal-body" id="body_accion">
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-info btn_accion_incidencia">Añadir</button>
					<button type="button" data-dismiss="modal" class="btn btn-warning close " onclick="cerrar_modal()">Cancelar</button>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal fade" id="nueva-incidencia">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
                
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Nueva incidencia</h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
			<div class="modal-body">
				<div class="form-group col-md-12">
					<label for="id_cliente" class="control-label">Puesto</label>
					<select class="form-control select2" id="id_puesto" name="id_puesto">
						<option value="" ></option>
						@php
							$planta=0;
							$edificio=0;	
						@endphp
						@foreach ($puestos as $puesto)
							@if($edificio!= $puesto->id_edificio)
								<optgroup label="{{ $puesto->des_edificio }}"></optgroup>
								@php $edificio=$puesto->id_edificio @endphp
							@endif
							@if($planta!= $puesto->id_planta)
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<optgroup label="{{ $puesto->des_planta }}"></optgroup>
								@php $planta=$puesto->id_planta @endphp
							@endif
							<option value="{{ $puesto->id_puesto }}" >
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ nombrepuesto($puesto) }}
							</option>
						@endforeach
					</select>
					{!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
				</div>
				
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
	
	
	<script>

	var lista_ficheros=new Array(0);

	$('.mantenimiento').addClass('active active-sub');
	$('.incidencias').addClass('active-link');

	$('#btn-toggle').click(function(){
         $('#tabla').bootstrapTable('toggleView')
    })

	$('.form_cierre').submit(form_ajax_submit);
	//$('.formbuscador').submit(ajax_filter);

	$(function(){

		$('#fechas, #ac').change(function(){
			$('#formbuscador').submit();
		})

		$('#divfiltro').hide();
	})     

	function change_fechas(){
		$('#formbuscador').submit();
	}


	$('#accion-incidencia').on('shown.bs.modal', function (e) {
		window.Laravel = {!! json_encode([
			'csrfToken' => csrf_token(),
		]) !!};
		
		//Dropzone para adjuntos de acciones
		lista_ficheros=[];	
		$('#adjuntos').val('');
		var myDropzone = new Dropzone("#dZUpload" , {
			url: '{{ url('/incidencias/upload_imagen/') }}',
			autoProcessQueue: true,
			uploadMultiple: true,
			parallelUploads: 1,
			maxFiles: {{ $config->num_imagenes_incidencias??2 }},
			addRemoveLinks: true,
			maxFilesize: 15,
			autoProcessQueue: true,
			acceptedFiles: 'image/*,video/*',
			dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Arrastre archivos <span class="font-xs">para subirlos</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (O haga Click)</h4></span>',
			dictResponseError: 'Error subiendo fichero!',
			dictDefaultMessage :
				'<span class="bigger-150 bolder"><i class=" fa fa-caret-right red"></i> Drop files</span> to upload \
				<span class="smaller-80 grey">(or click)</span> <br /> \
				<i class="upload-icon fa fa-cloud-upload blue fa-3x"></i>'
			,
			dictResponseError: 'Error while uploading file!',
			headers: {
				'X-CSRF-TOKEN': Laravel.csrfToken
			},
			init: function() {
				dzClosure = this; // Makes sure that 'this' is understood inside the functions below.
				this.on("sending", function(file, xhr, formData) {
					formData.append("id_cliente", {{ Auth::user()->id_cliente }});
					// formData.append("enviar_email", $("#enviar_email").is(':checked'));
					console.log(formData)
				});
				
				//send all the form data along with the files:
				this.on("sendingmultiple", function(data, xhr, formData) {
					console.log("multiple")
				});

				this.on("drop", function(event) {
					
				});

				this.on("removedfile", function(event) {
					console.log(event);
					value=event.name;
					lista_ficheros = lista_ficheros.filter(item => item.orig !== value);
					console.log(lista_ficheros);     
					ficheros_final=lista_ficheros.map(function(item,index,array){
						return item.nuevo;
					});
					$('#adjuntos').val(ficheros_final);
				});


				this.on("maxfilesexceeded", function(event) {
					toast_warning('Incidencias','El numero maximo de adjuntos es {{ $config->num_imagenes_incidencias??2 }}')   
				});

				this.on("success", function(file, responseText) {
					//Dropzone.forElement("#dZUpload").removeAllFiles(true);
					fic=new Object();
					fic.orig=responseText.filename;
					fic.nuevo=responseText.newfilename;
					lista_ficheros.push(fic);
					ficheros_final=lista_ficheros.map(function(item,index,array){
						return item.nuevo;
					});
					$('#adjuntos').val(ficheros_final);
					console.log(lista_ficheros);
				});
			}
		});

	});

	$('.btn_accion_incidencia').click(function(){
		$('#spinner_acc').show();
	})

	function post_form_ajax(data){
		$('#cell'+data.id).removeClass('bg-pink');
		$('#cell'+data.id).addClass('bg-success');
		$('#cell'+data.id).html('Cerrada');
		$('#boton-cierre'+data.id).hide();
	}
   
   function cierre_incidencia(id){
	 	$('#cerrar-incidencia').modal('show');
	    $('#des_incidencia_cerrar').html($(this).data('desc'));
		$('#body_cierre').load("{{ url('/incidencias/form_cierre/') }}/"+id);
   }

   

   function accion_incidencia(id){
		$('#accion-incidencia').modal('show');
		$('#spinner_acc').hide();
	    $('#des_incidencia_accion').html($(this).data('desc'));
		
		$.get("{{ url('/incidencias/form_accion/') }}/"+id,function(data){
			$('#body_accion').html(data);
		})
   	}

   function reabrir_incidencia(id){
		$.post('{{url('/incidencias/reabrir')}}', {_token: '{{csrf_token()}}',id_incidencia:id}, function(data, textStatus, xhr) {
            console.log(data);
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
				$('#cell'+id).removeClass('bg-success');
				$('#cell'+id).addClass('bg-pink');
				$('#cell'+id).html('Abierta');
                toast_ok(data.title,data.message);
				
            }
        }) 
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        })
        .always(function(data){
            if(data.url){
                setTimeout(()=>{window.open(data.url,'_self')},3000);
            } 
            
        });
   }

    $('#val_icono').iconpicker({
        icon:'{{isset($t) ? ($t->val_icono) : ''}}'
    });

	function edit(id){
		$('#editorCAM').load("{{ url('/incidencias/edit/') }}"+"/"+id, function(){
			animateCSS('#editorCAM','bounceInRight');
		});
	}


    $('.td').click(function(event){
        editar( $(this).data('id'));
	})

	$('#id_puesto').change(function(e){
		//window.open("{{ url('incidencias/create') }}/"+$('#id_puesto').val(),'_self');
		$('#editorCAM').load("{{ url('incidencias/create') }}/"+$('#id_puesto').val()+'/embed', function(){
			animateCSS('#editorCAM','bounceInRight');
			$('.modal').modal('hide');
		});
	})
	

	</script>

@endsection