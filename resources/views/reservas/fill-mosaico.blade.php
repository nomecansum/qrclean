@php
    $puestos_planta=$puestos->where('id_planta',$pl->id_planta);
@endphp
<div class="d-flex flex-wrap">
    @foreach($puestos_planta as $puesto)
        @php
            $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();
            $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();
            $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();
            $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();
            $cuadradito=\App\Classes\colorPuestoRes::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"P",$r->fechas);
            $es_reserva="Reservas";
            if(isMobile()){
                $puesto->factor_puestow=15;
                $puesto->factor_puestoh=15;
                $puesto->factor_letra=2.8;
            } else {
                $puesto->factor_puestow=3.7;
                $puesto->factor_puestoh=3.7;
                $puesto->factor_letra=0.8;
            }
        @endphp
        

        @if(session('CL')['modo_visualizacion_puestos']=='C')
        <div class="text-center font-bold rounded add-tooltip align-middle puesto_parent puesto_parent draggable {{  $cuadradito['clase_disp'] }} {{ $puesto->id_puesto==$id_puesto_edit?'disponible':'' }} mr-2 mb-2" id="puesto{{ $puesto->id_puesto }}" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif{!!  nombrepuesto($puesto) ." \r\n ".$cuadradito['title'] !!}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-opacity="{{ $cuadradito['transp']  }}" data-planta="{{ $pl->des_planta }}" style="height: {{ $puesto->factor_puestow }}vw ; width: {{ $puesto->factor_puestow }}vw; background-color: {{  $cuadradito['color'] }}; color: {{  $cuadradito['font_color'] }}; {{  $cuadradito['borde'] }}; opacity: {{ $cuadradito['transp']  }}">
            <span class="h-100 align-middle text-center puesto_child" style="font-size: {{ $puesto->factor_letra }}vw; color:#666">{{ $puesto->des_puesto }}</span>
            @include('resources.adornos_iconos_puesto')
        </div>
        @else
        <div class="text-center rounded add-tooltip align-middle puesto_parent draggable {{  $cuadradito['clase_disp'] }} {{ $puesto->id_puesto==$id_puesto_edit?'disponible':'' }} " id="puesto{{ $puesto->id_puesto }}" title="{!!  nombrepuesto($puesto) ." \r\n ".$cuadradito['title'] !!}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->des_planta }}" style="height: {{ $puesto->factor_puestow }}vw ; width: {{ $puesto->factor_puestow }}vw; color: {{ $cuadradito['font_color'] }}; {{ $cuadradito['borde'] }}; opacity: {{ $cuadradito['transp']  }}">
            <span class="h-100 align-middle text-center puesto_child" style="font-size: {{ $puesto->factor_letra }}vw; ; color:#666">
                <i class="{{ $puesto->icono_tipo??'' }} fa-2x" style="color: {{ $puesto->color_tipo??'' }}"></i><br>
                {{ $puesto->des_puesto }}</span>
            {{--  @include('resources.adornos_iconos_puesto')  --}}
        </div>
        @endif
        
    @endforeach
</div>