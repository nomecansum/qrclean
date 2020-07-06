@php
 //Datos de KPI
    use App\Models\puestos;
    use App\Models\edificios;
    use App\Models\plantas;
    $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            }
        })
        ->get();
    
    $datos_quesito=DB::table('estados_puestos')
        ->join('puestos','puestos.id_estado','estados_puestos.id_estado')
        ->selectraw('des_estado, count(cod_puesto) as cuenta')
        ->groupby('des_estado')
        ->get();

    $puestos_si=puestos::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        }
    })
    ->where('id_estado',1);
    
    $edificios=edificios::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        }
    });
    
    $plantas=plantas::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        }
    });

    

    try{
        $pct_completado=(100*$puestos_si->count()/$puestos->count());
    } catch(\Exception $e){
        $pct_completado=0;
    }

    //Datos de donut chart   

@endphp

@include('home.accesos_directos')

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

@section('scripts3')
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

@include('home.calendario')