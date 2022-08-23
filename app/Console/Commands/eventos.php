<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\cucoWEB;
use App\Models\evolucion;
use Illuminate\Support\Facades\Log;

class eventos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:eventos {id?} {origen=C} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando que evalua la ejecucion de eventos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    static function params(){
        $params='{
            "parametros":[
                {
                    "label": "Grupo de ejecucion",
                    "name": "cod_grupo_ejecucion",
                    "tipo": "list",
                    "multiple": false,
                    "list": "Todos|Minuto|Hora|Dia|Semana|Quincena|grupo A|grupo B|grupo C|grupo D|grupo E|grupo F",
                    "values": "*|I|H|D|S|Q|A|B|C|D|E|F",
                    "def": "*",
                    "required": false
                }
            ]
        }';
        return $params;
     }

     static function signature(){

        return "task:eventos";
     }

    static function definicion(){

        return "Esta tarea evalua y ejecuta los eventos programados";
     }

     static function grupo(){

        return "A";
     }

     static function log_evento($texto,$cod_regla,$tipo="info"){
        DB::table('eventos_log')->insert([
            'fec_log'=>Carbon::now(),
            'txt_log'=>substr($texto,0,5000),
            'cod_regla'=>$cod_regla,
            'tip_mensaje'=>$tipo
        ]);
     }

     public function scope(){
        return "events";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Las posibilidaddes de log son:  error    warning  debug    info   critical   notice    alert
        //Sacamos los parametros de la tarea
        Log::info('Inicio de la tarea programada ['.$this->argument('id').']'.__CLASS__);
        try{
            $tarea=tareas::findorfail($this->argument('id'));
            ini_set('max_execution_time', $tarea->val_timeout);
        } catch(\Exception $e) {
            Log::error('No existe la tarea '.$this->argument('id'));
            exit();
        }

        $parametros=json_decode($tarea->val_parametros);
        $grupo=valor($parametros,"cod_grupo_ejecucion");
        Log::debug('Grupo '.$grupo);
        //Primero vamos a obtener los eventos que tenemos que evaluar
        $evento=DB::table('eventos_reglas')
        ->where(function($qr) use($grupo){
            if($grupo!='*'){
                $qr->where('cod_grupo',$grupo);
            }
        })
        ->where('fec_inicio','<=',Carbon::now())
        ->where('fec_fin','>=',Carbon::now())
        ->where('mca_activa','S')
        ->where( function($q) {
            $q->where('fec_prox_ejecucion','<',Carbon::now());
            $q->orWhereNull('fec_prox_ejecucion');
        })
        ->first();

        if(isset($evento)){
            Log::debug('Encontrados evento');
            Log::notice('['.$evento->cod_regla.'] -> Regla: '.$evento->nom_regla);
            //Siguiente paso, a ver si estamos dentro de las horas de programacion de la regla
            //->setTimezone($evento->timezone)
            $hora=Carbon::now()->setTimezone("Europe/Madrid")->format('H');
            $dia=Carbon::now()->setTimezone("Europe/Madrid")->dayOfWeek;
            if($dia==0) $dia=7; //DOmingo en ingles
            Log::notice('Dia: '.$dia." Hora: ".$hora);
            $programacion=decodeComplexJson(decodeComplexJson($evento->schedule));
            $toca_programacion=false;
            foreach($programacion as $prog){
                if($prog->num_dia==$dia){
                    foreach($prog->horas as $h){
                        if($h==$hora){
                            $toca_programacion=true;
                        }
                    }
                }
            }
            if (!$toca_programacion){
                Log::error("La regla no esta programada para ser ejecutada el día ".$dia." en la hora ".$hora);
                exit();
            }
            $this->log_evento('Iniciando proceso de la regla dentro del horario de programacion',$evento->cod_regla);
            //Ahora leemos el fichero de la regla
            Log::debug('Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando);
            $output=$this->output;
            try{
                include_once(resource_path('views/events/comandos').'/'.$evento->nom_comando);
                $resultado_json=ejecutar($evento,$output);
                $resultado=json_decode($resultado_json);
                try{
                    $campos=json_decode($campos);
                    $campos=$campos->campos;
                } catch(\Throwable $e){
                    $campos=[];
                }
                if(sizeof($resultado->lista_id)>0){
                    $this->log_evento('Comando ejecutado, '.count($resultado->lista_id).' ID a procesar: '.implode(",",$resultado->lista_id),$evento->cod_regla);
                    Log::debug("ID a procesar: ".implode(",",$resultado->lista_id));
                } else{
                    $this->log_evento('Comando ejecutado, no hay ID para procesar',$evento->cod_regla);
                    Log::debug("No hay ID para procesar");
                }
    
                Log::notice("Resultado del comando: ".$resultado->respuesta);
            } catch(\Throwable $e){
                if(config('app.env')=="local"){
                    dump($e);
                }
                Log::error('Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando.', no encontrado');
                $this->log_evento('Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando.', no encontrado',$evento->cod_regla,'error');
                $resultado=json_decode(json_encode([
                    "respuesta" => "ERROR",
                    "comando" => $evento->nom_comando,
                    "tipo_id" => "void",
                    "table" => "void",
                    "campo" => "void",
                    "lista_id" =>  [],
                    "data" =>[],
                    "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ]));
            }
            


            //Sacamos la cuenta total de iteraciones para saber cuando acaba
            $max_iteracion=DB::table('eventos_acciones')->where('cod_regla',$evento->cod_regla)->max('val_iteracion');
            Log::debug('Iteraciones maximas de la regla: '.$max_iteracion);
            //Sacamos las acciones para la iteracion
            $acciones=DB::table('eventos_acciones')->where('cod_regla',$evento->cod_regla)->wherenotnull('nom_accion')->get();
            //Y sacamos la iteracion en que que esta cada ID
            $evolucion=DB::table('eventos_evolucion_id')->where('cod_regla',$evento->cod_regla)->get();
            //Vamos a hacer las acciones
            foreach($resultado->lista_id as $id){
                $evolucion_id=$evolucion->where('id',$id)->where('val_iteracion','<=',$max_iteracion)->first();
                if(!isset($evolucion_id)){
                    $num_iteracion=1;
                } else {
                    $num_iteracion=$evolucion_id->val_iteracion+1;
                }
                $acciones_toca=$acciones->where('val_iteracion',$num_iteracion)->sortby("nom_orden");
                foreach($acciones_toca as $accion){
                    try{
                        Log::debug("ID: ".$id." | Iteracion: " .$num_iteracion." | Accion[".$accion->num_orden."]: ".$accion->nom_accion);
                        $this->log_evento("ID: ".$id." | Iteracion: " .$num_iteracion." | Accion[".$accion->num_orden."]: ".$accion->nom_accion,$evento->cod_regla);
                        include(resource_path('views/events/acciones').'/'.$accion->nom_accion);
                        //Ejecutamos la funcion principal de cada accion
                        $func_accion($accion,$resultado,$campos,$id);
                        //Se elimina la funcion por si hay mas acciones en la misma regla
                        unset($func_accion);
                    } catch(\Throwable $e){
                        if(config('app.env')=="local"){
                            dump($e);
                        }
                        Log::error('Error al ejecutar la accion :'.$accion->nom_accion.', '.mensaje_excepcion($e));
                        $this->log_evento('Error al ejecutar la accion :'.$accion->nom_accion.', '.mensaje_excepcion($e),$evento->cod_regla,'error');
                    }
                }
                if($num_iteracion==1){ //Estamos en la primera ya hay que insertar en la tabla de evolucion para ir progresandola
                    DB::table('eventos_evolucion_id')->insert([
                        "cod_regla"=>$evento->cod_regla,
                        "val_iteracion"=>$num_iteracion,
                        "id"=>$id,
                        "fecha"=>Carbon::now()
                    ]);
                    Log::debug("Primera iteracion");
                } else if($num_iteracion>=$max_iteracion){ //Ha llegado al tope, la borramos de la tabla para en la siguiente volver a empezar
                    Log::debug("Superado el maximo de iteraciones, borrando evolucion");
                    $this->log_evento("Superado el maximo de iteraciones, borrando evolucion",$evento->cod_regla);
                    DB::table('eventos_evolucion_id')->where('cod_regla',$evento->cod_regla)->where('id',$id)->delete();
                    //Ahora si la regla tiene un no molestar en X horas lo ponemos
                    if(isset($evento->nomolestar)){
                        Log::debug('Añadido no molestar para '.$id.' hasta dentro de '.$evento->nomolestar.' horas');
                        $this->log_evento('Añadido no molestar para '.$id.' hasta dentro de '.$evento->nomolestar.' horas',$evento->cod_regla);
                        DB::table('eventos_noactuar')->where('cod_regla',$evento->cod_regla)->where('id',$id)->delete();
                        //Dependiendo de la unidad de tiempo de nomolestar
                        if($evento->tip_nomolestar=='H'){
                            $fecha_noactuar=Carbon::now()->addHours($evento->nomolestar);
                        } else if($evento->tip_nomolestar=='D'){
                            $fecha_noactuar=Carbon::now()->addDays($evento->nomolestar);
                        } else if($evento->tip_nomolestar=='M'){
                            $fecha_noactuar=Carbon::now()->addMonths($evento->nomolestar);
                        } else if($evento->tip_nomolestar=='Y'){
                            $fecha_noactuar=Carbon::now()->addYears($evento->nomolestar);
                        }
                        DB::table('eventos_noactuar')->insert([
                            "cod_regla"=>$evento->cod_regla,
                            "id"=>$id,
                            "fecha"=>$fecha_noactuar,
                        ]);
                    }
                } else { //Estamos a mitad del fregao, aumentamos el numero de iteracion y listo
                    DB::table('eventos_evolucion_id')->where('cod_regla',$evento->cod_regla)->where('id',$id)->update([
                        "val_iteracion"=>$num_iteracion,
                    ]);
                }
            }
        Log::info('Finalizado el procesado de la regla '.$evento->nom_regla);
        $this->log_evento('Finalizado el procesado de la regla ',$evento->cod_regla);
        //Actualizamos la fecha de ultima y proxima ejecucion ejecucion de la regla
        DB::table('eventos_reglas')->where('cod_regla',$evento->cod_regla)->update([
            'fec_ult_ejecucion'=>Carbon::now(),
            'fec_prox_ejecucion'=>Carbon::now()->addMinutes($evento->intervalo)
            ]);
        $this->log_evento('Proxima ejecucion establecida para  '.Carbon::now()->addMinutes($evento->intervalo)->toString(),$evento->cod_regla);
        } else {
            Log::info('No hay eventos para evaluar');
        }
        Log::info('Fin de la tarea '.__CLASS__);
    }
}





//Ahora tenemos que saber en que iteracion estamos
// $iter=evolucion::where('cod_regla',$evento->cod_regla)->where('mca_fin','N')->first();
// if(!$iter){ //regla nueva -> Primera iteracion
//     $iter=new evolucion();
//     $iter->cod_regla=$evento->cod_regla;
//     $iter->fec_inicio=Carbon::now();
//     $iter->fec_iteracion=Carbon::now();
//     $iter->data1=$resultado_json;
//     $iter->save();
//     $iter=evolucion::where('cod_regla',$evento->cod_regla)->where('mca_fin','N')->first();
// }  else {
//     //Ya estaba, actualizamos el data correspondiente a la iteracion y la fecha
//     $campo_data="data".$iter->val_iteracion;
//     $iter->fec_iteracion=Carbon::now();
//     $iter->$campo_data=$resultado_json;
//     $iter->save();
// }
// $iteracion_actual=$iter->val_iteracion;





// foreach($acciones as $accion){
//     $this->line('Accion['.$accion->num_orden.'] '.$accion->nom_accion );
//     include_once(resource_path('views/events/acciones').'/'.$accion->nom_accion);
//     //Ejecutamos la funcion principal de cada accion
//     $func_accion($accion,$resultado,$campos);
//     //Se elimina la funcion por si hay mas acciones en la misma regla
//     unset($func_accion);
// }
// //Aumentamos el numero de iteracion
// $iter->val_iteracion=$iteracion_actual+1;
// $iter->save();
// //Si estamos en la ultima iteracion, cerramos la evolucion de la regla
// if($max_iteracion==$iteracion_actual){
//     //Hemos llegado a la ultima iteracion, fin de la regla
//     $this->info('Fin de la evaluacion de la regla');
//     $iter->mca_fin="S";
//     $iter->save();
// }
