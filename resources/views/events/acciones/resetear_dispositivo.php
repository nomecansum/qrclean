<?php
    use App\Models\users;
    use Carbon\Carbon;

$descripcion="Manda una accion de reset/reboot al dispositivo";

$campos_notificaciones=false;

$icono='<i class="fa-solid fa-power-off"></i>';

$params='{
    "parametros":[
        {
            "label": "Accion",
            "name": "id_accion",
            "tipo": "list",
            "multiple": false,
            "list":  "Reset|Reboot|MMDReset|MMDReboot",
            "values": "126|96|40|41",
            "required": true
        },
        {
            "label": "Observaciones",
            "name": "observaciones",
            "tipo": "txt",
            "def": "",
            "required": false
        }
        
    ]
}';

$func_accion = function($accion, $resultado, $campos, $id) {
    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));
    $id_accion = valor($param_accion, "id_accion");
    $observaciones = valor($param_accion, "observaciones");

    $resultado = Collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    $datos = $resultado->where('id',$id)->first();

    Log::debug('Enviada accion '. valor($param_accion, "id_acion").' a '.$datos->nombre);
    //Y enviamos el comando
    $t=DB::table('dispositivos')
        ->select("dispositivos.id_dispositivo",'contratos.id_cliente','productos.id_tipo_producto')
        ->join('productos','dispositivos.id_producto','productos.id_producto')
        ->join('contratos','dispositivos.id_contrato','contratos.id_contrato')
        ->where('dispositivos.id_dispositivo',$id)
        ->distinct()
        ->first();

    //Primero a ver el tipo de conectividad que tienen
    if(isset($t)){
        if($t->id_tipo_producto==2){
            $tipo_conex='UDP';
        } else {
            $tipo_conex='MQTT';
        }
        if(strlen($observaciones??'')>0){
            $observaciones=substr($observaciones,0,255);
        } else {
            $observaciones="Accion de evento EnviarAccion";
        }

        DB::table('acciones_dispositivos')->insert([
        'id_dispositivo'=> $t->id_dispositivo,
        'id_accion'=> $id_accion,
        'fecha_envio'=> Carbon::now(),
        'fecha_programacion'=> Carbon::now(),
        'parametros'=> null,
        'tipo_notificacion'=> $tipo_conex,
        'observaciones'=> $observaciones,
        ]);
    } else {
        $this->log_evento('Dispositivo '.$id.' no encontrado',$accion->cod_regla);
    }
}
?>
