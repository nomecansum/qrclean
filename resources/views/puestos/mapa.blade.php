@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mapa de puestos (mosaico)</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item"><a href="{{url('/puestos')}}">Puestos</a></li>
        <li class="breadcrumb-item active">Mapa de puestos</li>
    </ol>
@endsection

@php
    $edificio_ahora=0;
    $planta_ahora=0;
@endphp

@section('content')
        <div class="row botones_accion">
            <div class="col-md-8">

            </div>
            <div class="col-md-4 text-right">
                <a href="{{ url('puestos/mapa') }}" class="mr-2" style="color:#fff"><i class="fad fa-th"></i> Mosaico</a>
                <a href="{{ url('puestos/plano') }}" class="mr-2"><i class="fad fa-map-marked-alt"></i> Plano</a>
            </div>
        </div>
   
        @foreach ($edificios as $e)
        <div class="panel">
            <div class="panel-heading bg-gray-dark">
                <div class="row">
                    <div class="col-md-3">
                        <span class="text-2x ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}</span>
                    </div>
                    <div class="col-md-7"></div>
                    <div class="col-md-2 text-right">
                        <h4>
                            <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                            <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @php
                    $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
                @endphp
                @foreach($plantas as $key=>$value)
                    <h3 class="pad-all w-100 bg-gray rounded" style="font-size: 2vh">PLANTA {{ $value }}</h3>
                    @php
                        $puestos_planta=$puestos->where('id_planta',$key);
                    @endphp
                    <div class="d-flex flex-wrap">
                        @foreach($puestos_planta as $puesto)
                            @php
                                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $title=$puesto->des_puesto;
                                $borde="";
                                if(isset($reserva)){
                                    $color="LightCoral";
                                    $font_color="#fff";
                                    $clase_disp="";
                                    $title="Reservado por ".$reserva->name." para hoy";
                                } else if(isset($asignado_usuario)){
                                    $color="LightCoral";
                                    $font_color="#fff";
                                    $clase_disp="";
                                    $title="Puesto permanentemente asignado a ".$asignado_usuario->name;
                                    $borde="border: 3px solid #ff9f1a; border-radius: 16px";
                                } else if(isset($asignado_otroperfil)){
                                    $color="#dff9d2";
                                    $font_color="##05688f";
                                    $clase_disp="";
                                    $borde="border: 3px solid #05688f; border-radius: 10px";
                                    $title="Puesto reservado para  ".$asignado_otroperfil->des_nivel_acceso;
                                } else if(isset($asignado_miperfil)){
                                    $color="#dff9d2";
                                    $font_color="##05688f";
                                    $clase_disp="disponible";
                                    $title="Puesto reservado para  ".$asignado_miperfil->des_nivel_acceso;
                                    $borde="border: 3px solid #05688f; border-radius: 10px";
                                }   else {
                                    $color="#dff9d2";
                                    $font_color="#fff";
                                    $clase_disp="disponible";
                                }    
                            @endphp
                            {{-- <div class="text-center font-bold rounded bg-{{ $p->val_color }} mr-2 mb-2 align-middle" style="width:8vw; height: 8vw; overflow: hidden; font-size: 1.6vw;">
                                <span class="h-100 align-middle">{{ $p->cod_puesto }}</span>
                            </div> --}}
                            <div class="text-center font-bold rounded add-tooltip align-middle flpuesto draggable {{ $clase_disp }} mr-2 mb-2 bg-{{ $puesto->val_color }}" id="puesto{{ $puesto->id_puesto }}" title="{{ $title }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $value }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw; color: {{ $font_color }}; {{ $borde }}">
                                <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw;">{{ $puesto->cod_puesto }}</span>
                                @if(isset($reserva))<br>
                                    <span class="font-bold" style="font-size: 18px; color: #ff0">R</span>
                                @endif
                                @if(isset($asignado_usuario))<br>
                                    <span class="font-bold" style="font-size: 18px; color: #f4d35d">{{ iniciales($asignado_usuario->name,3) }}</span>
                                @endif
                                @if(isset($asignado_miperfil))<br>
                                    <span class="font-bold" style="font-size: 18px; color: #05688f"><i class="fad fa-users" style="color: #fff"></i></span>
                                @endif
                                @if(isset($asignado_otroperfil))<br>
                                    <span class="font-bold" style="font-size: 18px;"><i class="fad fa-users" style="color: #fff"></i></span>
                                @endif
                            </div>
                            
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach


@endsection


@section('scripts')
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.mapa').addClass('active-link');
    </script>
@endsection
