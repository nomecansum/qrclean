<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\cucoWEB;
use Illuminate\Support\Facades\Log;

class taskejemplo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:ejemplo {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE
    //Para probar una tarea se debe ejecutar  php artisan task:<NOMBREDETAREA> <IDDETAREA>

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    static function params(){
        $params='{
            "parametros":[
                {
                    "label": "Parametro tipo boolean",
                    "name": "mca_respetar_festivos",
                    "tipo": "bool",
                    "def": true
                },
                {
                    "label": "Parametro numerico",
                    "name": "val_margen",
                    "tipo": "num",
                    "def": "15"
                },
                {
                    "label": "Parametro de texto",
                    "name": "val_texto",
                    "tipo": "txt",
                    "def": "dato"
                },
                {
                    "label": "Parametro lista multiple proveniente de BDD",
                    "name": "cod_colectivo",
                    "tipo": "list_db",
                    "multiple": true,
                    "sql": "select cod_colectivo as id, des_colectivo as nombre from cug_colectivos where cod_colectivo>0"
                },
                {
                    "label": "Parametro lista simple proveniente de BDD",
                    "name": "cod_estado_civil",
                    "tipo": "list_db",
                    "multiple": false,
                    "sql": "select cod_estado_civil as id, des_estado_civil as nombre from cug_estado_civil"
                },
                {
                    "label": "Parametro color",
                    "name": "val_color",
                    "tipo": "color"
                },
                {
                    "label": "Parametro con lista simple estatica",
                    "name": "cod_motivo",
                    "tipo": "list",
                    "multiple": false,
                    "list": "Motivo1,Motivo2,Motivo3,Motivo4,Motivo5,Motivo6",
                    "values": "1,2,3,4,5,6"
                },
                {
                    "label": "Parametro con lista multiple estatica",
                    "name": "cod_motivo",
                    "tipo": "list",
                    "multiple": true,
                    "list": "Motivo1,Motivo2,Motivo3,Motivo4,Motivo5,Motivo6",
                    "values": "1,2,3,4,5,6"
                }
            ]
        }';
        return $params;
     }

     static function signature(){

        return "task:ejemplo";
     }

    static function definicion(){

        return "Este es un comando de ejemplo que puede servir como plantilla para la creacion de nuevos comandos. En esta campo va la descripcion que el usuario verá cuando selecciona el comando. El objetivo de esta es explicar al usuario que hace el comando y ".
        "como se puede parametrizar, intentando dar el mayor detalle posible a los valores que pueden tomar los distintos parametros";
     }

     static function grupo(){

        return "A";
     }

     function escribelog_comando_comando($tipo,$mensaje){
        Log::$tipo($mensaje);
        if($this->argument('origen')=='W')
        {
            log_tarea($mensaje,$this->argument('id'),$tipo);
        }
    } 


    public function handle()
    {
        //Aqui es donde hay que poner el meollo de la tarea, es decir, el codigo a ejecutar.
        //La funcion Log registrará lo que se quiera en el log de laravel, el log de la tarea que se guarda en /storage/tareas
        //Las posibilidaddes de log son:  error    warning  debug    info   critical   notice    alert
        $this->escribelog_comando_comando('info','Inicio de la tarea programada ['.$this->argument('id').']'.__CLASS__); //__CLASS__ pone el nombre de la tarea
        //Sacamos los parametros de la tarea
        $tarea=tareas::find($this->argument('id'));
        $parametros=json_decode($tarea->val_parametros);
        //Esta es la forma de recoger cualquiera de los parametros de la tarea
        $num_dias=valor($parametros,"num_dias");
        /////////////////////////////////////////////////////
        //          CODIGO PRINCIPAL DE LA TAREA           //
        ////////////////////////////////////////////////////7
        //Actualiza la fechad de ultima ejecucion de la tarea
        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando_comando('info','Fin de la tarea '.__CLASS__);
    }
}
