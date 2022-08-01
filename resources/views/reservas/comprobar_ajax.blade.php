<script>
@foreach($puestos as $puesto)
    @php
        $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
        $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"P");
        $es_reserva="P";
        if(isMobile()){
            if($puesto->factor_puesto<3.5){
                $puesto->factor_puesto=12;
                $puesto->factor_letra=2.8;
            } else {
                $puesto->factor_puesto=$puesto->factor_puesto*4;
                $puesto->factor_letra=$puesto->factor_letra*4;
            }
            
            
        } else if($puesto->factor_puesto<3.5){
            $puesto->factor_puesto=3.7;
            $puesto->factor_letra=0.8;
        }
    @endphp
    $("#puesto{{ $puesto->id_puesto }}").removeClass("disponible");
    $("#puesto{{ $puesto->id_puesto }}").addClass("{{  $cuadradito['clase_disp'] }}");
    $("#puesto{{ $puesto->id_puesto }}").prop('title', "{!! $puesto->des_puesto." \r\n ".$cuadradito['title'] !!}" );

@endforeach
</script>