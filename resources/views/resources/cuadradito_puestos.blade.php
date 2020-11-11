
@php
if(isset($reserva)){
    $color="LightCoral";
    $font_color="#fff";
    $clase_disp="";
    $title="Reservado por ".$reserva->name." para hoy";
} else if(isset($asignado_usuario)){
    $color="#f2cb07";
    $font_color="#fff";
    $clase_disp="";
    $title="Puesto permanentemente asignado a ".$asignado_usuario->name;
    $borde="border: 3px solid #ff9f1a; border-radius: 16px";
} else if(isset($asignado_otroperfil)){
    $color="#e8c468";
    $font_color="#fff";
    $clase_disp="";
    $title="Puesto reservado para  ".$asignado_otroperfil->des_nivel_acceso;
} else if(isset($asignado_miperfil)){
    $color="#dff9d2";
    $font_color="#05688f";
    $clase_disp="disponible";
    $title="Puesto reservado para  ".$asignado_miperfil->des_nivel_acceso;
    $borde="border: 3px solid #05688f; border-radius: 10px";
}   else {
    $color="#dff9d2";
    $font_color="#aaa";
    $clase_disp="disponible";
    $tam_borde=isMobile()?'3':'5';
    $borde="border: ".$tam_borde."px solid ".$puesto->val_color??"#fff".";";
} 
@endphp
<div class="text-center rounded add-tooltip align-middle flpuesto draggable {{ $clase_disp }}" id="puesto{{ $puesto->id_puesto }}" title="{{ $title }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw;top: {{ $top }}px; left: {{ $left }}px; background-color: {{ $color }}; color: {{ $font_color }}; {{ $borde }}">
    <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw;">{{ $puesto->cod_puesto }}</span>
    @if(isset($reserva))<br>
        <span  style="font-size: {{ $puesto->factor_letra+0.8 }}vw; color: #ff0">R</span>
    @endif
    @if(isset($asignado_usuario))<br>
        <span  style="font-size: {{ $puesto->factor_letra+0.8 }}vw; color: #f4d35d; line-height: 0px">{{ iniciales($asignado_usuario->name,3) }}</span>
    @endif
    @if(isset($asignado_miperfil))<br>
        <span  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; color: #05688f; line-height: 0px"><i class="fad fa-users" style="color: #f4a462"></i></span>
    @endif
    @if(isset($asignado_otroperfil))<br>
        <span  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; line-height: 0px"><i class="fad fa-users" style="color: #fff"></i></span>
    @endif
</div>