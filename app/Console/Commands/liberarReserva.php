<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use App\Models\puestos;
use App\Models\clientes;
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
        $val_minutos=valor($parametros,"val_minutos");
        /////////////////////////////////////////////////////
        //          CODIGO PRINCIPAL DE LA TAREA           //
        ////////////////////////////////////////////////////7   
        //Primero sacar aquellas reservas 
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','users.id','reservas.id_usuario')
            ->where(function($q) use($tarea){
                if(isset($tarea->clientes) && $tarea->clientes!=''){
                    $lista_clientes=explode(',',$tarea->clientes);
                    $q->wherein('id_clientes',$lista_clientes);
                }
            })
            ->wheredate('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->where('fec_reserva','<=',Carbon::now()->subMinutes($val_minutos))
            ->wherenotnull('fec_fin_reserva')
            ->wherenull('fec_utilizada')
            ->get();
        $this->escribelog_comando_comando('info','Encontradas '.$reservas->count().' anulables'); 
        foreach($reservas as $res){
            //Marcamos las reservas como anuladas y les enviamos un mail a los afectados
            $upd_res=DB::table('reservas')
            ->where('id_reserva',$res->id_reserva)
            ->update([
                'fec_utilizada'=>Carbon::now(),
                'mca_anulada'=>'S'
            ]);
            $this->escribelog_comando_comando('debug','Anulada reserva # '.$res->id_reserva.' de '.$res->name.' para el puesto '.$res->cod_puesto.' por no utilizarla a tiempo ['.Carbon::parse($res->fec_reserva)->format('d/m/Y H:i').'] -> ['.Carbon::now()->format('d/m/Y H:i').']  ('.Carbon::now()->diffForHumans(Carbon::parse($res->fec_reserva)).')'); 
            savebitacora('Anulada reserva # '.$res->id_reserva.' de '.$res->name.' para el puesto '.$res->cod_puesto.' por no utilizarla a tiempo ['.Carbon::parse($res->fec_reserva)->format('d/m/Y H:i').'] -> ['.Carbon::now()->format('d/m/Y H:i').']',"Tareas programadas","liberarReserva","OK");
            $body="Le notificamos que su reserva del puesto ".$res->cod_puesto." que tenía para hoy a las ".Carbon::parse($res->fec_reserva)->format('d/m/Y H:i')." ha sido anulada porque a las ".Carbon::now()->format('d/m/Y H:i')." (".Carbon::now()->diffForHumans(Carbon::parse($res->fec_reserva)).") no consta que haya hecho uso del puesto.<br>".chr(13);
            $body.="Si quiere seguir haciendo uso del puesto puede volver a reservarlo o acceder a el directamente, siempre que no haya sido reservado por otro usuario";
            notificar_usuario($res,'Anulacion de su reserva de puesto','emails.asignacion_puesto',$body,1);
        }

        $this->escribelog_comando_comando('info','Liberacion automatica de puestos usados'); 
        //Ahora buscamos los puestos que deben liberarse automaticamente
        $clientes_liberar=DB::table('config_clientes')
            ->where('mca_liberar_puestos_auto','S')
            ->pluck('id_cliente')
            ->toArray();
        $puestos_liberar=DB::table('puestos')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('users','users.id','puestos.id_usuario_usando')
            ->wherein('puestos.id_cliente',$clientes_liberar)
            ->where(function($q){
                $q->wherenull('fec_liberacion_auto');
                $q->orwhere('fec_liberacion_auto','<',Carbon::now());
            })
            ->where('id_estado',2)
            ->get();

        $this->escribelog_comando_comando('info','Encontrados '.$puestos_liberar->count().' puestos para liberar'); 
        foreach($puestos_liberar as $p){
           $puesto=puestos::find($p->id_puesto);
           $puesto->id_estado=1;
           $puesto->fec_ult_estado=Carbon::now();
           $puesto->id_usuario_usando=null;
           $puesto->fec_liberacion_auto=Carbon::parse(Carbon::now()->addDay(1)->format('Y-m-d').' '.$p->hora_liberar);
           $puesto->save();
           savebitacora('Liberado automaticamente puesto '.$p->cod_puesto.' ocupado por '.$p->name,"Tareas programadas","liberarReserva","OK");
        }
            

        //Actualiza la fechad de ultima ejecucion de la tarea
        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando_comando('info','Fin de la tarea '.__CLASS__);
    }
}
