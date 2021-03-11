@if($puesto->mca_incidencia=='S')<br>
    <div  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; line-height: 15px;"><i class="fas fa-exclamation-triangle" style="color: #fff"></i></div>
@elseif($puesto->id_estado==5)<br>
    <div  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; line-height: 15px;"><i class="fas fa-ban" style="color: #fff"></i></div>
@elseif(isset($reserva))<br>
    <div  style="font-weight: bold; font-size: {{ $puesto->factor_letra+0.8 }}vw; color: #ff0; line-height: 15px">R</div>
@elseif(isset($asignado_usuario))<br>
    <div class="adorno_puesto"  style="font-size: {{ $puesto->factor_letra+0.8 }}vw; color: {{ isset($es_reserva) && $es_reserva?"#fff":"#f4d35d" }}; line-height: 0px; margin-top: 0.4em">{{ iniciales($asignado_usuario->name,3) }}</div>
@elseif(isset($asignado_miperfil))<br>
    <div  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; color: #05688f; line-height: 15px;"><i class="fad fa-user" style="color: {{ isset($es_reserva) && $es_reserva?"#339470":"#fff" }}; margin-top: 0.4em"></i></div>
@elseif(isset($asignado_otroperfil))<br>
    <div  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; line-height: 15px; margin-top: 0.4em"><i class="fad fa-users" style="color: #fff"></i></div>
@elseif(isset(session('CL')['modo_visualizacion_puestos']) && session('CL')['modo_visualizacion_puestos']=='I')
<div  style="line-height: 0px; margin-top: 0.4em"><i class="{{ $puesto->icono_tipo??'' }} fa-2x" style="color: {{ $puesto->color_tipo??'' }}"></i></div>
@endif