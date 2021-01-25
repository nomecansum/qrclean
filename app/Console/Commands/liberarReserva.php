<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\cucoWEB;
use Illuminate\Support\Facades\Log;

class liberarReserva extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:liberarReserva {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE
    //Para probar una tarea se debe ejecutar  php artisan task:<NOMBREDETAREA> <IDDETAREA>

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando marca como disponibles los puestos reservados que no esten ocupados despues de N minutos (paramnetrizable) de su hora de reserva';

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
                    "label": "Minutos de cortesia",
                    "name": "val_minutos",
                    "tipo": "num",
                    "def": "15"
                },
                {
                    "label": "Aplicar a puestos del tipo",
                    "name": "tipos_aplicar",
                    "tipo": "list_db",
                    "multiple": true,
                    "sql": "select id_tipo_puesto as id, des_tipo_puesto as nombre from puestos_tipos"
                },
                {
                    "label": "No aplicar a puestos del tipo",
                    "name": "tipos_noaplicar",
                    "tipo": "list_db",
                    "multiple": true,
                    "sql": "select id_tipo_puesto as id, des_tipo_puesto as nombre from puestos_tipos"
                }
            ]
        }';
        return $params;
     }

     static function signature(){

        return "task:liberarReserva";
     }

    static function definicion(){

        return "Este comando marca como disponibles los puestos reservados que no esten ocupados despues de N minutos (Minutos de cortesia) de su hora de reserva";
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
        $val_minutos=valor($parametros,"val_minutos");
        /////////////////////////////////////////////////////
        //          CODIGO PRINCIPAL DE LA TAREA           //
        ////////////////////////////////////////////////////7   
        //Primero sacar aquellas reservas 
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->where(function($q) use($tarea){
                if(isset($tarea->clientes) && $tarea->clientes!=''){
                    $lista_clientes=explode(',',$tarea->clientes);
                    $q->wherein('id_clientes',$lista_clientes);
                }
            })
            ->wheredate('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->where('fec_reserva','<=',Carbon::now()->addMinutes($val_minutos))
            
            ->wherenull('fec_utilizada')
            ->get();

        dd($reservas);

        //Actualiza la fechad de ultima ejecucion de la tarea
        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando_comando('info','Fin de la tarea '.__CLASS__);
    }
}
