<div class="panel">
<div class="panel-heading">
    <div class="input-group mb-3">
        <input type="text" class="form-control pull-left" id="fechas_resul" name="fechas" style="height: 33px; width: 300px" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d/m/Y').' - '.Carbon\Carbon::now()->endOfMonth()->format('d/m/Y') }}">
        <span class="btn input-group-text btn-mint" disabled  style="height: 33px"><i class="fas fa-calendar mt-1"></i></span>
    </div>
</div>
<div class="panel-body">
    <div class="row">
        <div class="col-md-12">
            <h4>Resultados de la encuesta: {{ $encuesta->titulo }}</h4>
        </div>
    </div>
    
    <div id="chartdiv_puestos" style="width:100%; height:300px;  ml-0"></div>
    <div class="row">
        <div class="col-md-12">
            @switch($encuesta->id_tipo_encuesta)
                @case(1)
                    <i class="fas fa-tired  fa-2x text-danger valor" data-value="1"></i> 1
                    <i class="fas fa-frown  fa-2x text-warning valor" data-value="2"></i> 2
                    <i class="fas fa-meh-rolling-eyes  fa-2x text-primary valor" data-value="3"></i> 3
                    <i class="fas fa-smile  fa-2x text-mint valor" data-value="4"></i> 4
                    <i class="fas fa-grin-alt fa-2x text-success valor" data-value="5"></i> 5
                    @break
                @case(2)
                    <i class="fas fa-frown  fa-2x text-danger valor" data-value="1"></i> 1
                    <i class="fas fa-meh  fa-2x text-warning valor" data-value="2"></i> 2
                    <i class="fas fa-smile fa-2x text-success valor" data-value="3"></i> 3
                    @break
                @case(3)
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est1" data-value="1"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est2" data-value="2"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est3" data-value="3"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est4" data-value="4"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est5" data-value="5"></i>
                    @break
            @endswitch
        </div>
    </div>
    <label class="mt-5">Votos por fecha</label>
    <div id="chartdiv" style="width:100%; height:300px;  ml-0"></div>

</div>
</div>


{{--  AMCharts  --}}
<script src="{{url('plugins')}}/amcharts4/core.js"></script>
<script src="{{url('plugins')}}/amcharts4/charts.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/material.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/animated.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/kelly.js"></script>
<script src="{{url('plugins')}}/amcharts4/lang/es_ES.js"></script>

<script src="/js/demo/nifty-demo.min.js"></script>

<script>
    $('#fechas_resul').on('apply.daterangepicker',function(){
        console.log('Refresh '+$('#fechas').val());
        $.post('{{url('/encuestas/resultados')}}', {_token:'{{csrf_token()}}',id_encuesta: {{ $encuesta->id_encuesta }},fechas: $('#fechas_resul').val()}, function(data, textStatus, xhr) {
               $('#body_resultados').html(data);
            });
    })
    
    $('#fechas_resul').daterangepicker({
        autoUpdateInput: true,
        locale: {
            format: '{{trans("general.date_format")}}',
            applyLabel: "OK",
            cancelLabel: "Cancelar",
            daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
            monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
            firstDay: {{trans("general.firstDayofWeek")}}
        },
        opens: 'right',
        parentEl: "#modal-resultados .body_resultados" 
    });

    am4core.ready(function() {
        // Themes begin
        am4core.useTheme(am4themes_kelly);
        am4core.useTheme(am4themes_animated);

        // Themes end
        // Create chart instance
        var chartpuestos = am4core.create("chartdiv_puestos", am4charts.PieChart);

        chartpuestos.legend = new am4charts.Legend();
        
        // Add data
        chartpuestos.data = {!! json_encode($resultado_valor) !!};
        
        // Add and configure Series
        var pieSeries = chartpuestos.series.push(new am4charts.PieSeries());
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


    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.XYChart);
    chart.addClassNames= true;

    var scrollbarX = new am4core.Scrollbar();
    chart.scrollbarX = scrollbarX;

    chart.language.locale = am4lang_es_ES;

    // Add data
    chart.data = [
        @foreach($resultado_fecha as $g)
            {
            "Fecha": "{{ Carbon\Carbon::parse($g->fecha)->format('Y-m-d') }}",
            "Cuenta": "{{ $g->cuenta }}"
            },
        @endforeach
        ];

    // Create axes
    var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    dateAxis.renderer.grid.template.location = 0;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

    // Create series
    function createSeries(field, name) {
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = field;
        series.dataFields.dateX = "Fecha";
        series.name = name;
        series.tooltipText = "{dateX}: {name} -> [b]{valueY}[/]";
        series.strokeWidth = 4;
        series.tensionX = 0.85;

        var bullet = series.bullets.push(new am4charts.CircleBullet());
        bullet.circle.stroke = am4core.color("#fff");
        bullet.circle.strokeWidth = 2;

        return series;
    }

    var series1 = createSeries("Cuenta", "Votos");

    chart.legend = new am4charts.Legend();
    chart.cursor = new am4charts.XYCursor();

</script>