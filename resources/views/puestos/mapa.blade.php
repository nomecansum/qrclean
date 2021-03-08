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
            <div class="col-md-7">
                
            </div>
            <div class="col-md-2 text-right">
                <a href="#modal-leyenda" data-toggle="modal" data-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
            </div>
            <div class="col-md-3 text-right">
                <a href="{{ url('puestos/lista') }}" class="mr-2" ><i class="fad fa-list"></i> Lista</a>
                <a href="{{ url('puestos/mapa') }}" class="mr-2" style="color:#fff"><i class="fad fa-th"></i> Mosaico</a>
                <a href="{{ url('puestos/plano') }}" class="mr-2"><i class="fad fa-map-marked-alt"></i> Plano</a>
            </div>
        </div>
        @include('puestos.content_mapa')
@endsection


@section('scripts')
    <script>
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        $('.parametrizacion').addClass('active active-sub');
        $('.mapa').addClass('active-link');
    </script>
@endsection
