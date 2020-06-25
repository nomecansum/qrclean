<div class="panel" id="editor">
	@php
	
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
						
						<div class="col-sm-3 text-center mt-3">
							<img src="{{ isset($c) ? url('/img/clientes/images/',$c->img_logo) : ''}}" style="width: 100%" alt="" class="img-fluid ml-0">
							<div class="form-group">
								<label>Imagen</label><br>
								<div class="custom-file">
									<input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_logo" id="img_logo" lang="es">
									<label class="custom-file-label" for="img_logo"></label>
								</div>
							</div>
							
							
							
						</div>
						<div class="col-sm-9">
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
											@if(checkPermissions(['Clientes'],["C"]) || checkPermissions(['Empresas'],["W"]))<button class="btn btn-mint" type="button"  id="btn_generar_token">Generar</button>@endif
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="">Informacion de contacto</label>
										<input type="text" name="nom_contacto" class="form-control" value="{{isset($c) ? $c->nom_contacto : ''}}">
									</div>
								</div>
							</div>
							
						</div>
					</div>
					{{csrf_field()}}

					@if(checkPermissions(['Clientes'],["C"]) || checkPermissions(['Clientes'],["W"]))<button type="submit" class="btn btn-primary">Guardar</button>@endif
					</form>

				</div>
			</div>
		</div>
	</div>
	<script>
		$('.form-ajax').submit(form_ajax_submit);
		
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
	
	</script>
