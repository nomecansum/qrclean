@if($puesto->mca_incidencia=='S')<br>
    <span  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; line-height: 15px;"><i class="fas fa-exclamation-triangle" style="color: #fff"></i></span>
@elseif($puesto->id_estado==5)<br>
    <span  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; line-height: 15px;"><i class="fas fa-ban" style="color: #fff"></i></span>
@elseif(isset($reserva))<br>
    <span  style="font-weight: bold; font-size: {{ $puesto->factor_letra+0.8 }}vw; color: #ff0; line-height: 15px">R</span>
@elseif(isset($asignado_usuario))<br>
    <span  style="font-size: {{ $puesto->factor_letra+0.8 }}vw; color: {{ isset($es_reserva) && $es_reserva?"#fff":"#f4d35d" }}; line-height: 0px">{{ iniciales($asignado_usuario->name,3) }}</span>
@elseif(isset($asignado_miperfil))<br>
    <span  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; color: #05688f; line-height: 15px;"><i class="fad fa-user" style="color: {{ isset($es_reserva) && $es_reserva?"#339470":"#fff" }}"></i></span>
@elseif(isset($asignado_otroperfil))<br>
    <span  style="font-size: {{ $puesto->factor_letra+0.5 }}vw; line-height: 15px;"><i class="fad fa-users" style="color: #fff"></i></span>
@elseif(isset(session('CL')['modo_visualizacion_puestos']) && session('CL')['modo_visualizacion_puestos']=='I')
<i class="{{ $puesto->icono_tipo??'' }} fa-2x" style="color: {{ $puesto->color_tipo??'' }}"></i><br>
@endif