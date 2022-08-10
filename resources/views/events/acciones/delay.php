<?php

$descripcion = "Espera un determinado numero de segundos antes de pasar al siguiente paso";
$campos_notificaciones=false;

$icono='<i class="fa-solid fa-clock"></i>';

$params='{
    "parametros":[
        {
            "label": "Segundos",
            "name": "seg",
            "tipo": "num",
            "def": "",
            "min": "0",
            "max": "1000",
            "required": true
        }
    ]
}';

$func_accion = function($accion, $resultado, $campos,$id) {

    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));

    $resultado = collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    $datos = $resultado->where('id', $id)->first();

    //A partir de aqui empieza la parte "personalizada" de la accion

    sleep(valor($param_accion, "seg"));
    //Especial atencion a la funcion comodines_texto
    
    //enviar_email($emp->dir_email, config('mail.from.address'), $emp->dir_email, $emp->nom_empleado." ".$emp->ape_empleado, valor($param_accion, "titulo"), "email.mail_eventos", $emp, $cli, null, comodines_texto(valor($param_accion, "cuerpo"), $campos, $datos));

    //echo("Enviado email a ".$datos->nombre." ".$emp->dir_email.chr(10).chr(13));
    //echo("Ejecutada accion enviar email empleado Regla: ".$accion->cod_regla." Accion: [".$accion->val_iteracion."-".$accion->num_orden."]".chr(10).chr(13));

}
?>
