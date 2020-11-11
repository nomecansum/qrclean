@php
    $edificio_ahora=0;
    $planta_ahora=0;
    //dd($puestos);
@endphp
<div class="row botones_accion">
    <div class="col-md-8">
        <span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="col-md-4 text-right">
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar" style="color: #1e90ff"><i class="fad fa-th"></i> Mosaico</a>
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar_plano"><i class="fad fa-map-marked-alt"></i> Plano</a>
    </div>
</div>
@foreach ($edificios as $e)
<div class="panel">
    <div class="panel-body">
        @php
            $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
        @endphp
        @foreach($plantas as $key=>$value)
            <h3 class=" w-100 bg-gray rounded">PLANTA {{ $value }}</h3>
            @php
                $puestos_planta=$puestos->where('id_planta',$key);
            @endphp
            <div class="d-flex flex-wrap">
                @foreach($puestos_planta as $puesto)
                @php
                    $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
                    $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                    $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                    $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
                    $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"R");
                    $es_reserva="P";
                    if(isMobile()){
                        $puesto->factor_puesto=$puesto->factor_puesto*3;
                        $puesto->factor_letra=$puesto->factor_letra*3;
                    }
                @endphp
                    <div class="text-center font-bold rounded add-tooltip align-middle flpuesto draggable {{  $cuadradito['clase_disp'] }} mr-2 mb-2" id="puesto{{ $puesto->id_puesto }}" title="{!! $puesto->des_puesto." \r\n ".$cuadradito['title'] !!}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $value }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw; background-color: {{  $cuadradito['color'] }}; color: {{  $cuadradito['font_color'] }}; {{  $cuadradito['borde'] }}; opacity: {{ $cuadradito['transp']  }}">
                        <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw; color:#666">{{ $puesto->cod_puesto }}</span>
                        @include('resources.adornos_iconos_puesto')
                    </div>
                    
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endforeach
<script>

    $('.sitio').click(function(){
        $('#des_puesto').html('');
        $('#des_puesto_form').html('');
        
        $('#id_puesto').val(null);
        $('.disponible').removeClass('bg-info');
        $('.disponible').each(function(){
            $(this).css('background-color',$(this).data('bgcolor'));
        });
        
    });

    $('.disponible').click(function(){
        $('#des_puesto').html($(this).data('desc'));
        $('#des_puesto_form').val($(this).data('desc'));
        $('#id_puesto').val($(this).data('id'));
        $('.disponible').removeClass('bg-info');
        $('.disponible').each(function(){
            $(this).css('background-color',$(this).data('bgcolor'));
        });
        $(this).css('background-color','');
        $(this).addClass('bg-info');
        animateCSS('#des_puesto','zoomIn');
        $('#frm_contador').submit();
    })

    $('.boton_modo').click(boton_modo_click);

    var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();
</script>