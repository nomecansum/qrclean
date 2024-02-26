@php
    use Carbon\Carbon;
    use Carbon\CarbonInterface;
@endphp
@if($mostrar_graficos==1)
@notmobile
    </tbody>
</table>
<div class="row">
    <div class="col-md-12">
        <div id="chartdiv" style="width:100%; height:300px;  ml-0" class="ml-0"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        @php
            $datos=$incidencias->groupBy('des_tipo_incidencia')->map->count();
            $datos_quesito = [];
            foreach ($datos as $key => $value) {
                $obj=new stdClass();
                $obj->des_estado = $key;
                $obj->cuenta = $value;
                $datos_quesito[]=$obj;
            }   
            //dd($incidencias); 
        @endphp
        <div class="card">
            {{-- <div class="card-header">
                <h3 class="card-title font-bold"><span class="font-bold fs-2">{{ $incidencias->count() }}</span> Incidencias</h3>
            </div> --}}
            <div class="card-body">
                Tipo de {{ $pagina }}
                <div id="chartdiv_incidencias" style="width:100%; height: 400px " ></div>
        
            </div>
        </div>

        

        <script>
            am4core.ready(function() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            am4core.useTheme(am4themes_kelly);

            // Themes end
            // Create chart instance
            var chart_inci = am4core.create("chartdiv_incidencias", am4charts.PieChart);

            chart_inci.legend = new am4charts.Legend();
            chart_inci.legend.maxHeight = 150;
            chart_inci.legend.scrollable = true;
            
            // Add data
            chart_inci.data = {!! json_encode($datos_quesito) !!};
            
            // Add and configure Series
            var pieSeries = chart_inci.series.push(new am4charts.PieSeries());
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
    </div>
    <div class="col-md-6">
        @php
            $datos=$incidencias->groupBy('estado_incidencia')->map->count();
            $datos_quesito = [];
            foreach ($datos as $key => $value) {
                $obj=new stdClass();
                if($key!=''){
                    $obj->des_estado = $key;
                } else{
                    $obj->des_estado = "Sin estado";
                }
                
                $obj->cuenta = $value;
                $datos_quesito[]=$obj;
            }    

            //dd($datos_quesito);
        @endphp
        <div class="card">
            <div class="card-body">
                Estado de {{ $pagina }}
                <div id="chartdiv_incidencias2" style="width:100%; height: 400px " class="ml-0"></div>
        
            </div>
        </div>

        <script>
            am4core.ready(function() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            am4core.useTheme(am4themes_kelly);
        
            // Themes end
            // Create chart instance
            var chart_inci2 = am4core.create("chartdiv_incidencias2", am4charts.PieChart);
        
            chart_inci2.legend = new am4charts.Legend();
            
            // Add data
            chart_inci2.data = {!! json_encode($datos_quesito) !!};
            
            // Add and configure Series
            var pieSeries2 = chart_inci2.series.push(new am4charts.PieSeries());
            pieSeries2.dataFields.value = "cuenta";
            pieSeries2.dataFields.category = "des_estado";
            pieSeries2.slices.template.stroke = am4core.color("#fff");
            pieSeries2.slices.template.strokeWidth = 2;
            pieSeries2.slices.template.strokeOpacity = 1;
            
            // This creates initial animation
            pieSeries2.hiddenState.properties.opacity = 1;
            pieSeries2.hiddenState.properties.endAngle = -90;
            pieSeries2.hiddenState.properties.startAngle = -90;

            }); // end am4core.ready()
        </script>
    </div>
</div>


@php
    $datos=$incidencias->groupBy('fecha_corta')->map->count();
    $resultado_fecha = [];
    foreach ($datos as $key => $value) {
        $obj=new stdClass();
        $obj->fecha = $key;
        $obj->cuenta = $value;
        $resultado_fecha[]=$obj;
    }    

    //dd($datos_quesito);
@endphp

<script>
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
            "Fecha": "{{ Carbon::parse($g->fecha)->format('Y-m-d') }}",
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
        bullet.circle.strokeWidth = 1;

        return series;
    }

    var series1 = createSeries("Cuenta", "{{ $pagina }}");

    chart.legend = new am4charts.Legend();
    chart.cursor = new am4charts.XYCursor();
</script>
@endnotmobile 
@endif