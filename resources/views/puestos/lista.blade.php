@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mapa de puestos (lista)</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <style type="text-css">

    </style>
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
        <div class="row botones_accion mb-2">
            <div class="col-md-4">
                <form action="{{ url('puestos/lista') }}" name="form_mapa" id="form_mapa" method="POST">
                    {{ csrf_field() }}
                    <div class="input-group float-right" id="div_fechas">
                        <input type="text" class="form-control pull-left" id="fecha" name="fecha" style="width: 100px" value="{{isset($r->fecha)?$r->fecha:Carbon\Carbon::now()->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
                
            </div>
            <div class="col-md-2 text-right">
                <a href="#modal-leyenda" data-toggle="modal" data-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
            </div>
            <div class="col-md-3 text-right">
                <a href="{{ url('puestos/lista') }}" class="mr-2" style="color: #1e1ed3; font-weight: bold"><i class="fad fa-list"></i> Lista</a>
                <a href="{{ url('puestos/mapa') }}" class="mr-2  text-white" ><i class="fad fa-th"></i> Mosaico</a>
                <a href="{{ url('puestos/plano') }}" class="mr-2  text-white"><i class="fad fa-map-marked-alt"></i> Plano</a>
            </div>
        </div>
        @include('puestos.content_lista')
@endsection


@section('scripts')
    <script>
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        $('.parametrizacion').addClass('active active-sub');
        $('.mapa').addClass('active-link');
        $('.adorno_puesto').css('margin-top','0px');
        $('.adorno_puesto').css('line-height','20px');
        $('.adorno_puesto').css('vertical-align','absmiddle');

        $('#fecha').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput : true,
            //autoApply: true,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            }
        });
        $('#fecha').change(function(){
               $('#form_mapa').submit();
        });
    </script>

@endsection
