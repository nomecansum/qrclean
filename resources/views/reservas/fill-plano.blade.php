
@php
// dd($pl);   
@endphp
<div class="form-group col-md-4 mb-3">
    <label>Zoom: </label> <span id="puesto-zoom-def-val-{{ $pl->id_planta }}"></span>
    <div id="puesto-zoom-def-{{ $pl->id_planta }}"></div>	
</div>
{{-- <div class="viewport"></div> --}}
@if(isset($pl->img_plano))
{{--  {!! json_encode($pl->posiciones) !!}  --}}

    
    @php
        $left=0;
        $top=0;
        if(isset($puestos)){ //La lista de puestos a mostrar viene ya filtrada del controller
            $puestos_mostrar=$puestos->pluck('id_puesto')->toArray();
        }
        
        $puestos= DB::Table('puestos')
            ->select('puestos.*','plantas.*','estados_puestos.val_color as color_estado','estados_puestos.hex_color','estados_puestos.des_estado', 'puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->where('puestos.id_planta',$pl->id_planta)
            ->when(isset($puestos_mostrar), function($q) use($puestos_mostrar){
                $q->wherein('puestos.id_puesto',$puestos_mostrar);
            })
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->where(function($q){
                if(!checkPermissions(['Mostrar puestos no reservables'],['R'])){
                    $q->where('puestos.mca_reservar','S');
                }
            })
            ->get();
    @endphp
    
    @if($puestos->count()>0)
   
    <div class="container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%" id="img_fondo{{ $pl->id_planta }}">
        @else
        {{-- <div class="row">
            <div class="col-md-1"></div><div class="col-md-4 bg-warning pad-all rounded font-20 font-bold v-middle"><i class="fad fa-info-square fa-2x"></i> No hay puestos disponibles</div> --}}
            <script>
                $('#planta{{ $pl->id_planta }}').hide();
            </script>
    @endif
    
    @foreach($puestos as $puesto)
        @php
            $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
            $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
            $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
            $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
            $es_reserva="P";
            $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,$es_reserva);

        @endphp
        {{--  --}}
        <div class="text-center  add-tooltip align-middle flpuesto draggable puesto_parent {{ $cuadradito['clase_disp'] }} {{ $puesto->id_puesto==$id_puesto_edit?'disponible':'' }}" id="puesto{{ $puesto->id_puesto }}" title="{{ strip_tags( $puesto->cod_puesto." \r\n ".$cuadradito['title']) }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" data-factorh="{{ $puesto->factor_puestoh }}" data-factorw="{{$puesto->factor_puestow}}" data-factorr="{{ $puesto->factor_puestor }}" style="height: {{ $puesto->factor_puestoh }}% ; width: {{ $puesto->factor_puestow }}%; top: {{ $top }}px; left: {{ $left }}px; background-color: {{ $cuadradito['color'] }}; @if(session('CL')['modo_visualizacion_puestos']=='C') color: {{ $cuadradito['font_color'] }} @endif; {{ $cuadradito['borde'] }}; opacity: {{ $cuadradito['transp']}}; border-radius: {{ $cuadradito['border-radius'] }}  ">
            @if(session('CL')['modo_visualizacion_puestos']=='C')
                <span class="h-100 align-middle text-center puesto_child" style="font-size: {{ $puesto->factor_letra }}vw; ; color:#666">{{ $puesto->des_puesto }}</span>
                @include('resources.adornos_iconos_puesto')
            @else
                <i class="{{ $puesto->icono_tipo }} fa-2x" style="color: {{ $puesto->color_tipo }}"></i><br>
                {{ $puesto->des_puesto }}</span>
            @endif
        </div>
    @php
        $left+=50;
        if($left==500){
            $left=0;
            $top+=50;
        }
     @endphp
    @endforeach
</div>
{{-- <div>fh: {{ $pl->factor_puestoh }} fw: {{ $pl->factor_puestow }}</div> --}}
<script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
<script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
<script>
    var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();
    
    try{
        posiciones={!! json_encode($pl->posiciones)??'[]' !!};
        //posiciones=JSON.parse(posiciones); 
    } catch($err){
        posiciones=[];
    }
    try{
        document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
    } catch($err){
       
    }

    zoom_actual{{ $pl->id_planta }}=100;
    var hacer_zoom{{ $pl->id_planta }};

    function zoom{{ $pl->id_planta }}(){

        $('#plano{{ $pl->id_planta }}').animate({ 'zoom': zoom_actual{{ $pl->id_planta }}/100 }, 400);
        $('#plano{{ $pl->id_planta }}').animate({ 'width': zoom_actual{{ $pl->id_planta }}+'%' }, 400);
        recolocar_puestos();
    }
    

    var z_def{{ $pl->id_planta }} = document.getElementById('puesto-zoom-def-{{ $pl->id_planta }}');
    var z_def_value{{ $pl->id_planta }} = document.getElementById('puesto-zoom-def-val-{{ $pl->id_planta }}');

    noUiSlider.create(z_def{{ $pl->id_planta }},{
        start   : [ 100 ],
        connect : 'lower',
        step: 10,
        range   : {
            'min': [  20 ],
            'max': [ 500 ]
        },
        format: wNumb({
            decimals: 0
        }),
    });

    z_def{{ $pl->id_planta }}.noUiSlider.on('update', function( values, handle ) {
        z_def_value{{ $pl->id_planta }}.innerHTML = values[handle]+' %';
        zoom_actual{{ $pl->id_planta }}=values[handle];
        clearTimeout(hacer_zoom{{ $pl->id_planta }});
        hacer_zoom{{ $pl->id_planta }}=setTimeout(() => {
            zoom{{ $pl->id_planta }}();
        }, 500);
    });

    

    //document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
    //$('#plano{{ $pl->id_plano }}').data('posiciones',posiciones);
</script>
@else
    <div id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        No hay plano de la planta
    </div>
@endif