@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Acceso a puesto</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')

@endsection

@section('content')
@php
    $puesto=$respuesta['puesto']??null;
    //dump($respuesta);
@endphp

    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            @if(isset($puesto))
            <img src="{{ !empty($puesto->img_logo) && file_exists(public_path().'/img/clientes/images/'.$puesto->img_logo) ? url('/img/clientes/images/'.$puesto->img_logo) : url('/img/logo.png') }}" style="width: 120px"><br>
            <h2>{{ $puesto->nom_cliente }}</h2>
            @endif
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <div class="pad-all text-center font-bold" style="color: {{ $puesto->val_color }}; font-size: 22px">
                <i class="{{ $puesto->val_icono }}"></i>[{{ $puesto->cod_puesto }}] - {{ $puesto->des_puesto }}
            </div>
        </div>
    </div>
    <div class="row" id="div_respuesta">
        <div class="col-md-3"></div>
        <div class="col-md-6 text-3x text-center bg-{{$respuesta['color']}} rounded">
            {!!$respuesta['icono']!!} {{$respuesta['mensaje']}}
        </div>
        <div class="col-md-3"></div>
    </div>
    <div class="row" id="div_mensaje_fin" style="display:none">
        <div class="col-md-3"></div>
        <div class="col-md-6 text-3x text-center rounded" id="div_txt_mensaje">
            
        </div>
        <div class="col-md-3"></div>
    </div>
    @if(isset($puesto))
        <div id="div_botones">
            @if(!$reserva)
                @if($puesto->id_estado<3)
                    <div class="row mt-5 mb-5">
                        <div class="col-md-12 pt-3 pb-3 text-2x text-center">
                            ¿Que quiere hacer?
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12 text-center">
                        @switch($puesto->id_estado)
                            @case(1)
                                    <button class="btn btn-lg btn-success text-bold btn_estado" data-estado="2" data-id="{{$puesto->token}}">Voy a utilizar este puesto</button>
                                @break
                            @case(2)
                                    @if($puesto->id_usuario_usando==Auth::user()->id)
                                        @if($config_cliente->mca_limpieza=='S')
                                            <button class="btn btn-lg btn-purple btn_estado" data-estado="3"  data-id="{{$puesto->token}}">Voy a dejar este puesto</button>
                                        @else
                                            <button class="btn btn-lg btn-purple btn_estado" data-estado="1"  data-id="{{$puesto->token}}">Voy a dejar este puesto</button>
                                        @endif
                                    @endif
                                @break
                            @default
                        @endswitch
                    </div>
                </div>
                @if($puesto->id_estado!=6)
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-lg btn-warning text-bold btn_incidencia" data-estado="6" data-id="{{$puesto->token}}"><i class="fad fa-exclamation-triangle"></i> Notificar una incidencia en este puesto</button>
                        </div>
                    </div>
                @endif
            @endif
            @if(($puesto->id_estado>1 && isset($respuesta['disponibles'])) || $reserva)
                <div class="row">
                    <div class="col-md-12 font-18 text-center mt-5">
                        En esta misma planta tiene los siguientes puestos disponibles:
                    </div>
                </div>
                <div class="row">
                    @foreach($respuesta['disponibles'] as $disp)
                        <div class="col-md-4 pad-all font-18 text-center font-bold" style="color: {{ $disp->val_color }}">
                            <i class="{{ $disp->val_icono }}"></i>[{{ $disp->cod_puesto }}] - {{ $disp->des_puesto }}
                        </div>
                    @endforeach
                    </div>
                </div>
                @if(Auth::check())
                    <div class="row">
                        <div class="col-md-12 text-center mt-3">
                            <a class="btn btn-lg btn-primary text-2x rounded btn_otravez" href="{{ url('/scan_usuario/') }} "><i class="fad fa-qrcode fa-3x"></i> Escanear otra vez</a>
                        </div>
                    </div>
                @endif

            @endif
        </div>
    @endif

@endsection


@section('scripts')
    <script>
        $('.btn_estado').click(function(){
            $.get("{{url('/puesto/estado/')}}/"+$(this).data('id')+"/"+$(this).data('estado'), function(data){
                $('#div_botones').hide();
                $('#div_respuesta').hide();
                $('#div_mensaje_fin').show();

                animateCSS('#div_mensaje_fin','bounceInright');
                if(data.tipo=='OK'){
                    $('#div_txt_mensaje').addClass('bg-info');
                    $('#div_txt_mensaje').removeClass('bg-danger');
                    $('#div_txt_mensaje').html('<i class="fad fa-check-circle"></i> '+data.mensaje);
                } else {
                    $('#div_txt_mensaje').removeClass('bg-info');
                    $('#div_txt_mensaje').addClass('bg-danger');
                    $('#div_txt_mensaje').html('<i class="fad fa-exclamation-square"></i> '+data.mensaje);
                }
                console.log(data);
            })
        })

        $('.btn_incidencia').click(function(){
            window.location.replace("{{url('/incidencias/create')}}/"+$(this).data('id'));
        })

    </script>
@endsection
