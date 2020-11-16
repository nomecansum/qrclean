<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\cucoWEB;
use Illuminate\Support\Facades\Log;

class limpiar_bitacora extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:limpiar_bitacora {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para limpiar de las tablas de logs y bitacora de registros de mas de N dias';

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

    function escribelog_comando($tipo,$mensaje){
        Log::$tipo($mensaje);
        if($this->argument('origen')=='W')
        {
            log_tarea($mensaje,$this->argument('id'),$tipo);   
        }
    } 
    
    static function params(){
        $params='{
            "parametros":[
                {
                    "label": "Numero de dias bitacora",
                    "name": "num_dias_bitacora",
                    "tipo": "num",
                    "def": "60"
                },
                {
                    "label": "Numero de dias reservas",
                    "name": "num_dias_reservas",
                    "tipo": "num",
                    "def": "120"
                },
                {
                    "label": "Numero de dias LOG",
                    "name": "num_dias_log",
                    "tipo": "num",
                    "def": "20"
                }
            ]
        }';
        return $params;
     }

    static function signature(){

        return "task:limpiar_bitacora";
     }

    static function definicion(){

        return "Esta tarea borra las entradas de logs y bitacora mas antiguas del numero de dias determinado en el parámetro, por defecto, 60 días";
     }

     static function grupo(){

        return "A";
     }


    public function handle()
    {
        $this->escribelog_comando('info','Inicio de la tarea programada ['.$this->argument('id').']'.__CLASS__);
        //Sacamos los parametros de la tarea
        $tarea=tareas::find($this->argument('id'));
        $parametros=json_decode($tarea->val_parametros);
        $num_dias_bitacora=valor($parametros,"num_dias_bitacora");
        $num_dias_acciones=valor($parametros,"num_dias_reservas");
        $num_dias_log=valor($parametros,"num_dias_log");


        $this->escribelog_comando('debug','Bitacora - Dias a borrar, más de: '.$num_dias_bitacora);
        $this->escribelog_comando('debug','Borrando entradas de bitacora ');
        $affected=DB::table('bitacora')
        ->whereraw("fecha < DATE_SUB(now(), interval ".$num_dias_bitacora." DAY)")
        ->delete();
        $this->escribelog_comando('info',$affected.' filas borradas');

        $this->escribelog_comando('debug','LOGS - Borrando entradas de LOG');
        $affected=DB::table('log_cambios_estado')
        ->whereraw("fecha < DATE_SUB(now(), interval ".$num_dias_log." DAY)")
        ->delete();

        $this->escribelog_comando('debug','RESERVAS - Borrando entradas de RESERVAS');
        $affected=DB::table('reservas')
        ->whereraw("fec_reserva < DATE_SUB(now(), interval ".$num_dias_log." DAY)")
        ->delete();

        $this->escribelog_comando('info',$affected.' filas borradas');
        $affected=DB::table('tareas_programadas_log')
        ->whereraw("fec_log < DATE_SUB(now(), interval ".$num_dias_log." DAY)")
        ->delete();
        $this->escribelog_comando('info',$affected.' filas borradas');

        

        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}
