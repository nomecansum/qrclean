<div class="panel" id="editor">
	@php
		//dd($c);
	@endphp
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
			</div>
			@if($c->id_cliente!=0)
			<h3 class="panel-title">Modificar cliente</h3>
			@else
			<h3 class="panel-title">Nuevo cliente</h3>
			@endif
           
        </div>
        <div class="panel-body">
			<div class="row">
				<div class="col-12">

					@if($c->id_cliente!=0)
						<form action="{{url('/clientes/update')}}" method="POST" class="form-ajax" enctype='multipart/form-data'>
						<input type="hidden" name="id" value="{{$c->id_cliente}}">
					@else
						<form action="{{url('/clientes/save')}}" method="POST" class="form-ajax" enctype='multipart/form-data'>
					@endif
						
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
									<label class="control-label">Token registro</label>
									<div class="input-group mb-3">
										<input type="text" name="token_1uso" readonly=""  id="token_1uso"  class="form-control" value="{{isset($c) ? $c->token_1uso : ''}}">
										<div class="input-group-btn">
											@if(checkPermissions(['Clientes'],["W"]))<button class="btn btn-mint" type="button"  id="btn_generar_token">Generar</button>@endif
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="">Informacion de contacto</label>
										<input type="text" name="nom_contacto" class="form-control" value="{{isset($c) ? $c->nom_contacto : ''}}">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="">Distribuidor</label>
										<select name="id_distribuidor" id="id_distribuidor" class="form-control select2" style="width: 100%">
											<option value=""></option>
											@foreach (\DB::table('distribuidores')->get() as $d)
												<option {{isset($c) && $c->id_distribuidor == $d->id_distribuidor ? 'selected' : ''}} value="{{$d->id_distribuidor}}">{{$d->nom_distribuidor}}</option>
											@endforeach
										</select>
										
									</div>
								</div>
							</div>
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 p-b-10">
							<p class="text-main text-bold text-uppercase text-left">Logos</p>
						</div>
					</div>
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
							<img src="{{ isset($c) ? Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$c->img_logo) : ''}}" style="height: 150px; margin-top: 50px" alt="" class="img-fluid ml-0">
							<div class="form-group">

								<div class="custom-file">
									<input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_logo" id="img_logo" lang="es">
									<label class="custom-file-label" for="img_logo"></label>
								</div>
							</div>
						</div>
						<div class="col-md-6 text-center b-all">
							<img src="{{ isset($c) ? Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$c->img_logo_menu) : ''}}" style="height: 150px;  margin-top: 50px" alt="" class="img-fluid ml-0">
							<div class="form-group">
								<div class="custom-file">
									<input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_logo_menu" id="img_logo_menu" lang="es">
									<label class="custom-file-label" for="img_logo_menu"></label>
								</div>
							</div>
						</div>
					</div>
					
					<input type="hidden" name="theme_type" id="theme_type" value="{{ isset($config->theme_type)?$config->theme_type:'navy' }}"> 
					<input type="hidden" name="theme_name" id="theme_name"  value="{{ isset($config->theme_name)?$config->theme_name:'e' }}"> 
					<br>
					<br>
					<div class="row b-all rounded  p-b-10">
						<div class="col-md-12 p-b-10">
							<p class="text-main text-bold text-uppercase text-left">Configuracion de cliente</p>
							<div class="col-md-3">
								<input type="checkbox" class="form-control  magic-checkbox" name="mca_restringir_usuarios_planta"  id="mca_restringir_usuarios_planta" value="S" {{ isset($config->mca_restringir_usuarios_planta)&&$config->mca_restringir_usuarios_planta=='S'?'checked':'' }}> 
								<label class="custom-control-label"   for="mca_restringir_usuarios_planta">Restringir plantas usuarios</label>
							</div>
							<div class="col-md-3">
								<input type="checkbox" class="form-control  magic-checkbox" name="mca_limpieza"  id="mca_limpieza" value="S" {{ isset($config->mca_limpieza)&&$config->mca_limpieza=='S'?'checked':'' }}> 
								<label class="custom-control-label"   for="mca_limpieza">Funcion de limpieza</label>
							</div>
							<div class="col-md-3">
								<input type="checkbox" class="form-control  magic-checkbox" name="mca_permitir_anonimo"  id="mca_permitir_anonimo" value="S" {{ isset($config->mca_permitir_anonimo)&&$config->mca_permitir_anonimo=='S'?'checked':'' }}> 
								<label class="custom-control-label"   for="mca_permitir_anonimo">Permitir escaneo anónimo</label>
							</div>
							<div class="col-md-3">
								<input type="checkbox" class="form-control  magic-checkbox" name="mca_reserva_horas"  id="mca_reserva_horas" value="S" {{ isset($config->mca_reserva_horas)&&$config->mca_reserva_horas=='S'?'checked':'' }}> 
								<label class="custom-control-label"   for="mca_reserva_horas">Reservas por horas</label>
							</div>
							
						</div>

						<div class="row">
							<div class="col-md-12 p-b-10">
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Notificar a usuarios</label>
										<select name="val_metodo_notificacion" id="val_metodo_notificacion" class="form-control ">
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
							</div>
						</div>
						<DIV class="row">
							<div class="col-md-2 ml-2">
								<label for="modo_visualizacion_reservas">Vista defecto en reservas</label>
								<select name="modo_visualizacion_reservas" id="modo_visualizacion_reservas" class="form-control" style="width: 100%">
									<option value="M" {{ isset($config->modo_visualizacion_reservas)&&$config->modo_visualizacion_reservas=='M'?'selected':'' }}>Mapa</option>
									<option value="P" {{ isset($config->modo_visualizacion_reservas)&&$config->modo_visualizacion_reservas=='P'?'selected':'' }}>Plano</option>
								</select>
							</div>
							<div class="col-md-2 ml-2">
								<label for="modo_visualizacion_puestos">Representacion de puestos</label>
								<select name="modo_visualizacion_puestos" id="modo_visualizacion_puestos" class="form-control" style="width: 100%">
									<option value="C" {{ isset($config->modo_visualizacion_puestos)&&$config->modo_visualizacion_puestos=='C'?'selected':'' }}>Cuadro</option>
									<option value="I" {{ isset($config->modo_visualizacion_puestos)&&$config->modo_visualizacion_puestos=='I'?'selected':'' }}>Icono</option>
								</select>
							</div>
						</DIV>
					</div>

					<div class="row b-all rounded mt-3">
						<div class="row">
							<div class="col-lg-8" style="padding-left: 30px">
								<div id="demo-theme">
									<p class="text-main text-bold text-uppercase text-left">Tema de la aplicacion</p>
									<hr class="new-section-xs">
									<div class="clearfix demo-full-theme">
										<div class="col-md-6">
											<div class="media v-middle">
												<div class="media-left demo-single-theme">
													<a href="#" class="demo-theme demo-theme-light add-tooltip btn-scheme" data-theme="theme-light-full" data-type="full" data-title="Light"></a>
												</div>
												<div class="media-body">
													<p class="text-bold text-main mar-no text-uppercase text-sm">Claro</p>
													<small class="text-muted text-xs">Tema completamente claro</small>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="media v-middle">
												<div class="media-left demo-single-theme">
													<a href="#" class="demo-theme demo-theme-dark add-tooltip  btn-scheme" data-theme="theme-dark-full" data-type="full" data-title="Dark"></a>
												</div>
												<div class="media-body">
													<p class="text-bold text-main mar-no text-uppercase text-sm">Oscuro</p>
													<small class="text-muted text-xs">Tema completamente oscuro</small>
												</div>
											</div>
										</div>
									</div>
									
									<hr class="bord-no new-section-xs">
									Resalte de color (Solo puede seleccionarse uno)
									<div class="clearfix text-center demo-srow-scheme bg-gray-light pad-ver mar-top light">
										<div class="demo-theme-btn col-md-6">
											<p class="text-semibold text-uppercase text-xs text-muted">Moderno</p>
											<div class="media">
												<div class="media-left">
													<img src="img/color-schemes-e.png">
												</div>
												<div class="media-body">
													<p class="text-main text-bold text-sm mar-no">Modo cabecera completa</p>
												</div>
											</div>
										</div>
										<div class="demo-theme-btn col-md-6">
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-gray add-tooltip" data-theme="theme-gray" data-type="e" data-title="(E). Gray"></a>
												<a href="#" class="demo-theme demo-theme-navy add-tooltip" data-theme="theme-navy" data-type="e" data-title="(E). Navy Blue"></a>
												<a href="#" class="demo-theme demo-theme-ocean add-tooltip" data-theme="theme-ocean" data-type="e" data-title="(E). Ocean"></a>
		
												<a href="#" class="demo-theme demo-theme-lime add-tooltip" data-theme="theme-lime" data-type="e" data-title="(E). Lime"></a>
												<a href="#" class="demo-theme demo-theme-purple add-tooltip" data-theme="theme-purple" data-type="e" data-title="(E). Purple"></a>
												<a href="#" class="demo-theme demo-theme-dust add-tooltip" data-theme="theme-dust" data-type="e" data-title="(E). Dust"></a>
		
												<a href="#" class="demo-theme demo-theme-mint add-tooltip" data-theme="theme-mint" data-type="e" data-title="(E). Mint"></a>
												<a href="#" class="demo-theme demo-theme-yellow add-tooltip" data-theme="theme-yellow" data-type="e" data-title="(E). Yellow"></a>
												<a href="#" class="demo-theme demo-theme-well-red add-tooltip" data-theme="theme-well-red" data-type="e" data-title="(E). Well Red"></a>
		
												<a href="#" class="demo-theme demo-theme-coffee add-tooltip" data-theme="theme-coffee" data-type="e" data-title="(E). Coffee"></a>
												<a href="#" class="demo-theme demo-theme-prickly-pear add-tooltip" data-theme="theme-prickly-pear" data-type="e" data-title="(E). Prickly pear"></a>
												<a href="#" class="demo-theme demo-theme-dark add-tooltip" data-theme="theme-dark" data-type="e" data-title="(E). Dark"></a>
											</div>
										</div>
									</div>
									<div class="clearfix text-center light">
										<div class="demo-theme-btn col-md-3 pad-ver">
											<p class="text-semibold text-uppercase text-xs text-muted">Cabecera</p>
											<div class="mar-btm">
												<img src="img/color-schemes-a.png" class="img-responsive">
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-gray add-tooltip" data-theme="theme-gray" data-type="a" data-title="(A). Gray"></a>
												<a href="#" class="demo-theme demo-theme-navy add-tooltip" data-theme="theme-navy" data-type="a" data-title="(A). Navy Blue"></a>
												<a href="#" class="demo-theme demo-theme-ocean add-tooltip" data-theme="theme-ocean" data-type="a" data-title="(A). Ocean"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-lime add-tooltip" data-theme="theme-lime" data-type="a" data-title="(A). Lime"></a>
												<a href="#" class="demo-theme demo-theme-purple add-tooltip" data-theme="theme-purple" data-type="a" data-title="(A). Purple"></a>
												<a href="#" class="demo-theme demo-theme-dust add-tooltip" data-theme="theme-dust" data-type="a" data-title="(A). Dust"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-mint add-tooltip" data-theme="theme-mint" data-type="a" data-title="(A). Mint"></a>
												<a href="#" class="demo-theme demo-theme-yellow add-tooltip" data-theme="theme-yellow" data-type="a" data-title="(A). Yellow"></a>
												<a href="#" class="demo-theme demo-theme-well-red add-tooltip" data-theme="theme-well-red" data-type="a" data-title="(A). Well Red"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-coffee add-tooltip" data-theme="theme-coffee" data-type="a" data-title="(A). Coffee"></a>
												<a href="#" class="demo-theme demo-theme-prickly-pear add-tooltip" data-theme="theme-prickly-pear" data-type="a" data-title="(A). Prickly pear"></a>
												<a href="#" class="demo-theme demo-theme-dark add-tooltip" data-theme="theme-dark" data-type="a" data-title="(A). Dark"></a>
											</div>
										</div>
										<div class="demo-theme-btn col-md-3 pad-ver light">
											<p class="text-semibold text-uppercase text-xs text-muted">Marca</p>
											<div class="mar-btm">
												<img src="img/color-schemes-b.png" class="img-responsive">
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-gray add-tooltip" data-theme="theme-gray" data-type="b" data-title="(B). Gray"></a>
												<a href="#" class="demo-theme demo-theme-navy add-tooltip" data-theme="theme-navy" data-type="b" data-title="(B). Navy Blue"></a>
												<a href="#" class="demo-theme demo-theme-ocean add-tooltip" data-theme="theme-ocean" data-type="b" data-title="(B). Ocean"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-lime add-tooltip" data-theme="theme-lime" data-type="b" data-title="(B). Lime"></a>
												<a href="#" class="demo-theme demo-theme-purple add-tooltip" data-theme="theme-purple" data-type="b" data-title="(B). Purple"></a>
												<a href="#" class="demo-theme demo-theme-dust add-tooltip" data-theme="theme-dust" data-type="b" data-title="(B). Dust"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-mint add-tooltip" data-theme="theme-mint" data-type="b" data-title="(B). Mint"></a>
												<a href="#" class="demo-theme demo-theme-yellow add-tooltip" data-theme="theme-yellow" data-type="b" data-title="(B). Yellow"></a>
												<a href="#" class="demo-theme demo-theme-well-red add-tooltip" data-theme="theme-well-red" data-type="b" data-title="(B). Well red"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-coffee add-tooltip" data-theme="theme-coffee" data-type="b" data-title="(B). Coofee"></a>
												<a href="#" class="demo-theme demo-theme-prickly-pear add-tooltip" data-theme="theme-prickly-pear" data-type="b" data-title="(B). Prickly pear"></a>
												<a href="#" class="demo-theme demo-theme-dark add-tooltip" data-theme="theme-dark" data-type="b" data-title="(B). Dark"></a>
											</div>
										</div>
										<div class="demo-theme-btn col-md-3 pad-ver light">
											<p class="text-semibold text-uppercase text-xs text-muted">Menú principal</p>
											<div class="mar-btm">
												<img src="img/color-schemes-c.png" class="img-responsive">
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-gray add-tooltip" data-theme="theme-gray" data-type="c" data-title="(C). Gray"></a>
												<a href="#" class="demo-theme demo-theme-navy add-tooltip" data-theme="theme-navy" data-type="c" data-title="(C). Navy Blue"></a>
												<a href="#" class="demo-theme demo-theme-ocean add-tooltip" data-theme="theme-ocean" data-type="c" data-title="(C). Ocean"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-lime add-tooltip" data-theme="theme-lime" data-type="c" data-title="(C). Lime"></a>
												<a href="#" class="demo-theme demo-theme-purple add-tooltip" data-theme="theme-purple" data-type="c" data-title="(C). Purple"></a>
												<a href="#" class="demo-theme demo-theme-dust add-tooltip" data-theme="theme-dust" data-type="c" data-title="(C). Dust"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-mint add-tooltip" data-theme="theme-mint" data-type="c" data-title="(C). Mint"></a>
												<a href="#" class="demo-theme demo-theme-yellow add-tooltip" data-theme="theme-yellow" data-type="c" data-title="(C). Yellow"></a>
												<a href="#" class="demo-theme demo-theme-well-red add-tooltip" data-theme="theme-well-red" data-type="c" data-title="(C). Well Red"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-coffee add-tooltip" data-theme="theme-coffee" data-type="c" data-title="(C). Coffee"></a>
												<a href="#" class="demo-theme demo-theme-prickly-pear add-tooltip" data-theme="theme-prickly-pear" data-type="c" data-title="(C). Prickly pear"></a>
												<a href="#" class="demo-theme demo-theme-dark add-tooltip" data-theme="theme-dark" data-type="c" data-title="(C). Dark"></a>
											</div>
										</div>
										<div class="demo-theme-btn col-md-3 pad-ver light">
											<p class="text-semibold text-uppercase text-xs text-muted">Barra superior</p>
											<div class="mar-btm">
												<img src="img/color-schemes-d.png" class="img-responsive">
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-gray add-tooltip" data-theme="theme-gray" data-type="d" data-title="(D). Gray"></a>
												<a href="#" class="demo-theme demo-theme-navy add-tooltip" data-theme="theme-navy" data-type="d" data-title="(D). Navy Blue"></a>
												<a href="#" class="demo-theme demo-theme-ocean add-tooltip" data-theme="theme-ocean" data-type="d" data-title="(D). Ocean"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-lime add-tooltip" data-theme="theme-lime" data-type="d" data-title="(D). Lime"></a>
												<a href="#" class="demo-theme demo-theme-purple add-tooltip" data-theme="theme-purple" data-type="d" data-title="(D). Purple"></a>
												<a href="#" class="demo-theme demo-theme-dust add-tooltip" data-theme="theme-dust" data-type="d" data-title="(D). Dust"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-mint add-tooltip" data-theme="theme-mint" data-type="d" data-title="(D). Mint"></a>
												<a href="#" class="demo-theme demo-theme-yellow add-tooltip" data-theme="theme-yellow" data-type="d" data-title="(D). Yellow"></a>
												<a href="#" class="demo-theme demo-theme-well-red add-tooltip" data-theme="theme-well-red" data-type="d" data-title="(D). Well Red"></a>
											</div>
											<div class="demo-justify-theme">
												<a href="#" class="demo-theme demo-theme-coffee add-tooltip" data-theme="theme-coffee" data-type="d" data-title="(D). Coffee"></a>
												<a href="#" class="demo-theme demo-theme-prickly-pear add-tooltip" data-theme="theme-prickly-pear" data-type="d" data-title="(D). Prickly pear"></a>
												<a href="#" class="demo-theme demo-theme-dark add-tooltip" data-theme="theme-dark" data-type="d" data-title="(D). Dark"></a>
											</div>
										</div>
									</div>
		
								</div>
							</div>
						</div>
					</div>


					{{csrf_field()}}
					<div class="row mt-2">
						<div class="col-md-offset-11 col-md-12">
							@if(checkPermissions(['Clientes'],["C"]) || checkPermissions(['Clientes'],["W"]))<button type="submit" class="btn btn-primary">Guardar</button>@endif
						</div>
					</div>
					
					</form>

				</div>
			</div>
		</div>
	</div>
	<script>
		$('.form-ajax').submit(form_ajax_submit);

		$('.select2').select2();

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
	
	</script>
@include('layouts.scripts_panel')