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

    {{-- <div class="row">
        <div class="col-sm-3 col-lg-3">
            <!--Sparkline Area Chart-->
            <div class="panel panel-success panel-colorful">
                <div class="pad-all">
                    <p class="text-lg text-semibold"><i class="demo-pli-data-storage icon-fw"></i> HDD Usage</p>
                    <p class="mar-no">
                        <span class="pull-right text-bold">132Gb</span> Free Space
                    </p>
                    <p class="mar-no">
                        <span class="pull-right text-bold">1,45Gb</span> Used space
                    </p>
                </div>
                <div class="pad-top text-center">
                    <!--Placeholder-->
                    <div id="demo-sparkline-area" class="sparklines-full-content"><canvas width="213" height="60" style="display: inline-block; width: 213.328px; height: 60px; vertical-align: top;"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-lg-3">

            <!--Sparkline Line Chart-->
            <div class="panel panel-info panel-colorful">
                <div class="pad-all">
                    <p class="text-lg text-semibold">Earning</p>
                    <p class="mar-no">
                        <span class="pull-right text-bold">$764</span> Today
                    </p>
                    <p class="mar-no">
                        <span class="pull-right text-bold">$1,332</span> Last 7 Day
                    </p>
                </div>
                <div class="pad-top text-center">

                    <!--Placeholder-->
                    <div id="demo-sparkline-line" class="sparklines-full-content"><canvas width="213" height="60" style="display: inline-block; width: 213.328px; height: 60px; vertical-align: top;"></canvas></div>

                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-6">
            <div class="panel">
                <div class="panel-body text-center clearfix">
                    <div class="col-sm-4 pad-top">
                        <div class="text-lg">
                            <p class="text-5x text-thin text-main">95</p>
                        </div>
                        <p class="text-sm text-bold text-uppercase">New Friends</p>
                    </div>
                    <div class="col-sm-8">
                        <button class="btn btn-pink mar-ver">View Details</button>
                        <p class="text-xs">Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                        <ul class="list-unstyled text-center bord-top pad-top mar-no row">
                            <li class="col-xs-4">
                                <span class="text-lg text-semibold text-main">1,345</span>
                                <p class="text-sm text-muted mar-no">Following</p>
                            </li>
                            <li class="col-xs-4">
                                <span class="text-lg text-semibold text-main">23K</span>
                                <p class="text-sm text-muted mar-no">Followers</p>
                            </li>
                            <li class="col-xs-4">
                                <span class="text-lg text-semibold text-main">278</span>
                                <p class="text-sm text-muted mar-no">Post</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

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
