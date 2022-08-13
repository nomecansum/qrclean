<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\cucoWEB;
use Illuminate\Support\Facades\Log;

class TurnosPuestos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:TurnosPuestos {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE
    //Para probar una tarea se debe ejecutar  php artisan task:<NOMBREDETAREA> <IDDETAREA>

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esta tarea genera reservas para todos los usuarios en funcion del turno de asistencia que tienen asignado. Se gnerarán tantos dias por delante de la fecha actual como se indique en el parametro  de dias de la tarea';

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
                    "label": "Generar para dias",
                    "name": "dias",
                    "tipo": "num",
                    "def": "15",
                    "required": true
                },
                {
                    "label": "Poniendo este parametro a true, indicaremos que necesariamente se debe seleccionar un cliente",
                    "name": "control_cliente",
                    "tipo": "cli",
                    "required": true
                }
            ]
        }';
        return $params;
     }

     static function signature(){

        return "task:ejemplo";
     }

    static function definicion(){

        return "Esta tarea genera reservas para todos los usuarios en funcion del turno de asistencia que tienen asignado. Se gnerarán tantos dias por delante de la fecha actual como se indique en el parametro  de dias de la tarea";
     }

     static function grupo(){

        return "A";
     }

     function escribelog_comando_comando($tipo,$mensaje){
        Log::$tipo($mensaje);
        log_tarea($mensaje,$this->argument('id'),$tipo);
        if($this->argument('origen')=='W')
        {
           
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
        $val_minutos=valor($parametros,"dias");
        /////////////////////////////////////////////////////
        //          CODIGO PRINCIPAL DE LA TAREA           //
        ////////////////////////////////////////////////////7


        //Actualiza la fechad de ultima ejecucion de la tarea
        $this->escribelog_comando_comando('info','YO estuve aqui');
        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando_comando('info','Fin de la tarea '.__CLASS__);
    }
}
