<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\token_mi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Models\dispositivos;
use Symfony\Component\Console\Helper\ProgressBar;

$descripcion="Este es un comando que busca los terminales de magicinfo que cumplan un determinado criterio, utilizando el API de MI";

$grupo="A";

$params='{
    "parametros":[
        {
            "label": "Estado pantalla",
            "name": "pantalla",
            "tipo": "list",
            "multiple": false,
            "list":  "---|Apagada|Encendida",
            "values": "-1|0|1",
            "required": true
        },
        {
            "label": "Estado fuente",
            "name": "fuente",
            "tipo": "list",
            "multiple": false,
            "list":  "---|MagicInfo|HDMI|URL",
            "values": "-1|96|33|99",
            "required": true
        },
        {
            "label": "Estado power",
            "name": "power",
            "tipo": "list",
            "multiple": false,
            "list":  "---|Apagado|Encendido",
            "values": "-1|0|1",
            "required": true
        }
    ]
}';

//Acciones recomendadas para este comando
$acciones_def='[
       {
        "accion": "enviar_comando_magicinfo.php",
        "iteracion": 1,
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
        }
    ]
}';

function ejecutar($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite

    //Primero vamos a ver si hay token y que caducidad tiene
    actualizar_token_magicinfo();
    //Ahora traemos la lista completa de terminales
    $lista_terminales=enviar_request_magicinfo('get','/restapi/v1.0/rms/devices?startIndex=1&pageSize=5000','');
    
    $array_terminales=[];
    foreach($lista_terminales['items'] as $term){
        //Ahora vamos a ver si tiene un status
        $item=new stdclass;
        $item->id=$term['deviceId'];
        $item->nombre=$term['deviceName'];
        $item->power=$term['power'];
        $item->ult_status="";
        $array_terminales[]=$item;
    }
    //Ahora veremos el estado de cada uno de los terminales
    Log::info("Obtenidos ".count($array_terminales)." terminales");
    $progress = new ProgressBar($output, count($array_terminales));
    foreach($array_terminales as $term){
        $status=enviar_request_magicinfo('get','/restapi/v1.0/rms/devices/'.$term->id.'/display','');
        if($status['status']=='Success'){
            $term->pantalla=$status['items']['basicPanelStatus']==0?true:false;
            $term->source=$status['items']['basicSource'];
        }
        //Y actualizamos el estado en adminweb
        $dir_mac=strtoupper($term->id);
        $upd_t=dispositivos::where('mac',strtoupper($dir_mac))->first();
        if($upd_t && $upd_t->COD_TERMINAL){
            $term->id_dispositivo=$upd_t->id_dispositivo;
            DB::table('dispositivos')->where('id_dispositivo',$upd_t->id_dispositivo)->update([
                    'mca_pantalla'=>$term->pantalla?1:0
                ]);
        }
        $progress->advance();
    }
    $progress->finish();
    Log::info("Actualizado estado en Spotdyna");
    //Ahora vamos a sacar los que tengan la pantalla en el estodo que nos interesa
    $parametros = json_decode(json_decode($evento->param_comando));
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $pantalla=valor($parametros,"pantalla");
    $power=valor($parametros,"power");
    $fuente=valor($parametros,"fuente");
    $terminales=collect($array_terminales);
    if($pantalla!=-1){
        $terminales=$terminales->where('pantalla',$pantalla==1?true:false);
    }
    if($power!=-1){
        $terminales=$terminales->where('power',$power==1?true:false);
    }
    if($fuente!=-1){
        $terminales=$terminales->where('source',$fuente);
    }

    //Por ultimo sacamos la lista de los que cumplen
    $lista_id=[];
    foreach($terminales as $t){
        $lista_id[]=$t->id;
    }
    Log::info("Resultado ".count($lista_id)." terminales");

    
    //Al final se debe retornar un JSON con la lista de ID que se han obtenido de la consulta y sobre los que se actuará en las acciones
    return json_encode([
        "respuesta" => "ok",
        "comando" => basename(__FILE__, '.php'),
        "tipo_id" => "mac",
        "table" => "dispositivos",
        "campo" => "mac",
        "lista_id" => $lista_id,
        "data" => $terminales,
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);

}

?>
