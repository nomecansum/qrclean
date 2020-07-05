@extends('layouts.app')

@extends('layout')

@section('styles')
<style>
    #qr {
        width: 640px;
        border: 1px solid silver
    }
    @media(max-width: 600px) {
        #qr {
            width: 300px;
            border: 1px solid silver
        }
    }
    button:disabled,
    button[disabled]{
      opacity: 0.5;
    }
    .scan-type-region {
        display: block;
        border: 1px solid silver;
        padding: 10px;
        margin: 5px;
        border-radius: 5px;
    }
    .scan-type-region.disabled {
        opacity: 0.5;
    }
    .empty {
        display: block;
        width: 100%;
        height: 20px;
    }
    #qr .placeholder {
        padding: 50px;
    }
    </style>
@endsection

@section('title')
{{--  <h1 class="page-header text-overflow pad-no">Helper Classes</h1>  --}}
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{url('/')}}"><i class="demo-pli-home"></i> Home</a></li>
    {{--  <li class="active">Helper Classes</li>  --}}
</ol>
@endsection

@section('content')
    <div id="page-head">
        <div class="w-100 text-center">
            <img src="{{ url('/img/Mosaic_brand_white.png') }}" style="height: 100px">
        </div>
       
        <div class="pad-all text-center text-primary mt-3">
            <div class="text-primary text-3x font-bold">Bienvenido de nuevo {{ Auth::user()->name }}</div>
            <p1>Scroll down to see quick links and overviews of your Server, To do list, Order status or get some Help using Nifty.<p></p>
        </p1></div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-purple panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="fad fa-building fa-2x"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">{{ $edificios->count() }}</p>
                    <p class="mar-no">Edificios</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-info panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="fad fa-layer-group fa-2x"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">{{ $plantas->count() }}</p>
                    <p class="mar-no">Plantas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-mint panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="fad fa-desktop-alt fa-2x"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">{{ $puestos->count() }}</p>
                    <p class="mar-no">Puestos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-{{ color_porcentaje($pct_completado) }} panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="fad fa-check"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">{{ round($pct_completado) }}%   </p>
                    <p class="mar-no">Operativos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Estado {!! beauty_fecha(Carbon\Carbon::now()->Settimezone(Auth::user()->val_timezone)) !!}</h3>
        </div>
        <div class="panel-body">
            <div id="chartdiv" style="width:100%; height:300px;  ml-0"></div>

        </div>
    </div>

@endsection

@php
   
@endphp

@section('scripts')
    {{--  AMCharts  --}}
    <script src="{{url('plugins')}}/amcharts4/core.js"></script>
    <script src="{{url('plugins')}}/amcharts4/charts.js"></script>
    <script src="{{url('plugins')}}/amcharts4/themes/material.js"></script>
    <script src="{{url('plugins')}}/amcharts4/themes/animated.js"></script>
    <script src="{{url('plugins')}}/amcharts4/themes/kelly.js"></script>
    <script src="{{url('plugins')}}/amcharts4/lang/es_ES.js"></script>

    <script src="/js/demo/nifty-demo.min.js"></script>

<script>
    am4core.ready(function() {
    // Themes begin
    am4core.useTheme(am4themes_animated);
    //am4core.useTheme(am4themes_kelly);

    // Themes end
    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.PieChart);

    chart.legend = new am4charts.Legend();
    
    // Add data
    chart.data = {!! json_encode($datos_quesito) !!};
    
    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "cuenta";
    pieSeries.dataFields.category = "des_estado";
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = 2;
    pieSeries.slices.template.strokeOpacity = 1;
    
    // This creates initial animation
    pieSeries.hiddenState.properties.opacity = 1;
    pieSeries.hiddenState.properties.endAngle = -90;
    pieSeries.hiddenState.properties.startAngle = -90;
    
    }); // end am4core.ready()
    </script>


@endsection
