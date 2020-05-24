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
					<div class="card">
						<div class="card-body">
							@if($c->id_cliente!=0)
								<form action="{{url('/clientes/update')}}" method="POST" class="form-ajax" enctype='multipart/form-data'>
								<input type="hidden" name="id" value="{{$c->id_cliente}}">
							@else
								<form action="{{url('/clientes/save')}}" method="POST" class="form-ajax" enctype='multipart/form-data'>
							@endif
								
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label for="">Logo</label> <br>
										<label for="img_logo" class="preview preview1" style="background-image: url()">
											<img src="{{ isset($c) && !empty($c->img_logo) ? url('/img/clientes/images/',$c->img_logo) : url('/img/logo_def.png')}}" style="height: 100%; margin: auto; display: block;" alt="">
										</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input imgInp" accept=".jpg,.png,.gif" name="img_logo" id="img_logo" data-preview=".preview"  value="{{  isset($c) && !empty($c->img_logo) ? $c->img_logo : '' }}">
											<label class="custom-file-label" for="inputGroupFile01">@if(isset($c) && !empty($c->img_logo)) {{$c->img_logo}} @else Elegir logo Cliente @endif</label>
										</div>
									</div>
									<input type="hidden" name="old_img_logo" value="{{  isset($c) && !empty($c->img_logo) ? $c->img_logo : '' }}">
								</div>
								<div class="col-sm-9">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="">Nombre</label>
												<input type="text" name="nom_cliente" class="form-control" required value="{{isset($c) ? $c->nom_cliente : ''}}">
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
		</div>
	</div>
	<script>


	</script>
