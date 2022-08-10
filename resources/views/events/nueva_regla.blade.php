@extends('layouts.app')
@section('styles')

 {{--  Colorpicker  --}}
 <link href="{{url('/plugins/jquery-minicolors-master/jquery.minicolors.css')}}" rel="stylesheet" media="all">
 <style type="text-css">
	.select2-container .select2-search__field {
		width: 100% !important;
	}

 </style>
@endsection
@section('content')
@php
	Carbon\Carbon::setLocale(session('lang'));
    setlocale(LC_TIME, 'Spanish');
    if(!isset($cod_regla)){
        $cod_regla=0;
    }

    if(count(session('clientes'))<100 && Auth::user()->mca_acceso_todos_clientes!=1){
        $clientes=DB::table('usuarios_clientes')
            ->join('clientes','clientes.id_cliente','usuarios_clientes.id_cliente')
            ->where('id_usuario',Auth::user()->id_usuario)
            ->get();
    }
    else if(isset($reglas)&&strlen($reglas->clientes)>0){
        $clientes=DB::table('usuarios_clientes')
            ->join('clientes','clientes.id_cliente','usuarios_clientes.id_cliente')
            ->where('id_usuario',Auth::user()->id_usuario)
            ->wherein('clientes.id_cliente',explode(",",$reglas->clientes))
            ->get();
        $clientes=collect($clientes);
    }
    else {
        $clientes=[];
        $clientes=collect($clientes);
    }
@endphp

<div class="container-fluid">
	<div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
        	<h3 class="text-themecolor mb-0 mt-0">{{ __('eventos.nueva_regla_evento') }}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{trans('general.home')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{  url('/events')}}">{{ __('general.eventos') }}</a></li>
                <li class="breadcrumb-item active">{{ __('eventos.nueva_regla_evento') }}</li>
            </ol>
        </div>
        <div class="col-md-6 col-4 align-self-center">
            {{-- <a href="{{url('eventos')}}" class="btn float-right hidden-sm-down btn-warning"><i class="mdi mdi-chevron-double-left"></i> {{trans('strings.back')}}</a> --}}
        </div>
	</div>
    <form action="{{url(config('app.carpeta_asset').'/save')}}" method="POST" class="form-ajax" id="formcomando">
        {{csrf_field()}}
        <input type="hidden" name="cod_regla" value="{{ $cod_regla }}">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        {{-- <h4 class="card-title">Nueva regla</h4> --}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label  for="nom_regla">{{ __('general.nombre') }}</label><br>
                                    <input required type="text" name="nom_regla" id="nom_regla" class="form-control" style="height: 47px" value="{{ isset($reglas->nom_regla)?$reglas->nom_regla:'' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-group"  >
                                        <label>{{ __('general.propietario') }}
                                            @include('resources.spin_puntitos',['id_spin'=>'spin_cli'])
                                        </label>
                                        <select name="cod_propietario"  required  @if(Auth::user()->mca_acceso_todos_clientes==1 || count(session('clientes'))>100)class="form-control" multiple @else class="form-control" @endif  id="cod_propietario" lang="{{ config('app.lang', 'es') }}">
                                            @foreach ($clientes as $c)
                                                <option value="{{$c->id_cliente}}" @if(isset($reglas) && $c->id_cliente=$reglas->cod_cliente) selected @endif>{{$c->nombre_cliente}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('general.comando') }}</label>
                                    <select name="comando" id="comando"class="form-control select2" style="width: 100%" placeholder="{{ __('eventos.seleccione_un_comando') }}">
                                        @php
                                            $files = File::allFiles(resource_path('views/events/comandos'));

                                        @endphp
                                        <option value=""></option>
                                        @foreach ($files as $file)
                                        {{-- {{ $elementos->url_elemento==basename($file) ? 'selected' : '' }} --}}
                                        <option style="text-transform: uppercase" {{ isset($reglas->nom_comando)&&$reglas->nom_comando==basename($file)?'selected':'' }}  value="{{ basename($file) }}">{{ str_replace(".php","",str_replace("_"," ",basename($file))) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8 mt-4 text-muted" id="txt-desc" style="font-weight: 400;">
                                {!! isset($descripcion)?$descripcion:'' !!}
                            </div>
                        </div>
                        <div class="row" id="row_intervalo" style="display: none">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">{{ __('general.intervalo') }} (min)</label><br>
                                    <input required type="number" name="intervalo" id="intervalo" class="form-control" value="{{ isset($reglas->intervalo)?$reglas->intervalo:'' }}" min="2" max="99999">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label  for="cod_grupo">{{ __('general.grupo') }}</label><br>
                                    <select class="form-control"  name="cod_grupo" id="cod_grupo">
                                        <option value=""> </option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="I"?'selected':''}} value="I">{{ __('general.minuto') }}</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="H"?'selected':''}} value="H">{{ __('general.hora') }}</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="D"?'selected':''}} value="D">{{ __('general.dia') }}</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="S"?'selected':''}} value="S">{{ __('general.semana') }}</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="Q"?'selected':''}} value="Q">{{ __('general.quincena') }}</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="A"?'selected':''}} value="A">{{ __('general.grupo') }} A</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="B"?'selected':''}} value="B">{{ __('general.grupo') }} B</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="C"?'selected':''}} value="C">{{ __('general.grupo') }} C</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="D"?'selected':''}} value="D">{{ __('general.grupo') }} D</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="E"?'selected':''}} value="E">{{ __('general.grupo') }} E</option>
                                        <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="F"?'selected':''}} value="F">{{ __('general.grupo') }} F</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-1 pt-2">
                                <div class="form-group">
                                    <div class="form-group d-flex align-items-center justify-content-between pt-4 ml-2">
                                        <label class="mb-0" for="mca_activa">{{ __('general.activa') }}</label>
                                        <input class="input-switch" id="mca_activa" type="checkbox" name="mca_activa" value="S" {{(isset($t) && $t->mca_activa=="S")||(!isset($t))?'checked':'' }}>
                                        <i class="btn-switch switchOff  {{(isset($t) && $t->mca_activa=="S")||(!isset($t))?'switchOn':'' }}"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex flex-row p-3">

                                    <label class="mt-3 mr-1" for="" title="{{ __('eventos.hint_tiempo_espera') }}">{{ __('eventos.intervalo_de_espera') }}</label>
                                    @php
                                       
                                    @endphp
                                    <input required type="number" name="int_espera" id="int_espera" class="form-control col-4" value="{{ $reglas->nomolestar??'' }}" min="0" max="365">
                                    <select class="form-control col-4"  name="tip_espera" id="tip_espera">
                                        <option value="M" {{ isset($reglas->tip_nomolestar) && $reglas->tip_nomolestar=='M'? 'selected':'' }}>{{ __('general.minutos') }}</option>
                                        <option value="H" {{ isset($reglas->tip_nomolestar) && $reglas->tip_nomolestar=='H'? 'selected':'' }}>{{ __('general.horas') }}</option>
                                        <option value="D" {{ isset($reglas->tip_nomolestar) && $reglas->tip_nomolestar=='D'? 'selected':'' }}>{{ __('general.dias') }}</option>
                                    </select>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="div_regla" style="display:none">
            <div class="col-md-12">
                <div class="card totales_resultados b-all" >
                    <h4 class="mt-2 ml-2" >{{ __('eventos.parametrizacion_del_comando') }}</h4>
                    <div class="card-body" id="param_regla">

                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="div_prog" style="display:none">
            <div class="col-md-12">
                <div class="card" >
                    <h4 class="mt-2 ml-2" >{{ __('eventos.programacion_de_la_regla') }}</h4>
                    <div class="card-body" id="prog_regla">

                    </div>
                </div>
            </div>
        </div>
    </form>
    <form action="{{url(config('app.carpeta_asset').'/acciones/param_acciones/save')}}" method="POST" class="form-ajax" id="formaccion">
        {{csrf_field()}}
        <div class="row" id="div_acc" style="display:none">
            <div class="col-md-12">
                <div class="card" >
                    <div class="card-header bg-white">
                        <b>Acciones</b>
                            <div class="dropdown" >

                                <a href="javascript:void(0)" data-toggle="dropdown" class="btn float-right hidden-sm-down btn-success dropdown-toggle mr-3 pr-3"><i class="fas fa-plus-circle"></i> {{ ucfirst(__('general.addadir')) }}</a>

                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item nueva_accion p-1"  id="nueva_iteracion" data-tipo="iteracion" href="javascript:void(0)"><i class="fad fa-recycle" style="width:20px"></i> {{ __('eventos.iteracion') }}</a>
                                    <a class="dropdown-item nueva_accion p-1" id="nueva_accion" data-tipo="accion" href="javascript:void(0)"><i class="fad fa-shoe-prints" style="width:20px"></i> {{ __('general.accion') }}</a>
                                </div>

                            </div>
                    </div>
                    <div class="card-body" id="acciones_regla">

                    </div>
                </div>
            </div>
        </div>
    </form>

</div>




@endsection
@section('scripts')
{{--  Colorpicker  --}}
<script src="{{url('/plugins/jquery-minicolors-master/jquery.minicolors.min.js')}}"></script>
<script>
     $('.colorpicker').minicolors({
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
    
    var iteracion_seleccionada=1;
    //Esto cargara los form si se esta editando la regla
    @if($cod_regla!=0)
        $.post('{{url(config('app.carpeta_asset')."/param_comando/".$cod_regla)}}', {_token: '{{csrf_token()}}', 'comando': '{{ $reglas->nom_comando }}', 'cod_regla': '{{ $reglas->cod_regla }}'}, function(data, textStatus, xhr) {
        })
        .done(function(data){
            $('#param_regla').html(data);
            $('#div_regla').show();
            $('#row_intervalo').show();
            animateCSS('#div_regla','bounceInRight');
            // //Cargar el calendario
            $('#prog_regla').load("{{ url(config('app.carpeta_asset').'/calendario/'.$cod_regla) }}");
            $('#div_prog').show();
            animateCSS('#div_prog','bounceInLeft');
            //Cargar las acciones
            $('#acciones_regla').load("{{ url(config('app.carpeta_asset').'/acciones/'.$cod_regla) }}");
            $('#div_acc').show();
            animateCSS('#div_acc','bounceInUp');
        });

    @endif;


    $('.nueva_accion').click(function(){
        tipo_accion=$(this).data('tipo');
        $.ajax({
                url: "{{ url(config('app.carpeta_asset').'/acciones/nueva/'.$cod_regla) }}/"+$(this).data('tipo')
            })
            .fail(function(err) {
                let error = JSON.parse(err.responseText);
                let html = "";
                console.log(error);
                toast_error(error.titulo,error.error);
            })
            .done(function(data) {
                console.log(data);
                toast_ok("{{ __('general.addadir') }} "+tipo_accion,tipo_accion+" {{ __('general.addadida') }}");
                $('#acciones_regla').html(data);
                $('.form_param').empty();

            });
                    //$('#acciones_regla').load("{{ url('/events/acciones/nueva/'.$cod_regla.'/accion') }}");
    });

    // $('#nueva_iteracion').click(function(){
    //     $.get('#acciones_regla').load("{{ url('/events/acciones/nueva/'.$cod_regla.'/iteracion') }}")
    // });

    $("#comando").select2({
        placeholder: "{{ __('eventos.seleccione_un_comando') }}",
        allowClear: false
    });
    $('#comando').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        $('#param_regla').html('');
        $.post('{{url(config('app.carpeta_asset')."/param_comando/".$cod_regla)}}', {_token: '{{csrf_token()}}', 'comando': data.id}, function(data, textStatus, xhr) {

        })
        .done(function(data) {

            $('#param_regla').html(data);
            $('#div_regla').show();
            $('#row_intervalo').show();
            animateCSS('#div_regla','bounceInRight');
            //Cargar el calendario
            $('#prog_regla').load("{{ url(config('app.carpeta_asset').'/calendario/'.$cod_regla) }}");
            $('#div_prog').show();
            animateCSS('#div_prog','bounceInLeft');

        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            console.log(error);
            toast_error("Error",error.error);
        })
    });

    @if(Auth::user()->mca_acceso_todos_clientes==1 || count(session('clientes'))>100)
        $("#cod_propietario").select2(
            {
                placeholder: "{{ __('general.seleccione_un_cliente') }}",
                dropdownAutoWidth: false,
                width: '100%',
                minimumResultsForSearch: 1,
                maximumSelectionLength: 1,
                minimumInputLength: 3,
                language: "es",
                ajax: { url: "{{ url(config('app.asset_url').'/combos/clientes_search') }}", type: "post", dataType: 'json', delay: 500,data: function (params) {
                $('#spin_cli').show();
                return {
                    searchTerm: params.term,// search term
                    _token:'{{csrf_token()}}'
                    };

                },
                processResults: function (response) {
                    $('#spin_cli').hide();
                    return {
                        results: response 
                    };
                },
                cache: true
                }
            });
    @else
        $("#cod_propietario").select2({
            placeholder: "{{ __('general.seleccione_un_cliente') }}",
            dropdownAutoWidth: false,
            width: '100%',
            minimumResultsForSearch: 1,
            language: "es",
        });
    @endif


</script>
@endsection
