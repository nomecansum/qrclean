<?php
if(!function_exists('mis_clientes')){
    function mis_clientes(){
        if(is_array(clientes())){
            $clientes=implode(",",clientes());
        }   else {
            $clientes=implode(",",clientes()->ToArray());
        } 
        return $clientes;
    }
}
$descripcion = "Escribe una notificacion en la web para el usuario";

$campos_notificaciones=true;

$icono='<i class="fa-solid fa-browser"></i>';

$tipo_destino='*';

$params='{
    "parametros":[
        {
            "label": "Tipo",
            "name": "id_tipo_notificacion",
            "tipo": "list_db",
            "multiple": false,
            "sql": "select id_tipo_notificacion as id, des_tipo_notificacion as nombre from notificaciones_tipos",
            "required": true
        },
        {
            "label": "Texto de la notificacion",
            "name": "cuerpo",
            "tipo": "txt",
            "def": "",
            "required": true
        },
        {
            "label": "Destinatarios",
            "name": "usuarios",
            "tipo": "list_db",
            "multiple": true,
            "sql": "SELECT DISTINCT \n
                        `users`.`id` as id, \n
                        concat(\'[\',nom_cliente,\'] - \',`users`.`name`) as nombre  \n
                    FROM \n
                        `users` \n
                        INNER JOIN `clientes` ON (`users`.`id_cliente` = `clientes`.`id_cliente`) \n
                    WHERE \n
                    `users`.`id_cliente` in('.mis_clientes().')  \n
                    ORDER BY 2", 
            "required": true,
            "buscar": true
        }
    ]
}';

$func_accion = function($accion, $resultado, $campos,$id) {

    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));

    $resultado = collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso

    $datos = $resultado->where('id', $id)->first();


    foreach(valor($param_accion, "usuarios") as $u){
        insertar_notificacion_web($u,valor($param_accion, "id_tipo_notificacion"),comodines_texto(valor($param_accion, "cuerpo"), $campos, $datos),0);
    }


}
?>
