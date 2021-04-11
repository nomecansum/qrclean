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
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Incidencias</h3>
			</div>
			<form method="post" name="form_puestos" id="formbuscador" action="{{ url('/incidencias') }}" class="form-horizontal ajax-filter">
				@csrf
				<input type="hidden" name="document" value="pantalla">
				<input type="hidden" name="output" value="pantalla">
				@include('resources.combos_filtro',[])
				<div class="col-md-3" style="padding-left: 15px">
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
				<br>
			</form>
			<div class="panel-body">
				{{-- <div id="all_toolbar">
					<div class="input-group">
						<input type="text" class="form-control pull-left" id="fechas" name="fechas" style="height: 40px; width: 200px" value="{{ $f1->format('d/m/Y').' - '.$f2->format('d/m/Y') }}">
						<span class="btn input-group-text btn-mint"  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
						<button id="btn-toggle" class="btn btn-mint float-right ml-3 add-tooltip" title="Cambiar vista tabla/tarjetas"><i class="fal fa-table"></i> | <i class="fal fa-credit-card-blank mt-1"></i></button>
					</div>
				</div> --}}
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
							<th data-sortable="true">Id</th>
							<th></th>
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
<div class="modal fade" id="cerrar-incidencia">
	<form method="POST" action="{{ url('/incidencias/cerrar') }}" accept-charset="UTF-8" class="form-horizontal form-ajax">	
		@csrf
		<div class="modal-dialog modal-md">
			<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
				<div class="modal-header"><i class="mdi mdi-thumb-up text-success mdi-48px"></i>
					Cerrar incidencia <span id="des_incidencia_cerrar"></span>
				</div>
				<div class="modal-body" id="body_cierre">
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-info btn_cerrar_incidencia">Si</button>
					<button type="button" data-dismiss="modal" class="btn btn-warning">Cancelar</button>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal fade" id="accion-incidencia">
	<form method="POST" action="{{ url('/incidencias/accion') }}" accept-charset="UTF-8" class="form-horizontal form-ajax" enctype="multipart/form-data">	
		@csrf
		<div class="modal-dialog modal-md">
			<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
				<div class="modal-header"><i class="fad fa-plus fa-2x"></i>
					Añadir accion a la incidencia <span id="des_incidencia_accion"></span>
				</div>
				<div class="modal-body" id="body_accion">
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-info btn_accion_incidencia">Si</button>
					<button type="button" data-dismiss="modal" class="btn btn-warning">Cancelar</button>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal fade" id="nueva-incidencia">
	<div class="modal-dialog modal-md">
		<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
			<div class="modal-header"><i class="fad fa-plus-hexagon text-info fa-2x"></i>
				Nueva incidencia 
			</div>
			<div class="modal-body">
				<div class="form-group col-md-12">
					<label for="id_cliente" class="control-label">Puesto</label>
					<select class="form-control" id="id_puesto" name="id_puesto">
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
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $puesto->des_puesto }}
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
	})     

	$('.minicolors').minicolors({
          control: $(this).attr('data-control') || 'hue',
          defaultValue: $(this).attr('data-defaultValue') || '',
          format: $(this).attr('data-format') || 'hex',
          keywords: $(this).attr('data-keywords') || '',
          inline: $(this).attr('data-inline') === 'true',
          letterCase: $(this).attr('data-letterCase') || 'lowercase',
          opacity: $(this).attr('data-opacity'),
          position: $(this).attr('data-position') || 'bottom',
          swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
          change: function(value, opacity) {
            if( !value ) return;
            if( opacity ) value += ', ' + opacity;
          },
          theme: 'bootstrap'
        })

		

	 //Date range picker
	 $('#fechas').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            },
            opens: 'right',
        }, function(start_date, end_date) {
            $('#fechas').val(start_date.format('DD/MM/YYYY')+' - '+end_date.format('DD/MM/YYYY'));
			// console.log($('#fechas').val());
			// $('#formbuscador').submit();
            //window.location.href = '{{ url('/incidencias/') }}/'+start_date.format('YYYY-MM-DD')+'/'+end_date.format('YYYY-MM-DD');
        });

	function post_form_ajax(data){
		console.log(data);
		$('#cell'+data.id).removeClass('bg-pink');
		$('#cell'+data.id).addClass('bg-success');
		$('#cell'+data.id).html('Cerrada');
		$('#boton-cierre'+data.id).hide();
	}
   
   function cierre_incidencia(id){
	    $('#des_incidencia_cerrar').html($(this).data('desc'));
		$('#body_cierre').load("{{ url('/incidencias/form_cierre/') }}/"+id);
   }

   function accion_incidencia(id){
	    $('#des_incidencia_accion').html($(this).data('desc'));
		$('#body_accion').load("{{ url('/incidencias/form_accion/') }}/"+id);
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
		window.open("{{ url('incidencias/create') }}/"+$('#id_puesto').val(),'_self');
	})

	</script>

@endsection