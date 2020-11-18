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
				<h3 class="panel-title">Incidencias abiertas</h3>
			</div>
			<div class="panel-body">
				<div id="all_toolbar">
					<div class="input-group">
						<input type="text" class="form-control pull-left" id="fechas" name="fechas" style="height: 40px; width: 200px" value="{{ $f1->format('d/m/Y').' - '.$f2->format('d/m/Y') }}">
						<span class="btn input-group-text btn-mint"  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
						<button id="btn-toggle" class="btn btn-mint float-right ml-3 add-tooltip" title="Cambiar vista tabla/tarjetas"><i class="fal fa-table"></i> | <i class="fal fa-credit-card-blank mt-1"></i></button>
					</div>
				</div>
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
							<th data-sortable="true">Puesto</th>
							<th data-sortable="true">Edificio</th>
                            <th data-sortable="true">Planta</th>
							<th data-sortable="true">Fecha</th>
							<th data-sortable="true">Estado</th>
							<th data-sortable="true">Tipo</th>
							<th style="width: 30%" data-sortable="true">Incidencia</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($incidencias as $inc)
							<tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
								<td>{{$inc->id_incidencia}}</td>
								<td><i class="{{ $inc->val_icono }} fa-2x" style="color:{{ $inc->val_color }}"></i></td>
								<td>{{ $inc->des_puesto}}</td>
                                <td>{{ $inc->des_edificio}}</td>
                                <td>{{ $inc->des_planta}}</td>
								<td>{!! beauty_fecha($inc->fec_apertura)!!}</td>
								<td>@if(isset($inc->fec_cierre)) <div class="bg-success text-xs text-white text-center rounded b-all" style="padding: 5px" id="cell{{$inc->id_incidencia}}">Cerrada</div> @else  <div class="bg-pink  text-xs text-white text-center rounded b-all"  style="padding: 5px" id="cell{{$inc->id_incidencia}}">Abierta </div>@endif</td>  
								<td>
									<div class="rounded"  style="padding: 3px; width:100%: height: 100%; background-color: {{ $inc->val_color  }}; {{ txt_blanco($inc->val_color=='text-white')?'color: #fff':'color:#222' }}">
										{{$inc->des_tipo_incidencia}}
									</div>
									
								</td>
								<td style="position: relative; vertical-align: middle" class="pt-2">
									{{ $inc->des_incidencia}}
									<div class="floating-like-gmail mt-2 w-100" style="width: 100%">
										@if (checkPermissions(['Incidencias'],["W"]))<a href="#" title="Ver incidencia " data-id="{{ $inc->id_incidencia }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $inc->id_incidencia }})"><span class="fa fa-eye pt-1" aria-hidden="true"></span> Ver</a>@endif
                                        @if (!isset($inc->fec_cierre) && checkPermissions(['Incidencias'],["W"]))<a href="#cerrar-incidencia" title="Cerrar incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-cierre" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-cierre{{ $inc->id_incidencia }}" onclick="cierre_incidencia({{ $inc->id_incidencia}})"><span class="fad fa-thumbs-up pt-1" aria-hidden="true"></span> Cerrar</a>@endif
										@if (isset($inc->fec_cierre) && checkPermissions(['Incidencias'],["W"]))<a href="#reabrir-incidencia" title="Reabrir incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-reabrir" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-reabrir{{ $inc->id_incidencia }}" onclick="reabrir_incidencia({{ $inc->id_incidencia}})"><i class="fad fa-external-link-square-alt"></i> Reabrir</a>@endif
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
								</td>
							</tr>
						@endforeach
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
				<div class="modal-header"><i class="mdi mdi-thumb-up text-success mdi-48px"></i><b>
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
<div class="modal fade" id="nueva-incidencia">
	<div class="modal-dialog modal-md">
		<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
			<div class="modal-header"><i class="fad fa-plus-hexagon text-info fa-2x"></i><b>
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
        });
    //$('#frm_contador').on('submit',form_ajax_submit);

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
            window.location.href = '{{ url('/incidencias/') }}/'+start_date.format('YYYY-MM-DD')+'/'+end_date.format('YYYY-MM-DD');
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