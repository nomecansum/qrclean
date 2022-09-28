<?php

$descripcion = "Envia un email a un usuario concreto";

$campos_notificaciones=false;

$icono='<i class="fa-solid fa-gears"></i>';

$tipo_destino='*';

$params='{
    "parametros":[
        {
            "label": "Parametro de mentira para que este relleno",
            "name": "void",
            "tipo": "void",
            "required": false
        }
    ]
}';

$func_accion = function($accion, &$resultado, $campos,$id) {

    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));

    $evento=DB::table('eventos_reglas')
        ->where('cod_regla',$accion->cod_regla)
        ->first();
    Log::debug('Reevaluando Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando);
    $this->log_evento('Reevaluando Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando,$accion->cod_regla);
    include(resource_path('views/events/comandos').'/'.$evento->nom_comando);
    $resultado_json=$func_comando($evento);
    $resultado=json_decode($resultado_json);
    unset($func_comando);
    $this->log_evento(count($resultado->lista_id).' ID detectados',$accion->cod_regla);
    try{
        $campos=json_decode($campos);
        $campos=$campos->campos;
    } catch(\Throwable $e){
        $campos=[];
    }

}
?>
