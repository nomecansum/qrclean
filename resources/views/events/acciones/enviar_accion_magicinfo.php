<?php
    use App\Models\users;

$descripcion="Envia una accion al dispositivo indicado a traves del servidor de magicinfo";

$campos_notificaciones=false;

$icono='<i class="fa-solid fa-tv"></i>';

$params='{
    "parametros":[
        {
            "label": "Accion",
            "name": "accion_mi",
            "tipo": "list",
            "multiple": false,
            "list":  "Apagar pantalla|Encender pantalla|Apagar SBB|Encender SBB|Fuente Magicinfo",
            "values": "{\"basicPanelStatus\": 1}|{\"basicPanelStatus\": 0}|{\"basicPower\": 1}|{\"basicPower\": 0}|{\"basicSource\": 99}",
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

    $resultado = Collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    $datos = $resultado->where('id',$id)->first();

    Log::debug('Enviada accion magicinfo '. valor($param_accion, "accion_mi").' a '.$datos->nombre);
    
    $resultado=enviar_request_magicinfo('put','/restapi/v1.0/rms/devices/'.$datos->id.'/display',valor($param_accion, "accion_mi"));
    if (is_numeric($resultado)){
        Log::error('Result code: '.$resultado);
    } else {
        Log::info($resultado['status']);
    }
}
?>
