<style type="text-css">
	.center_input {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}
</style>

<div class="card editor mb-5" id="editor">
	@php
		//dd($c);
	@endphp
    <div class="card">
		<div class="card-header toolbar">
			<div class="toolbar-start">
				<h5 class="m-0">
					@if($c->id_cliente!=0)
					<h3 class="card-title">Modificar cliente</h3>
					@else
					<h3 class="card-title">Nuevo cliente</h3>
					@endif
				</h5>
			</div>
			<div class="toolbar-end">
				<button type="button" class="btn-close btn-close-card">
					<span class="visually-hidden">Close the card</span>
				</button>
			</div>
		</div>
        <div class="card-body">
			@if($c->id_cliente!=0)
				<form action="{{url('/clientes/update')}}" method="POST" class="form-ajax" id="frm_cliente" enctype='multipart/form-data'>
				<input type="hidden" name="id" value="{{$c->id_cliente}}">
			@else
				<form action="{{url('/clientes/save')}}" method="POST" class="form-ajax"  id="frm_cliente" enctype='multipart/form-data'>
			@endif
			{{csrf_field()}}
			<div class="tab-base">
					
				<!--Nav tabs-->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item active" role="presentation">
						<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-1" type="button" role="tab" aria-controls="general" aria-selected="true">Datos generales</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-2" type="button" role="tab" aria-controls="config" aria-selected="true">Configuracion</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-3" type="button" role="tab" aria-controls="logos" aria-selected="true">Logos</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-4" type="button" role="tab" aria-controls="tema" aria-selected="true">Tema</button>
					</li>
				</ul>
	
				<!--Tabs Content-->
				<div class="tab-content">
					<div id="demo-stk-lft-tab-1" class="tab-pane fade "  role="tabpanel" aria-labelledby="general-tab">
						<div class="row">
							<div class="col-12">
								<div class="row">
									<div class="col-sm-12">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label for="">Nombre</label>
													<input type="text" name="nom_cliente" class="form-control" required value="{{isset($c) ? $c->nom_cliente : ''}}">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label for="">Distribuidor</label>
													<select name="id_distribuidor" id="id_distribuidor" class="form-control" style="width: 100%">
														<option value=""></option>
														@foreach (\DB::table('distribuidores')->get() as $d)
															<option {{isset($c) && $c->id_distribuidor == $d->id_distribuidor ? 'selected' : ''}} value="{{$d->id_distribuidor}}">{{$d->nom_distribuidor}}</option>
														@endforeach
													</select>
													
												</div>
											</div>
										</div>
										
										<div class="row mt-2">
											<div class="col-sm-12">
												<div class="form-group">
													<label for="">Informacion de contacto</label>
													<input type="text" name="nom_contacto" class="form-control" value="{{isset($c) ? $c->nom_contacto : ''}}">
												</div>
											</div>
										</div>
										<div class="row mt-2">
											<div class="col-sm-12">
												<label class="control-label">Token registro</label>
												<div class="input-group mb-3">
													<input type="text" name="token_1uso" readonly=""  id="token_1uso"  class="form-control" value="{{isset($c) ? $c->token_1uso : ''}}">
													<div class="input-group-btn">
														@if(checkPermissions(['Clientes'],["W"]))<button class="btn btn-secondary" type="button"  id="btn_generar_token">Generar</button>@endif
													</div>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
						
						
					</div>
					<div id="demo-stk-lft-tab-2" class="tab-pane fade"  role="tabpanel" aria-labelledby="config-tab">
						<div class="row">
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_restringir_usuarios_planta"  id="mca_restringir_usuarios_planta" value="S" {{ isset($config->mca_restringir_usuarios_planta)&&$config->mca_restringir_usuarios_planta=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_restringir_usuarios_planta">Restringir plantas usuarios</label>
								</div>
							</div>
							<div class="col-md-3  mt-1">
								<div class="form-check pt-2">
									<input  name="mca_limpieza"  id="mca_limpieza" value="S" {{ isset($config->mca_limpieza)&&$config->mca_limpieza=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_limpieza">Funcion de limpieza</label>
								</div>
							</div>
							<div class="col-md-3  mt-1">
								<div class="form-check pt-2">
									<input  name="mca_salas"  id="mca_salas" value="S" {{ isset($config->mca_salas)&&$config->mca_salas=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label"  for="mca_salas">Gestion de salas</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input  name="mca_permitir_anonimo"  id="mca_permitir_anonimo" value="S" {{ isset($config->mca_permitir_anonimo)&&$config->mca_permitir_anonimo=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_permitir_anonimo">Permitir escaneo anónimo</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input  name="mca_reserva_horas"  id="mca_reserva_horas" value="S" {{ isset($config->mca_reserva_horas)&&$config->mca_reserva_horas=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" ffor="mca_reserva_horas">Reservas por horas</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_mostrar_nombre_usando"  id="mca_mostrar_nombre_usando" value="S" {{ isset($config->mca_mostrar_nombre_usando)&&$config->mca_mostrar_nombre_usando=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_mostrar_nombre_usando">Mostrar nombre que usa puesto</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input  name="mca_liberar_puestos_auto"  id="mca_liberar_puestos_auto" value="S" {{ isset($config->mca_liberar_puestos_auto)&&$config->mca_liberar_puestos_auto=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_liberar_puestos_auto">Liberar puestos auto</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_mostrar_datos_fijos"  id="mca_mostrar_datos_fijos" value="S" {{ isset($config->mca_mostrar_datos_fijos)&&$config->mca_mostrar_datos_fijos=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label"  for="mca_mostrar_datos_fijos">Mostrar datos fijos</label>
								</div>
							</div>
							
							
						</div>

						<div class="row mt-4">
							<div class="col-md-2">
								<div class="form-group">
									<label for="">Notificar a usuarios</label>
									<select name="val_metodo_notificacion" id="val_metodo_notificacion" class="form-control">
										<option value="0"  {{isset($config->val_metodo_notificacion) && $config->val_metodo_notificacion == 0 ? 'selected' : ''}}>No</option>
										<option value="1"  {{isset($config->val_metodo_notificacion) && $config->val_metodo_notificacion == 1 ? 'selected' : ''}}>e-mail</option>
										{{--  <option value="2"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Notificacion APP</option>
										<option value="3"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Ambas</option>  --}}
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="">Tamaño QR</label>
									<input type="number" class="form-control" min="50" max="500"  required name="tam_qr" value="{{ $config->tam_qr??230 }}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="">Layout incidencias</label>
									<select name="val_layout incidencias" id="val_layout incidencias" class="form-control ">
										<option value="A"  {{isset($config->val_layout_incidencias) && $config->val_layout_incidencias == 'A' ? 'selected' : ''}}>Titulo y descripcion</option>
										<option value="T"  {{isset($config->val_layout_incidencias) && $config->val_layout_incidencias == 'T' ? 'selected' : ''}}>Solo titulo</option>
										<option value="D"  {{isset($config->val_layout_incidencias) && $config->val_layout_incidencias == 'D' ? 'selected' : ''}}>Solo descripcion</option>
										{{--  <option value="2"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Notificacion APP</option>
										<option value="3"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Ambas</option>  --}}
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="">Imagenes incidencias</label>
									<input type="number" class="form-control" min="0" max="2"  required name="num_imagenes_incidencias" value="{{ $config->num_imagenes_incidencias??1 }}">
								</div>
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-3">
								<label for="modo_visualizacion_reservas">Vista por defecto en reservas</label>
								<select name="modo_visualizacion_reservas" id="modo_visualizacion_reservas" class="form-control" style="width: 100%">
									<option value="M" {{ isset($config->modo_visualizacion_reservas)&&$config->modo_visualizacion_reservas=='M'?'selected':'' }}>Mosaico (Puestos)</option>
									<option value="P" {{ isset($config->modo_visualizacion_reservas)&&$config->modo_visualizacion_reservas=='P'?'selected':'' }}>Plano</option>
								</select>
							</div>
							<div class="col-md-2">
								<label for="modo_visualizacion_puestos">Vista de puesto</label>
								<select name="modo_visualizacion_puestos" id="modo_visualizacion_puestos" class="form-control" style="width: 100%">
									<option value="C" {{ isset($config->modo_visualizacion_puestos)&&$config->modo_visualizacion_puestos=='C'?'selected':'' }}>Cuadro</option>
									<option value="I" {{ isset($config->modo_visualizacion_puestos)&&$config->modo_visualizacion_puestos=='I'?'selected':'' }}>Icono</option>
								</select>
							</div>
							<div class="col-md-3">
								<label for="val_campo_puesto_mostrar">Mostrar como nombre de puesto</label><br>
								<select name="val_campo_puesto_mostrar" id="val_campo_puesto_mostrar" class="form-control" style="width: 100%">
									<option value="D" {{ isset($config->val_campo_puesto_mostrar)&&$config->val_campo_puesto_mostrar=='D'?'selected':'' }}>Descripcion</option>
									<option value="I" {{ isset($config->val_campo_puesto_mostrar)&&$config->val_campo_puesto_mostrar=='I'?'selected':'' }}>Identificador</option>
									<option value="A" {{ isset($config->val_campo_puesto_mostrar)&&$config->val_campo_puesto_mostrar=='A'?'selected':'' }}>[Identif] Descripcion</option>
								</select>
							</div>
							{{-- <div class="form-group col-md-2" style="{{ $config->mca_liberar_puestos_auto=='N'?'display:none':'' }}" id="grupo_liberar">
								<label for="hora_liberar_puestos">Hora def. de liberar</label><br>
								<input type="time" autocomplete="off" name="hora_liberar_puestos" id="hora_liberar_puestos"   class="form-control" value="{{isset($config->hora_liberar_puestos)?$config->hora_liberar_puestos:'23:59'}}" />
							</div> --}}
							<div class="form-group col-md-2" style="{{ isset($config->mca_liberar_puestos_auto) && $config->mca_liberar_puestos_auto=='N'?'display:none':'' }}" id="grupo_liberar">
								<label for="mca_mostrar_puestos_reservas">Mostrar en reserva</label><br>
								<select name="mca_mostrar_puestos_reservas" id="mca_mostrar_puestos_reservas" class="form-control" style="width: 100%">
									<option value="D" {{ isset($config->mca_mostrar_puestos_reservas)&&$config->mca_mostrar_puestos_reservas=='D'?'selected':'' }}>Solo disponibles</option>
									<option value="T" {{ isset($config->mca_mostrar_puestos_reservas)&&$config->mca_mostrar_puestos_reservas=='T'?'selected':'' }}>Todos</option>
								</select>
							</div>
							
							<div class="form-group col-md-2">
								<label for="max_dias_reserva">Max de dias reserva</label><br>
								<select name="max_dias_reserva" id="max_dias_reserva"  class="form-control ">
									@for ($n=1;$n<=31;$n++)
										<option value={{$n}}  {{  isset($config->max_dias_reserva)&&$config->max_dias_reserva==$n?'selected':''  }}>{{ $n }}</option>
									@endfor
									
								</select>
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-12 text-muted">
								Rango de horas de reserva
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="max_horas_reservar">Min</label>
									<input type="text" autocomplete="off" name="min_hora_reservas" id="min_hora_reservas"   class="form-control hourMask" value="{{isset($config->min_hora_reservas)?decimal_to_time($config->min_hora_reservas/60):'00:00'}}" />						
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="max_horas_reservar">Max</label>
									<input type="text" autocomplete="off" name="max_hora_reservas" id="max_hora_reservas"   class="form-control hourMask" value="{{isset($config->max_hora_reservas)?decimal_to_time($config->max_hora_reservas/60):'23:59'}}" />
								</div>
							</div>

						</div>

					</div>
					<div id="demo-stk-lft-tab-3" class="tab-pane fade"  role="tabpanel" aria-labelledby="logos-tab">
						<div class="row mb-0">
							<div class="col-md-6 text-center bg-gray-light pad-all">
								<img src="img/img_logo_grande.png" style="width: 50px"> Logo grande (Home, informes) <span style="font-size:8px"> 500px</span></label>
							</div>
							<div class="col-md-6 text-center mb-0 bg-gray-light pad-all">
								<img src="img/img_logo_menu.png"  style="width: 50px"> Logo pequeño (Menu) <span style="font-size:8px">55X55px</span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 text-center b-all">
								<img src="{{ isset($c) ? Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$c->img_logo) : ''}}" style="width: 300px; margin-top: 50px" alt="" class="img-fluid ml-0">
								<div class="form-group">
	
									<div class="custom-file center_input ">
										<input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_logo" id="img_logo" lang="es">
										<label class="custom-file-label" for="img_logo"></label>
									</div>
								</div>
							</div>
							<div class="col-md-6 text-center b-all" >
								<img src="{{ isset($c) ? Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$c->img_logo_menu) : ''}}" style="idth: 300px;  margin-top: 50px" alt="" class="img-fluid ml-0">
								<div class="form-group">
									<div class="custom-file center_input ">
										<input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_logo_menu" id="img_logo_menu" lang="es">
										<label class="custom-file-label" for="img_logo_menu"></label>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					<div id="demo-stk-lft-tab-4" class="tab-pane fade active show"  role="tabpanel" aria-labelledby="tema-tab">
						<p class="text-main text-semibold">Tema que tendrán por defecto los usuarios que no lo hayan personalizado</p>
						<input type="hidden" name="tema" id="tema" value="{{ isset($config->theme->tema)?$config->theme->tema:'/color-schemes' }}"> 
						<input type="hidden" name="rootClass" id="rootClass"  value="{{ isset($config->theme->rootClass)?$config->theme->rootClass:'' }}"> 
						<input type="hidden" name="esquema" id="esquema"  value="{{ isset($config->theme->esquema)?$config->theme->esquema:'' }}"> 
						<input type="hidden" name="menu" id="menu"  value="{{ isset($config->theme->menu)?$config->theme->menu:'mn--max' }}"> 
						@include('customers.setting_include')
					</div>
				</div>
			</div>
			
			<div class="row mt-2">
				<div class="col-md-12 text-end">
					@if(checkPermissions(['Clientes'],["C"]) || checkPermissions(['Clientes'],["W"]))<button type="submit" class="btn btn-primary btn_guardar">Guardar</button>@endif
				</div>
			</div>
			
			

		</div>
	</div>
</form>
	<script src="{{ asset('/plugins/inputmask/dist/inputmask.js') }}"></script>
	<script src="{{ asset('/plugins/inputmask/dist/jquery.inputmask.js') }}"></script>
	<script src="{{ asset('/plugins/inputmask/dist/bindings/inputmask.binding.js') }}"></script>
	<script>
		$('.form-ajax').submit(form_ajax_submit);

		$('.select2').select2();

		document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

		@if(checkPermissions(['Clientes'],["W"]))
			$('#btn_generar_token').click(function(event){
				$.get( "/clientes/gen_key")
				.done(function( data, textStatus, jqXHR ) {
					$('#token_1uso').val(data);
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
						console.log(errorThrown);
				});	
			})
			if($("#token_1uso").val() == "")
				$('#btn_generar_token').click();
		@endif
		
		$('.btn-scheme').click(function(){
			if($(this).data('title')=='Dark'){
				$('.light').hide();
			} else {
				$('.light').show();
			}
				
		})

		if(localStorage.getItem('theme')=='theme-dark-full'){
            $('.light').hide();
        }

		var demoSet             = $('#demo-nifty-settings'),
            niftyContainer      = $('#container'),
            niftyMainNav        = $('#mainnav-container'),
            niftyAside          = $('#aside-container'),
			demoSetBtn          = $('#demo-set-btn');



        // COLOR SCHEMES
        // =================================================================
       

        $('#demo-theme').on('click', '.demo-theme', function (e) {
			$('#demo-theme').removeClass('active');
			$('#demo-theme').removeClass('disabled');
            e.preventDefault();
            var el = $(this);
            // if (el.hasClass('disabled') || el.hasClass('active')) {
            //     return false;
            // }
			changeTheme(el.attr('data-theme'), el.attr('data-type'));
			$('#theme_type').val(el.attr('data-type'));
			$('#theme_name').val(el.attr('data-theme'));
            //themeBtn.removeClass('active');
            //el.addClass('active').tooltip('hide');
            return false;
        });


        demoSet.on('click', function(e){
            if (demoSet.hasClass('in')){
                if ($(e.target).is(demoSet)) demoSet.removeClass('in');
            }
        });

        demoSetBtn.on('click', function(){
            demoSet.toggleClass('in');
            return false;
        });
        $('#demo-btn-close-settings').on('click', function () {
            demoSetBtn.trigger('click')
        });

		$('input[type="file"]').change(function(e){
			var fileName = e.target.files[0].name;
			$(this).next('label').html(fileName);
			//$('.custom-file-label').html(fileName);
		});

		$('#mca_liberar_puestos_auto').click(function(){
			if($(this).is(':checked')){
				$('#grupo_liberar').show();
			} else{
				$('#grupo_liberar').hide();
			}
			
		})

		Inputmask({regex:"^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$"}).mask('.hourMask');

	
	</script>
@include('layouts.scripts_panel')