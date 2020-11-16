<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\puestos;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\logpuestos;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        

        $contenido_home=DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->home_page;
        $contenido_home='home.'.$contenido_home;
   

        return view('home',compact('contenido_home'));
    }

    public function getsitio(Request $r){

        if(isset($r->data)){
            $data=explode("/",$r->data);
            $data=end($data);
            dd($data);
           
        }

    }


    public function getpuesto($puesto){ 
        $p=DB::table('puestos')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$puesto)
            ->first();

        $config_cliente=null;
        
       
        //A ver si el usuario viene autentificado
        if(Auth::check())
            {
                $id_usuario=Auth::user()->id;
                $cod_nivel=Auth::user()->cod_nivel;
            } else {
                $id_usuario=0;
                $cod_nivel=0;
            }

        if(!isset($p)){
            //Error puesto no encontrado
            $respuesta=[
                'mensaje'=>"Error, puesto no encontrado",
                'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                'color'=>'danger',
                'operativo' => 0
            ];
            $reserva=null;
        } else {    
            $config_cliente=DB::table('config_clientes')->where('id_cliente',$p->id_cliente)->first();
        
            $disponibles=DB::table('puestos')
                ->select('cod_puesto','des_puesto','val_color','val_icono')
                ->where('id_cliente',$p->id_cliente)
                ->where('id_edificio',$p->id_edificio)
                ->where('id_planta',$p->id_planta)
                ->where('id_estado',1)
                ->get();

            //Ahora comprobamos si el puesto esta reservado por alguien distinto a el usuario
            $reserva=DB::table('reservas')
                ->where('id_puesto',$p->id_puesto)
                ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
                ->where('id_usuario','<>',$id_usuario)
                ->first();

            if($reserva){
                $respuesta=[
                    'mensaje'=>"PUESTO RESERVADO",
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }

            //Aqui vemos si el puesto lo tiene alguien permanentemente asignado
            $asignados_usuarios=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                ->join('users','users.id','puestos_asignados.id_usuario')  
                ->where('puestos.id_puesto',$p->id_puesto)  
                ->where('id_usuario','<>',$id_usuario)
                ->get();

            if(!$asignados_usuarios->isEmpty()){
                $respuesta=[
                    'mensaje'=>"PUESTO ASIGNADO PERMANENTEMENTE",
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }
            
            //Y aqui si el pñuesto esta reserrvado para un perfil en concreto
            $asignados_nomiperfil=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')   
                ->where('puestos.id_puesto',$p->id_puesto)    
                ->where('id_perfil','<>',$cod_nivel)
                ->get();

            if(!$asignados_nomiperfil->isEmpty()){
                $respuesta=[
                    'mensaje'=>"PUESTO RESERVADO P",
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }

            if($id_usuario==0 && $p->mca_acceso_anonimo=='N'){
                $respuesta=[
                    'mensaje'=>"PUESTO NO DISPONIBLE: Debe iniciar sesion para poder utilizar este puesto",
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }
            
            switch ($p->id_estado) {
                case 1:
                    $respuesta=[
                        'mensaje'=>"Puesto disponible",
                        'icono' => '<i class="fad fa-thumbs-up"></i>',
                        'color'=>'success',
                        'puesto'=>$p,
                        'operativo' => 1
                    ];
                    break;
                case 2:
                    $respuesta=[
                        'mensaje'=>"Puesto ocupado",
                        'icono' => '<i class="fad fa-lock-alt"></i>',
                        'color'=>'danger',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 1
                    ];
                    break;
                case 3:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible,debe ser limpiado",
                        'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                        'color'=>'warning',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 1
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO INOPERATIVO",
                        'icono' => '<i class="fad fa-bring-forward"></i>',
                        'color'=>'gray',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 0
                    ];
                    break;
                case 5:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO BLOQUEADO",
                        'icono' => '<i class="fad fa-bring-forward"></i>',
                        'color'=>'danger',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 0
                    ];
                    break;
                case 6:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO AVERIADO",
                        'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                        'color'=>'warning',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 0
                    ];
                    break;
                default:
                    # code...
                    break;
            }
        }
        //savebitacora('Cambio de puestos QR anonimo'.$p->id_puesto. ' a estado '.$p->id_estado,"Home","getpuesto","OK");
        return view('scan.result',compact('respuesta','reserva','config_cliente'));
    }



    public function estado_puesto($puesto,$estado){ 
        //A ver si el usuario viene autentificado
        if(Auth::check())
            {
                $id_usuario=Auth::user()->id;
                $cod_nivel=Auth::user()->cod_nivel;
            } else {
                $id_usuario=0;
                $cod_nivel=0;
            }
        
        $p=DB::table('puestos')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$puesto)
            ->first();
        $e=DB::table('estados_puestos')->where('id_estado',$estado)->first();
        if(!isset($p)){
            //Error puesto no encontrado
            $respuesta=[
                'tipo'=>'ERROR',
                'mensaje'=>"Error, puesto no encontrado"
            ];
        } else {    
            $reserva=DB::table('reservas')
                ->where('id_puesto',$p->id_puesto)
                ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
                ->where('id_usuario','<>',$id_usuario)
                ->first();
            if($reserva){
                $respuesta=[
                    'tipo'=>'ERROR',
                    'mensaje'=>"El puesto esta reservado por otro usuario"
                ];
            }

             //Aqui vemos si el puesto lo tiene alguien permanentemente asignado
             $asignados_usuarios=DB::table('puestos_asignados')
             ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
             ->join('users','users.id','puestos_asignados.id_usuario')  
             ->where('puestos.id_puesto',$p->id_puesto)  
             ->where('id_usuario','<>',$id_usuario)
             ->get();

         if($asignados_usuarios){
            $respuesta=[
                'tipo'=>'ERROR',
                'mensaje'=>"El puesto esta asignado permanentemente"
            ];
         }
         
         //Y aqui si el pñuesto esta reserrvado para un perfil en concreto
         $asignados_nomiperfil=DB::table('puestos_asignados')
             ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
             ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')   
             ->where('puestos.id_puesto',$p->id_puesto)    
             ->where('id_perfil','<>',$cod_nivel)
             ->get();

         if($asignados_nomiperfil){
            $respuesta=[
                'tipo'=>'ERROR',
                'mensaje'=>"El puesto esta reservado"
            ];
         }

            logpuestos::create(['id_puesto'=>$p->id_puesto,'id_estado'=>$estado,'id_user'=>Auth::user()->id??0,'fecha'=>Carbon::now()]);
            DB::table('puestos')->where('token',$puesto)->update([
                'id_estado'=>$estado,
                'fec_ult_estado'=>Carbon::now(),
                'id_usuario_usando'=>$id_usuario,
            ]);
            DB::table('reservas')
                ->where('id_puesto',$p->id_puesto)
                ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
                ->where('id_usuario',$id_usuario)
                ->update(['fec_utilizada'=>Carbon::now()]);
            switch ($estado) {
                case 1:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".$p->cod_puesto." listo para ser usado de nuevo. Muchas gracias",
                        'color'=>'success',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                    ];
                    break;
                case 2:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".$p->cod_puesto." esta ahora ocupado por usted",
                        'color'=>'danger',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                    ];
                    break;
                case 3:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".$p->cod_puesto." marcado para limpiar. Muchas gracias",
                        'color'=>'info',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".$p->cod_puesto." marcado como inoperativo",
                        'color'=>'gray',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".$p->cod_puesto." marcado como bloqueado",
                        'color'=>'danger',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                    ];
                    break;
                default:
                    # code...
                    break;
            }
            savebitacora('Cambio de puesto '.$p->id_puesto. ' a estado '.$estado,"Home","getpuesto","OK");
        }
        
        return $respuesta;
    }



    public function getqr($sitio){

       foreach(DB::table('puestos')->get() as $puesto)
       {
           DB::table('puestos')->where('id_puesto',$puesto->id_puesto)->update([
               'token'=>Str::random(50)
           ]);
       }
    }

    public function scan(){
        $estado_destino=2;
        $modo='cambio_estado';
        $titulo='Marcar puesto como usado';
        $tipo_scan="main";
        return view('scan',compact('estado_destino','modo','titulo','tipo_scan'));
    }

    public function scan_usuario(){
        $estado_destino=1;
        $modo='usuario';
        $titulo='';
        $tipo_scan="main";
        return view('scan',compact('estado_destino','modo','titulo','tipo_scan'));
    }

    public function scan_mantenimiento(){
        $estado_destino=1;
        $modo='incidencia';
        $titulo='';
        $tipo_scan="main";
        return view('scan',compact('estado_destino','modo','titulo','tipo_scan'));
    }
}
