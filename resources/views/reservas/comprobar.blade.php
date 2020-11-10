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
                    $title=$puesto->des_puesto;
                    $borde="";
                    if(isset($reserva)){
                        $color="LightCoral";
                        $font_color="#fff";
                        $clase_disp="";
                        $title="Reservado por ".$reserva->name." para hoy";
                    } else if(isset($asignado_usuario)){
                        $color="LightCoral";
                        $font_color="#fff";
                        $clase_disp="";
                        $title="Puesto permanentemente asignado a ".$asignado_usuario->name;
                        $borde="border: 3px solid #ff9f1a; border-radius: 16px";
                    } else if(isset($asignado_otroperfil)){
                        $color="LightCoral";
                        $font_color="#fff";
                        $clase_disp="";
                        $title="Puesto reservado para  ".$asignado_otroperfil->des_nivel_acceso;
                    } else if(isset($asignado_miperfil)){
                        $color="#dff9d2";
                        $font_color="##05688f";
                        $clase_disp="disponible";
                        $title="Puesto reservado para  ".$asignado_miperfil->des_nivel_acceso;
                        $borde="border: 3px solid #05688f; border-radius: 10px";
                    }   else {
                        $color="#dff9d2";
                        $font_color="#aaa";
                        $clase_disp="disponible";
                        $borde="border: 5px solid ".$puesto->val_color??"#fff".";";
                    } 
                    
                    // if(in_array($p->id_puesto,$reservas->pluck('reservas.id_puesto')->toArray())){
                    //     $color="LightCoral";
                    //     $clase_disp="";
                    // }   else {
                    //     $color="#dff9d2";
                    //     $clase_disp="disponible";
                    // }
                @endphp
                    <div class="text-center font-bold rounded add-tooltip align-middle flpuesto draggable {{ $clase_disp }} mr-2 mb-2" id="puesto{{ $puesto->id_puesto }}" title="{{ $title }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $value }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw; background-color: {{ $color }}; color: {{ $font_color }}; {{ $borde }}">
                        <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw;">{{ $puesto->cod_puesto }}</span>
                        @if(isset($reserva))<br>
                            <span class="font-bold" style="font-size: 18px; color: #ff0">R</span>
                        @endif
                        @if(isset($asignado_usuario))<br>
                            <span class="font-bold" style="font-size: 18px; color: #f4d35d">{{ iniciales($asignado_usuario->name,3) }}</span>
                        @endif
                        @if(isset($asignado_miperfil))<br>
                            <span class="font-bold" style="font-size: 18px; color: #05688f"><i class="fad fa-users" style="color: #f4a462"></i></span>
                        @endif
                        @if(isset($asignado_otroperfil))<br>
                            <span class="font-bold" style="font-size: 18px;"><i class="fad fa-users" style="color: #fff"></i></span>
                        @endif
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