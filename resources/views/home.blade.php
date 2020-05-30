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
            <div class="panel panel-warning panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="demo-pli-file-word icon-3x"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">241</p>
                    <p class="mar-no">Documents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-info panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="demo-pli-file-zip icon-3x"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">241</p>
                    <p class="mar-no">Zip Files</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-mint panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="demo-pli-camera-2 icon-3x"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">241</p>
                    <p class="mar-no">Photos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-danger panel-colorful media middle pad-all">
                <div class="media-left">
                    <div class="pad-hor">
                        <i class="demo-pli-video icon-3x"></i>
                    </div>
                </div>
                <div class="media-body">
                    <p class="text-2x mar-no text-semibold">241</p>
                    <p class="mar-no">Videos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
    </div>

    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Donut Chart</h3>
        </div>
        <div class="panel-body">
            <div id="demo-flot-donut" style="height: 250px; padding: 0px; position: relative;"><canvas class="flot-base" width="476" height="250" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 476px; height: 250px;"></canvas><canvas class="flot-overlay" width="476" height="250" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 476px; height: 250px;"></canvas><div class="legend"><div style="position: absolute; width: 52px; height: 68px; top: 5px; right: 5px; background-color: rgb(255, 255, 255); opacity: 0.85;"> </div><table style="position:absolute;top:5px;right:5px;;font-size:smaller;color:#545454"><tbody><tr><td class="legendColorBox"><div style="border:1px solid #ccc;padding:1px"><div style="width:4px;height:0;border:5px solid rgb(237,194,64);overflow:hidden"></div></div></td><td class="legendLabel">Series1</td></tr><tr><td class="legendColorBox"><div style="border:1px solid #ccc;padding:1px"><div style="width:4px;height:0;border:5px solid rgb(175,216,248);overflow:hidden"></div></div></td><td class="legendLabel">Series2</td></tr><tr><td class="legendColorBox"><div style="border:1px solid #ccc;padding:1px"><div style="width:4px;height:0;border:5px solid rgb(203,75,75);overflow:hidden"></div></div></td><td class="legendLabel">Series3</td></tr><tr><td class="legendColorBox"><div style="border:1px solid #ccc;padding:1px"><div style="width:4px;height:0;border:5px solid rgb(77,167,77);overflow:hidden"></div></div></td><td class="legendLabel">Series4</td></tr></tbody></table></div></div>
        </div>
    </div>

@endsection



@section('scripts')
    <script src="/js/demo/nifty-demo.min.js"></script>
@endsection
