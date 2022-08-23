<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use App\Models\users;
use App\Models\puestos_ronda;
use App\Models\puestos;
use App\Models\puestos_tipos;
use App\Models\reservas;
use App\Models\plantas_zonas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use stdClass;

class crear_reservas_turnos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:crear_reservas_turnos {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esta tarea crea las reservas para los usuarios seleccionados en base al turno que tengan configurado. En cada ejecucion se ocupará de que los usuarios tengan creadas, para cada tipo de puesto seleccionado, tantas reservas en el futuro como dias indique el parámetro "Mantener reservas para dias".';

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
        if($this->argument('origen')=='W')
        {
            
        }
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
                    "label": "Mantener reservas para dias",
                    "name": "dias_reservas",
                    "tipo": "num",
                    "def": "",
                    "required": true
                },
                {
                    "label": "Tipo de puesto",
                    "name": "id_tipo_puesto",
                    "tipo": "list_db",
                    "multiple": true, 
                    "sql": "SELECT DISTINCT \n
                                `puestos_tipos`.`id_tipo_puesto` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`puestos_tipos`.`des_tipo_puesto`) as nombre  \n
                            FROM \n
                                `puestos_tipos` \n
                                INNER JOIN `clientes` ON (`puestos_tipos`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            `puestos_tipos`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
                            ORDER BY 2", 
                    "required": false,
                    "buscar": true
                },
                {
                    "label": "Perfil",
                    "name": "id_perfil",
                    "tipo": "list_db",
                    "multiple": true, 
                    "sql": "SELECT DISTINCT \n
                                `niveles_acceso`.`cod_nivel` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`niveles_acceso`.`des_nivel_acceso`) as nombre  \n
                            FROM \n
                                `niveles_acceso` \n
                                INNER JOIN `clientes` ON (`niveles_acceso`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            (`niveles_acceso`.`id_cliente` in('.limpiarPuestos::clientes().') or niveles_acceso.mca_fijo=\'S\') \n
                            ORDER BY 2", 
                    "required": false,
                    "buscar": true
                },
                {
                    "label": "Edificio",
                    "name": "id_edificio",
                    "tipo": "list_db",
                    "multiple": true, 
                    "sql": "SELECT DISTINCT \n
                                `edificios`.`id_edificio` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`edificios`.`des_edificio`) as nombre  \n
                            FROM \n
                                `edificios` \n
                                INNER JOIN `clientes` ON (`edificios`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            `edificios`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
                            ORDER BY 2", 
                    "required": false,
                    "buscar": true
                },
                {
                    "label": "Turno",
                    "name": "id_turno",
                    "tipo": "list_db",
                    "multiple": true, 
                    "sql": "SELECT DISTINCT \n
                                `turnos`.`id_turno` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`turnos`.`des_turno`) as nombre  \n
                            FROM \n
                                `turnos` \n
                                INNER JOIN `clientes` ON (`turnos`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            `turnos`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
                            ORDER BY 2", 
                    "required": false,
                    "buscar": true
                },
                {
                    "label": "Colectivo",
                    "name": "id_colectivo",
                    "tipo": "list_db",
                    "multiple": true, 
                    "sql": "SELECT DISTINCT \n
                                `colectivos`.`cod_colectivo` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`colectivos`.`des_colectivo`) as nombre  \n
                            FROM \n
                                `colectivos` \n
                                INNER JOIN `clientes` ON (`colectivos`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            `colectivos`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
                            ORDER BY 2", 
                    "required": false,
                    "buscar": true
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

        return "task:crear_reservas_turnos";
     }

    static function definicion(){

        return 'Esta tarea crea las reservas para los usuarios seleccionados en base al turno que tengan configurado. En cada ejecucion se ocupará de que los usuarios tengan creadas tantas reservas en el futuro como dias indique el parámetro "dias_reserva".';
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
        $id_edificio=valor($parametros,"id_edificio");
        $id_perfil=valor($parametros,"id_perfil");
        $id_turno=valor($parametros,"id_turno");
        $id_colectivo=valor($parametros,"id_colectivo");
        $id_tipo_puesto=valor($parametros,"id_tipo_puesto");
        $dias_reservas=valor($parametros,"dias_reservas");
       
        $timezone=users::find($tarea->usu_audit)->val_timezone;
        if($tarea->clientes!=null){
            $cliente=explode(",",$tarea->clientes)[0];
        } else {
            $cliente=users::find($tarea->usu_audit)->id_cliente;
        }

        //Primero vamos a buscar los usuarios a los que les toca
        $usuarios=users::where('id_cliente',$cliente)
            ->select('users.id','users.name','users.list_puestos_preferidos')
            ->where(function($q) use($id_edificio){
                if ($id_edificio) {
                    $q->WhereIn('users.id_edificio',$id_edificio);
                }
            })
            ->where(function($q) use($id_perfil){
                if ($id_perfil) {
                    $q->WhereIn('users.cod_nivel',$id_perfil);
                }
            })
            ->when($id_turno, function($q) use ($id_turno){
                $q->join('turnos_usuarios','turnos_usuarios.id_usuario','users.id');
                $q->WhereIn('turnos_usuarios.id_turno',$id_turno);
            })
            ->when($id_colectivo, function($q) use($id_colectivo){
                $q->join('colectivos_usuarios','colectivos_usuarios.id_usuario','users.id');
                $q->WhereIn('colectivos_usuarios.cod_colectivo',$id_colectivo);
            })
            ->distinct()
            ->get();
        
        $this->escribelog_comando('info','Encontrados '.count($usuarios).' usuarios para procesar');
        $periodo=CarbonPeriod::create(Carbon::now(),Carbon::now()->addDays($dias_reservas));
        foreach($usuarios as $user){
            $this->escribelog_comando('debug','Procesando usuario ['.$user->id.'] '.$user->name);
            foreach($id_tipo_puesto as $tipo){
                $tipopuesto=puestos_tipos::find($tipo);
                $this->escribelog_comando('debug','Procesando tipo ['.$tipopuesto->id_tipo_puesto.'] '.$tipopuesto->des_tipo_puesto);
                foreach($periodo as $fecha){
                    $this->escribelog_comando('debug','Procesando fecha '.$fecha->format('Y-m-d'));
                    //A ver si es festivo o fin de semana y no puede reservar
                    $estadefiesta=collect(DB::select(DB::raw("select estadefiesta(".$user->id.",'".$fecha->format('Y-m-d')."') as estadefiesta ")))->first()->estadefiesta;
                    if($estadefiesta==1){
                        $this->escribelog_comando('info','El usuario '.$user->name.' no puede reservar en la fecha '.$fecha->format('Y-m-d').' porque es festivo');
                        continue;
                    }

                    //A ver que turno tiene el usuario
                    $turnos_usuario=DB::table('turnos_usuarios')
                        ->join('turnos','turnos.id_turno','turnos_usuarios.id_turno')
                        ->where('id_usuario',$user->id)
                        ->wheredate('fec_inicio','<=',$fecha->year(now()->format('Y')))
                        ->wheredate('fec_fin','>=',$fecha->year(now()->format('Y')))
                    ->get();
                    //ahora a ver si aplica el turno
                    $turno_aplica=null;
                    $indice=null;
                    foreach($turnos_usuario as $turno){
                        $dias=json_decode($turno->dias_semana);
                        foreach($dias->dia as $key=>$value){
                            if($value==$fecha->dayOfWeekIso && (Carbon::now()->weekOfYear % 2==$dias->mod_semana[$key] || $dias->mod_semana[$key]==-1)){
                                $turno_aplica=$dias;
                                $indice=$key;
                                break;
                            }
                        }
                    }
                    if(!isset($turno_aplica)){
                        $this->escribelog_comando('warning','No se ha encontrado un turno valido para el usuario '.$user->name.' en la fecha '.$fecha->format('d/m'));
                    } else {
                        //Tenemos turno, a jugar
                        //Ahora comprobamos si el tipo de puesto tiene slots
                        if(isset($tipopuesto->slots_reserva)){
                            $slots=json_decode($tipopuesto->slots_reserva);
                        } else {
                            //Horas en las que hay que reservar. Como no tiene slots se cogen las horas del turno como inicio y fiin de la reserva
                            $item=new stdClass();
                            $item->hora_inicio=$turno_aplica->hora_inicio[$indice];
                            $item->hora_fin=$turno_aplica->hora_fin[$indice];
                            $slots[]=$item;
                        }
                        foreach($slots as $slot){
                            $this->escribelog_comando('debug','Slot: '.$slot->hora_inicio.' '.$slot->hora_fin);
                            //A ver si ya tenia un puesto reservado en esa fecha, en ese caso, rompemos el buicle y saltamos a la sigueinte fecha
                            $tiene_reserva=comprobar_reserva_usuario($user->id,$fecha,$tipo,$slot->hora_inicio,$slot->hora_fin);
                            if($tiene_reserva===true){
                                $this->escribelog_comando('info','El usuario '.$user->name.' ya tiene una reserva en la fecha '.$fecha->format('Y-m-d').' de un puesto '.$tipopuesto->des_tipo_puesto);
                                break;
                            }
                            
                            //A ver que puestos hay disponibles
                            $puestos_disponibles=puestos_disponibles($cliente,$fecha,$tipo,$slot->hora_inicio,$slot->hora_fin);
                            //Reglas de reserva
                            $puestos_usuario=json_decode($user->list_puestos_preferidos);
                            $p=null;
                            foreach($puestos_usuario as $puesto){
                                switch ($puesto->tipo) {   //ul,pu,pl,zo
                                    case 'ul':  //Ultimas 20 reservas. Se cogen las ultimas 20 reservas del tipo de puesto y se selecciona el mas usado
                                        $ultimas_reservas=DB::table('reservas')
                                            ->select('reservas.id_puesto')
                                            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                                            ->where('id_usuario',$user->id)
                                            ->where('id_tipo_puesto',$tipo)
                                            ->orderby('fec_reserva','desc')
                                            ->take(20)
                                            ->get();
                                        $ultimas_reservas=$ultimas_reservas->countBy('id_puesto')->sortDesc();
                                        $p=collect(array_intersect($puestos_disponibles->pluck('id_puesto')->toArray(),$ultimas_reservas->keys()->toArray()))->first();

                                    case 'pu':  //Puestos preferidos. Se selecciona el puesto que esta en la lista de puestos preferidos
                                        $puesto_preferido=[$puesto->id];
                                        $p=collect(array_intersect($puestos_disponibles->pluck('id_puesto')->toArray(),$puesto_preferido))->first();
                                        break;
                                    case 'pl':
                                        $puestos_planta=DB::table('puestos')
                                            ->select('id_puesto')
                                            ->where('id_planta',$puesto->id)
                                            ->pluck('id_puesto')
                                            ->toArray();
                                        $p=collect(array_intersect($puestos_disponibles->pluck('id_puesto')->toArray(),$puestos_planta))->first();
                                        break;
                                    case 'zo':
                                        $puestos_zona=DB::table('puestos')
                                            ->select('id_puesto','plantas.id_planta','plantas_zonas.val_ancho','plantas_zonas.val_alto','plantas_zonas.val_x','plantas_zonas.val_y','puestos.offset_top','puestos.offset_left','plantas.width','plantas.height')
                                            ->join('plantas_zonas','plantas_zonas.id_planta','puestos.id_planta')
                                            ->join('plantas','plantas.id_planta','plantas_zonas.id_planta')
                                            ->where('plantas_zonas.key_id',$puesto->id)
                                            ->where(function($q){  //Este rollo viene de que las posiciones de las zonas estan guardadas como valores absolutos sobre los pixeles reales de la imagen y las posiciones de los puestos estan guardadas como valores de porcentaje (offsettop y offsetleft) de las dimensiones de la imagen
                                                $q->whereraw('puestos.offset_top between (100*plantas_zonas.val_y/plantas.`height`) AND ((100*plantas_zonas.val_y/plantas.`height`) + (100*plantas_zonas.val_alto/plantas.`height`))');
                                                $q->whereraw('puestos.offset_left between (100*plantas_zonas.val_x/plantas.`width`) AND ((100*plantas_zonas.val_x/plantas.`width`) + (100*plantas_zonas.val_ancho/plantas.`width`))');
                                            })
                                            ->pluck('id_puesto')
                                            ->toArray();
                                        $p=collect(array_intersect($puestos_disponibles->pluck('id_puesto')->toArray(),$puestos_zona))->first();
                                        break;
                                }
                                //Si ya hemos encontrado puesto, no seguimos mirando, a reservar
                                if($p!==null){
                                    break;
                                }
                            }

                            if($p!==null){
                                //Puesto disponible, reservar
                                $this->escribelog_comando('info','Reservando puesto '.$p.' para el usuario '.$user->name.' en la fecha '.$fecha->format('d/m'));
                                $reserva=new reservas();
                                $reserva->id_usuario=$user->id;
                                $reserva->id_cliente=$cliente;
                                $reserva->id_puesto=$p;
                                $reserva->fec_reserva=Carbon::parse(Carbon::parse($fecha)->format('Y-M-d').' '.$slot->hora_inicio.':00');
                                $reserva->fec_fin_reserva=Carbon::parse(Carbon::parse($fecha)->format('Y-M-d').' '.$slot->hora_fin.':00');
                                $reserva->save();
                                enviar_mail_reserva($reserva->id_reserva,'N','Tarea de creacion de reservas');
                                $this->escribelog_comando('info','Reserva realizada');
                                break; //<-- Aqui se sale del bucle de slots porque ya tenemos puesto reservado
                            } else {
                                $this->escribelog_comando('warning','No se ha encontrado un puesto del tipo ['.$tipopuesto->des_tipo_puesto.'] para crear la reserva para el usuario '.$user->name.' en la fecha '.$fecha->format('d/m'));
                            }
                        }
                        
                    }
                }
            }
        }

        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}