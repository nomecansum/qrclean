<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


$descripcion="Este es un comando busca aquellos contenidos dinamicos que se hayan actualizado en algun tag desde su ultima lectura";

$grupo="A";
    if(is_array(clientes())){
        $clientes=implode(",",clientes());
    }   else {
        $clientes=implode(",",clientes()->ToArray());
    } 
;
$params='{
    "parametros":[
        {
            "label": "TAG",
            "name": "id_tag",
            "tipo": "tags",
            "required": false
        },
        {
            "label": "Tipo de contenido",
            "name": "id_texto_dinamico",
            "tipo": "list_db",
            "multiple": true, 
            "sql": "SELECT DISTINCT \n
                        `textos_dinamicos`.`id_texto_dinamico` as id, \n
                        `textos_dinamicos`.`des_texto_dinamico`as nombre  \n
                    FROM \n
                        `textos_dinamicos` \n
                    WHERE \n
                    id_cliente in('.$clientes.')  \n
                    ORDER BY 2", 
            "required": false,
            "buscar": true
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
        "accion": "ejecutar_tarea_programada.php",
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
            "desc": "ID de tipo de contenido"
        },
        {
            "label": "[nombre]",
            "desc": "Descripcion del tipo de contenido"
        },
        {
            "label": "[fec_actualizacion]",
            "desc": "Fecha de actualizacion"
        }
    ]
}';

function ejecutar($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite

    $parametros = json_decode(json_decode($evento->param_comando));
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $id_tag=valor($parametros,"id_tag");
    $id_texto_dinamico=valor($parametros,"id_texto_dinamico");
    //Construir la query
    $data=DB::table('textos_dinamicos')
        ->select('textos_dinamicos.id_texto_dinamico as id','des_texto_dinamico as nombre','textos_tags.fec_desde as fec_actualizacion')
        ->join('textos_tags','textos_tags.id_texto_dinamico','textos_dinamicos.id_texto_dinamico')
        ->when($evento->clientes!=0,function($query) use ($evento){
             $query->whereIn('id_cliente',explode(",",$evento->clientes));
        })
        ->when($id_texto_dinamico,function($query) use ($id_texto_dinamico){
            $query->wherein('textos_tags.id_texto_dinamico',$id_texto_dinamico);
        })
        ->where('textos_tags.fec_audit','>=',Carbon::parse($evento->fec_ult_ejecucion)->subMinutes($evento->intervalo))
        ->get();
    
    $lista_id=$data->pluck('id')->toArray();

    //Al final se debe retornar un JSON con la lista de ID que se han obtenido de la consulta y sobre los que se actuará en las acciones
    return json_encode([
        "respuesta" => "ok",
        "comando" => basename(__FILE__, '.php'),
        "tipo_id" => "textos_dinamicos",
        "table" => "textos_dinamicos",
        "campo" => "id_texto_dinamico",
        "lista_id" =>  $lista_id,
        "data" => $data,
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);

}

?>
