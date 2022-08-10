<?php
    use App\Models\users;
    use App\Models\reglas;
    use Carbon\Carbon;

$descripcion="Envia al dispositivo un cambio de canal";

$campos_notificaciones=false;

$icono='<i class="fa-solid fa-music"></i>';

$detalle_regla=reglas::find($regla??0);
if(isset($detalle_regla->clientes)){
    $clientes=$detalle_regla->clientes;
} else {
    $clientes=implode(",",clientes());
}

$params='{
    "parametros":[
        {
            "label": "Canal a cambiar", 
            "name": "id_canal", 
            "tipo": "list_db", 
            "multiple": false, 
            "sql": "SELECT DISTINCT \n
                        `canales_musica`.`id_canal` as id, 
                        `canales_musica`.`des_canal` as nombre  \n
                    FROM \n
                        `dispositivos` INNER JOIN `contratos` ON (`dispositivos`.`id_contrato` = `contratos`.`id_contrato`) \n
                        INNER JOIN `dispositivos_canales` ON (`dispositivos`.`id_dispositivo` = `dispositivos_canales`.`id_dispositivo`) \n
                        INNER JOIN `productos` ON (`dispositivos`.`id_producto` = `productos`.`id_producto`) \n
                        INNER JOIN `canales_musica` ON (`dispositivos_canales`.`id_canal` = `canales_musica`.`id_canal`) \n
                    WHERE \n
                    id_cliente in('.$clientes.')  \n
                    ORDER BY des_canal", 
            "required": true,
            "buscar": true
        },
        {
            "label": "Tipo de cambio",
            "name": "id_accion",
            "tipo": "list",
            "multiple": false,
            "list":  "Inmediato|Siguiente cancion",
            "values": "116|138",
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
    if($t->id_tipo_producto==2){
        $tipo_conex='UDP';
    } else {
        $tipo_conex='MQTT';
    }
    if(strlen($observaciones??'')>0){
        $observaciones=substr($observaciones,0,255);
    } else {
        $observaciones="Cambio de evento CambiarCanal";
    }

    DB::table('acciones_dispositivos')->insert([
       'id_dispositivo'=> $t->id_dispositivo,
       'id_accion'=> valor($param_accion, "id_accion"),
       'fecha_envio'=> Carbon::now(),
       'fecha_programacion'=> Carbon::now(),
       'parametros'=> valor($param_accion, "id_canal"),
       'tipo_notificacion'=> $tipo_conex,
       'observaciones'=> $observaciones!=null?$observaciones:'',
    ]);
    
}
?>
