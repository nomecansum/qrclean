@extends('layout')
@section('content')
<div class="container-fluid">
	<div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
        	@isset ($c)
            <h3 class="text-themecolor mb-0 mt-0">{{trans('strings.edit_client')}}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{trans('strings.home')}}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('business') }}">{{trans('strings.business')}}</a></li>
                <li class="breadcrumb-item active">{{trans('strings.edit_client')}}</li>
            </ol>
            @else
            <h3 class="text-themecolor mb-0 mt-0">{{trans('strings.create_client')}}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{trans('strings.home')}}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('business') }}">{{trans('strings.business')}}</a></li>
                <li class="breadcrumb-item active">{{trans('strings.create_client')}}</li>
            </ol>
            @endisset
        </div>
        <div class="col-md-6 col-4 align-self-center">
            <a href="{{url('business')}}" class="btn float-right hidden-sm-down btn-warning"><i class="mdi mdi-chevron-double-left"></i> {{trans('strings.back')}}</a>
        </div>
    </div>
	<div class="row">
		<div class="col-12">
			<div class="card">
			    <div class="card-body">
					@isset ($c)
						<form action="{{url('business/update')}}" method="POST" class="form-ajax" enctype='multipart/form-data'>
						<h4 class="card-title">{{trans('strings.edit_client')}}</h4>
						<input type="hidden" name="id" value="{{$c->cod_cliente}}">
					@else
						<form action="{{url('business/save')}}" method="POST" class="form-ajax" enctype='multipart/form-data'>
						<h4 class="card-title">{{trans('strings.create_client')}}</h4>
					@endif
				        
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label for="">{{trans('strings._configuration.business.logo')}}</label> <br>
								<label for="img_logo" class="preview preview1" style="background-image: url()">
									<img src="{{ isset($c) && !empty($c->img_logo) ? url('uploads/customers/images',$c->img_logo) : url('/imgs/logo_def.png')}}" style="height: 100%; margin: auto; display: block;" alt="">
								</label>
								<div class="custom-file">
									<input type="file" class="custom-file-input imgInp" accept=".jpg,.png,.gif" name="img_logo" id="img_logo" data-preview=".preview"  value="{{  isset($c) && !empty($c->img_logo) ? $c->img_logo : '' }}">
									<label class="custom-file-label" for="inputGroupFile01">@if(isset($c) && !empty($c->img_logo)) {{$c->img_logo}} @else Elegir logo empresa @endif</label>
								</div>
							</div>
							<input type="hidden" name="old_img_logo" value="{{  isset($c) && !empty($c->img_logo) ? $c->img_logo : '' }}">
						</div>
						<div class="col-sm-9">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="">{{trans('strings._configuration.business.name')}}</label>
										<input type="text" name="nom_cliente" class="form-control" required value="{{isset($c) ? $c->nom_cliente : ''}}">
									</div>
								</div>
								
								<div class="col-sm-4">
									<div class="form-group">
										<label for="">{{trans('strings._configuration.business.cod_sistema')}}</label>
										<input type="number" min="10001" readonly="true" max="99999" name="cod_sistema" class="form-control" @if(isset($c))required @endif  value="{{isset($c) ? @\DB::table('cug_sistema')->where('cod_cliente',$c->cod_cliente)->first()->COD_SISTEMA : $cod_sistema}}">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="">{{trans('strings._configuration.business.information')}}</label>
										<input type="text" name="nom_contacto" class="form-control" value="{{isset($c) ? $c->nom_contacto : ''}}">
									</div>
								</div>
								
								<div class="col-md-12">
									<label class="control-label mt-3">Token registro</label>
									<div class="input-group mb-3">
										<input type="text" name="token_1uso" readonly=""  id="token_1uso"  class="form-control" value="{{isset($c) ? $c->token_1uso : ''}}">
										<div class="input-group-append">
											@if(checkPermissions(['Empresas'],["C"]) || checkPermissions(['Empresas'],["W"]))<button class="btn btn-info" type="button"  id="btn_generar_token">Generar</button>@endif
										</div>
									</div>
								</div>
								
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">{{trans('strings._configuration.business.max_employees')}}</label>
										<input type="number" name="num_max_empleados" class="form-control" required value="{{isset($c) ? $c->num_max_empleados : ''}}" @if(!fullAccess()) readonly @endif>
									</div>
								</div>
								<div class="col-sm-4" style="vertical-align:middle">
									<div class="custom-control custom-switch" style="margin-top: 35px; margin-right: 40px">
										<input type="checkbox" class="custom-control-input" id="other" value="S" name="mca_appmovil" onclick="if($(this).prop('checked')==true){$('#grp_apikey').show();} else {$('#grp_apikey').hide();}"  id="mca_appmovil" {{ isset($c->mca_appmovil) && $c->mca_appmovil == "S" ? 'checked' : ''}} @if(!fullAccess()) readonly @endif>
										<label class="custom-control-label" for="other">{{trans('strings._employees.act_appmovil')}}</label>
									</div>
								</div>
								<div class="col-sm-4">
                                    <label for="">{{trans('strings._configuration.business.suprabusiness')}}</label>
                                    <select name="cod_supracliente" class="form-control" id="cod_supracliente">
                                        <option value="" selected></option>
                                        @foreach (\DB::table('cug_clientes')->whereNull('cug_clientes.fec_borrado')->get() as $cl)
                                            <option {{isset($c) ? ($c->cod_supracliente == $cl->cod_cliente ? 'selected' : '') : ''}} value="{{$cl->cod_cliente}}">{{$cl->nom_cliente}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-sm-2">
                                    <label for="">Tipo de empresa</label>
                                    <select name="cod_tipo_cliente" class="form-control" id="cod_tipo_cliente">
                                        @foreach (\DB::table('cug_tipos_cliente')->get() as $cl)
                                            <option {{isset($c) ? ($c->cod_tipo_cliente == $cl->cod_tipo_cliente ? 'selected' : '') : ''}} value="{{$cl->cod_tipo_cliente}}">{{$cl->des_tipo_cliente}}</option>
                                        @endforeach
                                    </select>
                                </div>
								
								<div class="col-md-12" id="grp_apikey" style="{{ isset($c->mca_appmovil) && $c->mca_appmovil != "N" ? 'display: block;' : 'display: none;'}}">
                                    <label class="control-label mt-3">Key Appmovil</label>
                                    <div class="input-group mb-3">
                                        <input type="text" name="val_apikey"  id="val_apikey"  class="form-control" value="{{isset($c) ? $c->val_apikey : ''}}">
                                        <div class="input-group-append">
                                            @if(checkPermissions(['Empresas'],["C"]) || checkPermissions(['Empresas'],["W"]))<button class="btn btn-info" type="button" id="btn_generar_key">Generar</button>@endif
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
			    	{{csrf_field()}}

					@if(checkPermissions(['Empresas'],["C"]) || checkPermissions(['Empresas'],["W"]))<button type="submit" class="btn btn-primary">{{trans('strings.submit')}}</button>@endif
			    	</form>
			    </div>
			</div>
		</div>
	</div>
@stop

@section('scripts')
	<script>

		@if(checkPermissions(['Empresas'],["W"]))
		$('#btn_generar_key').click(function(event){
			$.get( "/business/gen_key")
			.done(function( data, textStatus, jqXHR ) {
				$('#val_apikey').val(data);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
					console.log(errorThrown);
			});	
		})

		$('#btn_generar_token').click(function(event){
			$.get( "/business/gen_key")
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
	
		$( "#token_1uso" ).dblclick(function() {
			window.open('{{ url('/checkin') }}/'+$(this).val(), '_blank', '');
		});

	</script>
@endsection