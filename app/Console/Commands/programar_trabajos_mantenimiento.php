<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use App\Models\users;
use App\Models\rondas;
use App\Models\puestos_ronda;
use App\Models\puestos;
use App\Models\limpiadores;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class programar_trabajos_mantenimiento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:programar_trabajos_mantenimiento {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para marcar generar las tareas de limpieza y mantenimiento programadas de acuerdo a lo establecido en los planes de trabajo ';

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
        log_tarea($mensaje,$this->argument('id'),$tipo);
    }

    static function clientes(){
        if(is_array(clientes())){
            $clientes=implode(",",clientes());
        }   else {
            $clientes=implode(",",clientes()->ToArray());
        }
        return $clientes;
    }
    
    static function params(){

        $params='{
            "parametros":[
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

        return "task:programar_trabajos_mantenimiento";
     }

    static function definicion(){

        return "Tarea para marcar generar las tareas de limpieza y mantenimiento programadas de acuerdo a lo establecido en los planes de trabajo ";
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
        //$horas=valor($parametros,"horas");
        try{
            $timezone=users::find($tarea->usu_audit)->val_timezone;
        } catch(\Throwable $e){
            $timezone='Europe/Madrid';
        }
        
        if($tarea->clientes!=null){
            $cliente=explode(",",$tarea->clientes)[0];
        } else {
            $cliente=users::find($tarea->usu_audit)->id_cliente;
        }

        //Primero vamos a sacar las tareas que tiene el cliente en sus planes
        $trabajos = DB::table('trabajos_planes_detalle')
            ->join('trabajos_planes','trabajos_planes.id_plan','trabajos_planes_detalle.id_plan')
            ->where(function($q) use($tarea){
                if(isset($tarea->clientes) && $tarea->clientes!=''){
                    $lista_clientes=explode(',',$tarea->clientes);
                    $q->wherein('id_cliente',$lista_clientes);
                }
            })
            ->where('trabajos_planes_detalle.mca_activa','S')
            ->where('trabajos_planes.mca_activo','S')
            ->get();
        //Ahora para cada uno de ellos a ver si tenemos al menos tantos dias como diga su plan que hay que tener
        foreach($trabajos as $t){
            $programaciones=DB::table('trabajos_programacion')
                ->where('id_trabajo',$t->id_trabajo)
                ->where('id_grupo',$t->id_grupo_trabajo)
                ->where('id_plan',$t->id_plan)
                ->where('fec_programada','>=',Carbon::now())
                ->orderby('fec_programacion')
                ->get();
            $fec_inicio=$programaciones->first()->fec_programacion??Carbon::now();
            $fec_fin=$programaciones->last()->fec_programacion??Carbon::now();
            dd($t);
        }
       

        


        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}
