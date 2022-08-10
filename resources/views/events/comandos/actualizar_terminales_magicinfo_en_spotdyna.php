<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\token_mi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Models\dispositivos;
use Symfony\Component\Console\Helper\ProgressBar;

$descripcion="Este es un comando que busca los terminales de magicinfo y actualiza su estado en spotdyna";

$grupo="A";

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

//Acciones recomendadas para este comando
$acciones_def='[]';

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
    $parametros = json_decode(json_decode($evento->param_comando));
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $pantalla=valor($parametros,"pantalla");
    $power=valor($parametros,"power");
    $fuente=valor($parametros,"fuente");
    //Primero vamos a ver si hay token y que caducidad tiene
    actualizar_token_magicinfo();
    //Ahora traemos la lista completa de terminales
    $lista_terminales=DB::connection('pgsql')
        ->table('mi_dms_info_display')
        ->select('mi_dms_info_device.device_id as id','mi_dms_info_device.device_name as name','mi_dms_info_device.last_connection_time as power','mi_dms_info_display.basic_source as source','mi_dms_info_display.basic_panel_status as pantalla')
        ->join('mi_dms_info_device','mi_dms_info_display.device_id','mi_dms_info_device.device_id')
        ->get();
    $progress = new ProgressBar($output, count($lista_terminales));
    foreach($lista_terminales as $term){
        //Y actualizamos el estado en adminweb
        $dir_mac=strtoupper($term->id);
        $upd_t=dispositivos::where('mac',strtoupper($dir_mac))->first();
        
        if($upd_t && $upd_t->COD_TERMINAL){
            $term->cod_terminal=$upd_t->COD_TERMINAL;
            DB::table('dispositivos')->where('id_dispositivo',$upd_t->id_dispositivo)->update([
                    'mca_pantalla'=>$term->pantalla==0?1:0
                ]);
            }
        $progress->advance();
    }
    $progress->finish();

    Log::info("Actualizado estado en AdminWEB");
    //Ahora vamos a sacar los que tengan la pantalla en el estodo que nos interesa

    //Por ultimo sacamos la lista de los que cumplen
    $lista_id=[];
    foreach($lista_terminales as $t){
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
        "lista_id" => [],
        "data" => [],
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);

}

?>
