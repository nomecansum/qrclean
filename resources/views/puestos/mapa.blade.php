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
                                
                                if(isMobile()){
                                    $puesto->factor_puesto=$puesto->factor_puesto*4;
                                    $puesto->factor_letra=$puesto->factor_letra*4;
                                }
                                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);
                               
                            @endphp
                            <div class="text-center rounded add-tooltip flpuesto draggable {{ $cuadradito['clase_disp'] }} p-0 mr-2 mb-2 bg-{{ $puesto->color_estado }}" id="puesto{{ $puesto->id_puesto }}" title="{!! $puesto->des_puesto." \r\n ".$cuadradito['title'] !!}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $value }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw; color: {{ $cuadradito['font_color'] }}; {{ $cuadradito['borde'] }}">
                                <span class="h-100 mb-0 mt-0" style="font-size: {{ $puesto->factor_letra }}vw; color: {{ $cuadradito['font_color'] }}">{{ $puesto->cod_puesto }}</span>
                                @include('resources.adornos_iconos_puesto')
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
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        $('.parametrizacion').addClass('active active-sub');
        $('.mapa').addClass('active-link');
    </script>
@endsection
