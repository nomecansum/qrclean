@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de cámaras</h1>
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
                                <button class="btn btn-lg btn-warning btn_estado" data-estado="3"  data-id="{{$puesto->token}}">Voy a dejar este puesto</button>
                            @break
                        @default
                    @endswitch
                </div>
            </div>
            @if($puesto->id_estado>1 && isset($respuesta['disponibles']))
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

    </script>
@endsection