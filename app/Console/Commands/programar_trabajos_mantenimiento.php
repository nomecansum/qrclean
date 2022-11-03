<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use App\Models\users;
use App\Models\rondas;
use App\Models\puestos_ronda;
use App\Models\puestos;
use App\Models\limpiadores;
use App\Models\trabajos_programacion;
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
            ->join('trabajos','trabajos.id_trabajo','trabajos_planes_detalle.id_trabajo')
            ->join('trabajos_planes','trabajos_planes.id_plan','trabajos_planes_detalle.id_plan')
            ->where(function($q) use($tarea){
                if(isset($tarea->clientes) && $tarea->clientes!=''){
                    $lista_clientes=explode(',',$tarea->clientes);
                    $q->wherein('trabajos_planes.id_cliente',$lista_clientes);
                }
            })
            ->where('trabajos_planes_detalle.mca_activa','S')
            ->where('trabajos_planes.mca_activo','S')
            ->get();
        //Ahora para cada uno de ellos a ver si tenemos al menos tantos dias como diga su plan que hay que tener
        foreach($trabajos as $t){
            try{
                $this->escribelog_comando('notice','Trabajo: ['.$t->id_trabajo.'] '.$t->des_trabajo.' '.$t->val_periodo);
                $programaciones=DB::table('trabajos_programacion')
                    ->where('id_trabajo_plan',$t->key_id)
                    ->where('id_plan',$t->id_plan)
                    ->where('fec_programada','>=',Carbon::now())
                    ->orderby('fec_programada')
                    ->get();
                $cuenta=0;
                $fec_inicio=$programaciones->first()->fec_programada??Carbon::now();
                $fec_fin=$programaciones->last()->fec_programada??Carbon::now();
                $proximas_fechas=next_cron($t->val_periodo,1000,Carbon::parse($fec_fin)->format('Y-m-d H:i:s'));
                foreach($proximas_fechas as $f){
                    $fec_fin=Carbon::parse($f);
                    $programacion=new trabajos_programacion();
                    $programacion->id_trabajo_plan=$t->key_id;
                    $programacion->id_plan=$t->id_plan;
                    $programacion->fec_programada=$fec_fin;
                    $programacion->val_tiempo_estimado=$t->val_tiempo;
                    $programacion->save();
                    $this->escribelog_comando('debug','Programada fecha: '.$f.' dias previstos '.Carbon::parse($fec_fin)->diffinDays(Carbon::parse($fec_inicio)));
                    $cuenta++;
                    if(Carbon::parse($fec_fin)->diffinDays(Carbon::parse($fec_inicio))> $t->num_dias_programar){
                        break;
                    }
                }
            } catch(\Throwable $e){
                $this->escribelog_comando('error','Error creando programacion para el trabajo: '.$t->id_trabajo.' '.$t->des_trabajo.' '.$t->val_periodo.': '.$e->getMessage());
            }
            $this->escribelog_comando('info','Se han programado '.$cuenta.' instancias del trabajo ['.$t->id_trabajo.'] '.$t->des_trabajo);
        }
        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}
