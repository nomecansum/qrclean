<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


$descripcion="Este es un comando que busca aquellos dispositivos que esten activos y lleven mas de N dias sin conectar para actualizar";

$grupo="A";

$params='{
    "parametros":[
        {
            "label": "Tiempo desde el ultimo status (min)",
            "name": "diff_minutos",
            "tipo": "num",
            "def": "15",
            "required": true
        },
        {
            "label": "Dias sin conexion",
            "name": "dias_conex",
            "tipo": "num",
            "def": "3",
            "required": true,
            "min": "1",
            "max": "365"
        },
        {
            "label": "Que pertenezcan a los tag",
            "name": "id_tag",
            "tipo": "tags",
            "required": false
        }
    ]
}';

//Acciones recomendadas para este comando
$acciones_def='[
       {
        "accion": "enviar_accion.php",
        "iteracion": 1,
        "orden" : 1
       },
       {
        "accion": "enviar_email_usuario.php",
        "iteracion": 2,
        "orden" : 1
       }
    ]';

//Los campos son una descriocion de los campos que se podrán leer en las notificaciones directaamente desde los datos leidos del comando
$campos='{
    "campos":
    [
        {
            "label": "[fecha]",
            "desc": "fecha actual"
        },
        {
            "label": "[hora]",
            "desc": "Hora actual"
        },
        {
            "label": "[id]",
            "desc": "ID de dispositivo"
        },
        {
            "label": "[nombre]",
            "desc": "Nombre del dispositivo"
        },
        {
            "label": "[ult_status]",
            "desc": "Fecha de ultimo status"
        },
        {
            "label": "[ult_conexion]",
            "desc": "Fecha de ultima conexion   "
        }
    ]
}';

function ejecutar($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite

    $parametros = json_decode(json_decode($evento->param_comando));
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $diff_minutos=valor($parametros,"diff_minutos");
    $no_considerar=valor($parametros,"no_considerar");
    $id_tag=valor($parametros,"id_tag");
    $dias_conex=valor($parametros,"dias_conex");

    //Construir la query
    $terminales=DB::table('dispositivos')
        ->select('id_dispositivo as id','nombre as nombre','ult_status as ult_status')
        ->join('productos','dispositivos.id_producto','productos.id_producto')
        ->join('contratos','dispositivos.id_contrato','contratos.id_contrato')
        ->when($evento->clientes!=0,function($query) use ($evento){
             $query->whereIn('id_cliente',explode(",",$evento->clientes));
        })
        ->where('ult_status','<',Carbon::now()->setTimezone(config('app.timezone_servidor'))->subMinutes($diff_minutos))
        ->where('ult_conexion','<',Carbon::now()->setTimezone(config('app.timezone_servidor'))->subDays($dias_conex))
        ->get();

    $lista_id=$terminales->pluck('id')->toArray();

    //Al final se debe retornar un JSON con la lista de ID que se han obtenido de la consulta y sobre los que se actuará en las acciones
    return json_encode([
        "respuesta" => "ok",
        "comando" => basename(__FILE__, '.php'),
        "tipo_id" => "dispositivos",
        "table" => "dispositivos",
        "campo" => "id_dispositivo",
        "lista_id" =>  $lista_id,
        "data" => $terminales,
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);

}

?>
