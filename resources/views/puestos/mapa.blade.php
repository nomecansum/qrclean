@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mapa de puestos</h1>
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
                            <span class="mr-2"><i class="fad fa-user"></i> {{ $e->puestos }}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @php
                    $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
                @endphp
                @foreach($plantas as $key=>$value)
                    <h3 class="pad-all w-100 bg-gray rounded">PLANTA {{ $value }}</h3>
                    @php
                        $puestos_planta=$puestos->where('id_planta',$key);
                    @endphp
                    <div class="d-flex flex-wrap">
                        @foreach($puestos_planta as $p)
                            <div class="text-center font-bold rounded bg-{{ $p->val_color }} mr-2 mb-2 align-middle" style="width:100px; height: 100px; overflow: hidden;">
                                <span class="h-100 align-middle">{{ $p->cod_puesto }}</span>
                            </div>
                            
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach


@endsection


@section('scripts')

@endsection
