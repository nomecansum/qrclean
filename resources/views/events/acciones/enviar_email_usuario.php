<?php

$descripcion = "Envia un email a un usuario concreto";

$icono='<i class="fa-solid fa-envelope"></i>';

$params='{
    "parametros":[
        {
            "label": "e-mail del usuario",
            "name": "e-mail",
            "tipo": "txt",
            "def": "",
            "required": true
        },
        {
            "label": "Titulo",
            "name": "titulo",
            "tipo": "txt",
            "def": "",
            "required": true
        },
        {
            "label": "Cuerpo del mensaje",
            "name": "cuerpo",
            "tipo": "html5",
            "def": "",
            "required": false
        }
    ]
}';

$func_accion = function($accion, $resultado, $campos,$id) {

    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion??[];
    $param_accion = json_decode(json_decode($param_accion));

    $resultado = collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    $datos = $resultado->where('id', $id)->first();

    //A partir de aqui empieza la parte "personalizada" de la accion
    //Especial atencion a la funcion comodines_texto

    $result_email=enviar_email(valor($param_accion, "e-mail"), config('mail.from.address'), valor($param_accion, "e-mail"),valor($param_accion, "e-mail"), valor($param_accion, "titulo"), "email.mail_eventos", null, comodines_texto(valor($param_accion, "cuerpo"), $campos, $datos));

    $this->log_evento("Enviado email a ".$datos->nombre." ".valor($param_accion, "e-mail").': '.$result_email,$accion->cod_regla);
  
}
?>
