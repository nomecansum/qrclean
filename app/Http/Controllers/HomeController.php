<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\puestos;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\logpuestos;
use App\Models\users;
use App\Models\notificaciones;
use App\Models\notificaciones_tipos;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        if(session('CL')==null){
            return redirect('/logout');
        }

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
        $mireserva=null;
        //A ver si el usuario viene autentificado
        if(Auth::check())
            {
                $id_usuario=Auth::user()->id;
                $cod_nivel=Auth::user()->cod_nivel;
            } else {
                $id_usuario=0;
                $cod_nivel=0;
            }
        $encuesta=0;

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
            $tags=DB::table('tags_puestos')->where('id_puesto',$p->id_puesto)->get();
            $config_cliente=DB::table('config_clientes')->where('id_cliente',$p->id_cliente)->first();
        
            //Vemos si el puesto tiene una encuesta activa que haya que mostrar en el escaneo
            $encuesta=DB::table('encuestas')
                ->whereraw("'".Carbon::now()->format('Y-m-d')."' between fec_inicio AND fec_fin")
                ->where('mca_activa','S')
                ->where(function($q) use($p){
                    $q->orwhereraw('FIND_IN_SET('.$p->id_puesto.', list_puestos) <> 0');
                    $q->orwherenull('list_puestos');
                })
                ->where(function($q) use($p){
                    $q->orwhereraw('FIND_IN_SET('.$p->id_planta.', list_plantas) <> 0');
                    $q->orwherenull('list_plantas');
                })
                ->where(function($q) use($p){
                    $q->orwhereraw('FIND_IN_SET('.$p->id_edificio.', list_edificios) <> 0');
                    $q->orwherenull('list_edificios');
                })
                ->where(function($q) use($p){
                    $q->orwhereraw('FIND_IN_SET('.$p->id_tipo_puesto.', list_tipos) <> 0');
                    $q->orwherenull('list_tipos');
                })
                ->where(function($q) use($p,$tags){
                    $q->orwhere(function($q) use($tags){
                        foreach($tags as $tag){
                            $q->orwhereraw('FIND_IN_SET('.$tag->id_tag.', list_tags) <> 0');
                        }
                    });
                    $q->orwherenull('list_tags');
                })
            ->orderby('id_encuesta','desc')
            ->first();


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
                ->join('users','users.id','reservas.id_usuario')
                ->where(function($q){
                    $q->where(function($q){
                        $q->wherenull('fec_fin_reserva');
                        $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                    });
                    $q->orwhereraw("'".Carbon::now()."' between DATE_SUB(fec_reserva,interval 15 MINUTE) AND DATE_ADD(fec_fin_reserva,interval 15 MINUTE)");
                })
                ->where('mca_anulada','N')
                ->first();
            
            if(isset($reserva) && $reserva->id_usuario!=$id_usuario){
                try{
                    $usuario_usando="";
                    if(session('CL')['mca_mostrar_nombre_usando']=='S'){
                        $usuario_usando=$reservas->name;
                        $usuario_usando=" por ".$usuario_usando;
                    }
                } catch(\Exception $e){
                    $usuario_usando="";
                }
                $respuesta=[
                    'mensaje'=>"PUESTO RESERVADO ".$usuario_usando,
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0,
                    'encuesta'=>0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            } else {
                $mireserva=DB::table('reservas')
                ->where('id_puesto',$p->id_puesto)
                ->where(function($q){
                    $q->where(function($q){
                        $q->wherenull('fec_fin_reserva');
                        $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                    });
                    $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
                })
                ->where('id_usuario',$id_usuario)
                ->where('mca_anulada','N')
                ->first();
            }


            //Aqui vemos si el puesto lo tiene alguien permanentemente asignado
            $asignados_usuarios=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
                ->join('users','users.id','puestos_asignados.id_usuario')
                ->where('puestos.id_puesto',$p->id_puesto)
                ->where('id_usuario','<>',$id_usuario)
                ->where(function($q){
                    $q->wherenull('fec_desde');
                    $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
                })
                ->get();

            if(!$asignados_usuarios->isEmpty()){
                try{
                    $usuario_usando="";
                    if(session('CL')['mca_mostrar_nombre_usando']=='S'){
                        $usuario_usando=$asignados_usuarios->first()->name;
                        $usuario_usando=" a ".$usuario_usando;
                    }
                } catch(\Exception $e){
                    $usuario_usando="";
                }
                $respuesta=[
                    'mensaje'=>"PUESTO ASIGNADO PERMANENTEMENTE ".$usuario_usando,
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0,
                    'encuesta'=>0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }

            //Y ahora vemos si el susodicho tiene asignado el puesto que se esta escaneando para darle la bienvenida
            $asignados_ami=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
                ->join('users','users.id','puestos_asignados.id_usuario')
                ->where('puestos.id_puesto',$p->id_puesto)
                ->where('id_usuario',$id_usuario)
                ->where(function($q){
                    $q->wherenull('fec_desde');
                    $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
                })
                ->get();

            if(!$asignados_ami->isEmpty()){
                if ($p->id_estado==1){
                    $enc_toca=(isset($encuesta->val_momento) && ($encuesta->val_momento=="A" || $encuesta->val_momento=="0"))?$encuesta->id_encuesta:0;
                } else {
                    $enc_toca=(isset($encuesta->val_momento) && ($encuesta->val_momento=="D" || $encuesta->val_momento=="0"))?$encuesta->id_encuesta:0;
                }
                    
                $respuesta=[
                    'mensaje'=>"Hola ".Auth::user()->name.' este es su puesto asignado para hoy ',
                    'icono' => '<i class="fad fa-user"></i>',
                    'color'=>'success',
                    'puesto'=>$p,
                    'disponibles'=> $p->id_estado==1||$p->id_estado==2?[]:$disponibles,
                    'operativo' => $p->id_estado==1||$p->id_estado==2?1:0,
                    'encuesta'=>$enc_toca,
                ];

                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }

            //Aqui vamos a ver si el pollo tiene asignado un puesto distinto al que esta escaneado para decirle que se peine y se vaya a su puesto
            $puesto_mio_es_otro=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
                ->join('users','users.id','puestos_asignados.id_usuario')
                ->where('puestos.id_puesto','<>',$p->id_puesto)
                ->where('id_usuario',$id_usuario)
                ->where('puestos.id_tipo_puesto',$p->id_tipo_puesto)
                ->where(function($q){
                    $q->wherenull('fec_desde');
                    $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
                })
                ->get();

            if(!$puesto_mio_es_otro->isEmpty()){
                $respuesta=[
                    'mensaje'=>"Hola ".Auth::user()->name.', para hoy usted tiene asignado el puesto '.$puesto_mio_es_otro->first()->cod_puesto.'.<br> <b>Debe utilizar ese puesto</b>',
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>null,
                    'operativo' => 0,
                    'encuesta'=>0
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
                try{
                    $usuario_usando="P";
                    if(session('CL')['mca_mostrar_nombre_usando']=='S'){
                        $usuario_usando=$asignados_nomiperfil->first()->des_nivel_acceso;
                        $usuario_usando=" para ".$usuario_usando;
                    }
                } catch(\Exception $e){
                    $usuario_usando="P";
                }
                $respuesta=[
                    'mensaje'=>"PUESTO RESERVADO ".$usuario_usando,
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'danger',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0,
                    'encuesta'=>0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }

            if($id_usuario==0 && $p->mca_acceso_anonimo=='N'){
                $respuesta=[
                    'mensaje'=>"Debe iniciar sesion para poder utilizar este puesto",
                    'icono' => '<i class="fad fa-bring-forward"></i>',
                    'color'=>'warning',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0,
                    'hacer_login'=>1,
                    'encuesta'=>0
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }
            //Ahora veamos si tiene incidencia
            if($p->mca_incidencia=='S'){
                $inc=DB::table('incidencias')
                    ->join('incidencias_tipos','incidencias_tipos.id_tipo_incidencia','incidencias.id_tipo_incidencia')
                    ->where('id_puesto',$p->id_puesto)
                    ->wherenull('fec_cierre')
                    ->first();
                $respuesta=[
                    'mensaje'=>"Puesto con incidencia",
                    'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                    'color'=>'warning',
                    'puesto'=>$p,
                    'disponibles'=>$disponibles,
                    'operativo' => 0,
                    'encuesta'=>0,
                    'incidencia'=>['id'=>$inc->id_incidencia??0,'tipo'=>$inc->des_tipo_incidencia??'','texto'=>$inc->des_incidencia??'']
                ];
                return view('scan.result',compact('respuesta','reserva','config_cliente'));
            }

            //dd($encuesta);
            switch ($p->id_estado) {
                case 7:  //Solo encuesta
                    $respuesta=[
                        'mensaje'=>"",
                        'icono' => '',
                        'color'=>'white',
                        'puesto'=>$p,
                        'disponibles'=>null,
                        'operativo' => 0,
                        'encuesta'=>(isset($encuesta->val_momento))?$encuesta->id_encuesta:0,
                    ];
                    $mireserva=null;
                    break;
                case 1:
                    $tiene_reserva=DB::table('reservas')
                    ->where('id_puesto',$p->id_puesto)
                    ->where(function($q){
                        $q->where(function($q){
                            $q->wheredate('fec_reserva',Carbon::now()->format('Y-m-d'));
                            $q->where('fec_reserva','>',Carbon::now());
                        });
                    })
                    ->where('id_usuario','<>',$id_usuario)
                    ->get();

                    $horarios_reserva="";
                    foreach($tiene_reserva as $rv){
                        $horarios_reserva.=Carbon::parse($rv->fec_reserva)->format('H:i').' - '.Carbon::parse($rv->fec_fin_reserva)->format('H:i').' | ';
                    }
                        
                    $respuesta=[
                        'mensaje'=>"Puesto disponible",
                        'icono' => '<i class="fad fa-thumbs-up"></i>',
                        'color'=>'success',
                        'puesto'=>$p,
                        'operativo' => 1,
                        'tiene_reserva'=>$horarios_reserva,
                        'encuesta'=>(isset($encuesta->val_momento) && ($encuesta->val_momento=="A" || $encuesta->val_momento=="0"))?$encuesta->id_encuesta:0,
                    ];
                    break;
                case 2:
                    try{
                        $usuario_usando="";
                        if(session('CL')['mca_mostrar_nombre_usando']=='S'){
                            $usuario_usando=users::find($p->id_usuario_usando)->name;
                            $usuario_usando=" por ".$usuario_usando;
                        }
                        if($p->id_usuario_usando==Auth::user()->id){
                            $usuario_usando=" por usted";
                        }
                    } catch(\Exception $e){
                        $usuario_usando="";
                    }
                    $respuesta=[
                        'mensaje'=>"Puesto ocupado".$usuario_usando,
                        'icono' => '<i class="fad fa-lock-alt"></i>',
                        'color'=>'danger',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 1,
                        'encuesta'=>(isset($encuesta->val_momento) && ($encuesta->val_momento=="D" || $encuesta->val_momento=="0"))?$encuesta->id_encuesta:0,
                    ];
                    break;
                case 3:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible,debe ser limpiado",
                        'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                        'color'=>'warning',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 1,
                        'encuesta'=>0
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO INOPERATIVO",
                        'icono' => '<i class="fad fa-bring-forward"></i>',
                        'color'=>'gray',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 0,
                        'encuesta'=>0
                    ];
                    break;
                case 5:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO BLOQUEADO",
                        'icono' => '<i class="fad fa-bring-forward"></i>',
                        'color'=>'danger',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 0,
                        'encuesta'=>0
                    ];
                    break;
                case 6:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO AVERIADO",
                        'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                        'color'=>'warning',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles,
                        'operativo' => 0,
                        'encuesta'=>0
                    ];
                    break;
                default:
                    $respuesta=[
                        'mensaje'=>"Estado no definido",
                        'icono' => '',
                        'color'=>'white',
                        'puesto'=>$p,
                        'disponibles'=>null,
                        'operativo' => 0,
                        'encuesta'=>(isset($encuesta->val_momento))?$encuesta->id_encuesta:0,
                    ];
                    $mireserva=null;
                    break;
            }
        }
        //savebitacora('Cambio de puestos QR anonimo'.$p->id_puesto. ' a estado '.$p->id_estado,"Home","getpuesto","OK");
        return view('scan.result',compact('respuesta','reserva','mireserva','config_cliente'));
    }

    function actualizar_estado_parking($estado,$id_usuario){
         //Vamos a ver si tiene plaza de parking
         $tiene_parking=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')  
            ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_parking'))  
            ->where('id_usuario',$id_usuario)
            ->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
         ->first();
         if(!isset($tiene_parking)){
             //Si no lo tiene asignado a ver si lo tiene reservado
             $tiene_parking=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')   
                ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_parking'))  
                ->where(function($q){
                    $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                    $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
                })
                ->where('id_usuario',$id_usuario)
                ->first();
         }
         if(isset($tiene_parking)){
             logpuestos::create(['id_puesto'=>$tiene_parking->id_puesto,'id_estado'=>$estado,'id_user'=>Auth::user()->id??0,'fecha'=>Carbon::now()]);

             DB::table('puestos')->where('id_puesto',$tiene_parking->id_puesto)->update([
                 'id_estado'=>$estado,
                 'fec_ult_estado'=>Carbon::now(),
                 'id_usuario_usando'=>$estado==1?null:$id_usuario,
                 'fec_liberacion_auto'=>Carbon::now()->endOfDay(),
             ]);
         }

    }

    public function estado_puesto($puesto,$estado){ 
        //A ver si el usuario viene autentificado
        if(Auth::check())
            {
                $id_usuario=Auth::user()->id;
                $cod_nivel=Auth::user()->cod_nivel;
            } else {
                $id_usuario=null;
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
                ->where(function($q){
                    $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                    $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
                })
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
             ->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
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
                'fec_liberacion_auto'=>Carbon::now()->endOfDay(),
                'id_usuario_usando'=>null,
            ]);
            DB::table('reservas')
                ->where('id_puesto',$p->id_puesto)
                ->wheredate('fec_reserva',Carbon::now()->format('Y-m-d'))
                ->where('id_usuario',$id_usuario)
                ->update(['fec_utilizada'=>Carbon::now()]);
            switch ($estado) {
                case 1:
                    $this->actualizar_estado_parking($estado,$id_usuario);
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".nombrepuesto($p)." listo para ser usado de nuevo. Muchas gracias",
                        'color'=>'success',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                        'mostrar_boton_home'=>1,
                    ];
                    break;
                case 2:
                    DB::table('puestos')->where('token',$puesto)->update([
                        'id_usuario_usando'=>$id_usuario,
                    ]);
                    $this->actualizar_estado_parking($estado,$id_usuario);
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".nombrepuesto($p)." esta ahora ocupado por usted",
                        'color'=>'danger',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                        'mostrar_boton_home'=>1,
                    ];
                    break;
                case 3:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".nombrepuesto($p)." marcado para limpiar. Muchas gracias",
                        'color'=>'info',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                        'mostrar_boton_home'=>1,
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".nombrepuesto($p)." marcado como inoperativo",
                        'color'=>'gray',
                        'label'=>$e->des_estado,
                        'id'=>$p->id_puesto,
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'tipo'=>'OK',
                        'mensaje'=>"Puesto ".nombrepuesto($p)." marcado como bloqueado",
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

    public function gen_qr(Request $r)
    {
        return base64_encode(QRCode::format('png')->size(500)->generate($r->url));
    }

    public function reset_perfil_admin(){
        $secciones=DB::table('secciones')->get();
        DB::table('secciones_perfiles')->where('id_perfil',5)->delete();
        foreach($secciones as $seccion){
            DB::table('secciones_perfiles')->insert([
                'id_perfil'=>5,
                'id_seccion'=>$seccion->cod_seccion,
                'mca_read'=>1,
                'mca_write'=>1,
                'mca_create'=>1,
                'mca_delete'=>1
            ]);
        }
    }

    public function regenera_fontawesome(){
        $icons=[Null];
           $json=file_get_contents(public_path('/plugins/fontawesome6/metadata/categories.json'));
           $json=json_decode($json);
           foreach($json as $cat){
                foreach($cat->icons as $icon){
                    $icons[]='fa-solid fa-'.$icon;
                    $icons[]='fa-regular fa-'.$icon;
                    $icons[]='fa-duotone fa-'.$icon;
                    $icons[]='fa-light fa-'.$icon;
                    $icons[]='fa-brands fa-'.$icon;
                }
           }
           $icons=array_unique($icons);
           return (str_replace(",",",\r\n",str_replace('"',"'",json_encode(array_values($icons)))));
    }

    public function politica(){
        $response=Http::withOptions(['verify' => false])->get(config('app.link_politica'));
        
        if($response->status()==200){
           return $response->body();
        }
    }

    public function terminos(){
        return view('layouts.terminos');
    }

    public function cookies(){
        return view('layouts.cookies');
    }

    public function check_notificaciones(){
        $reservas=\App\Http\Controllers\UsersController::mis_puestos(auth()->user()->id)['mispuestos'];
        session(['reservas'=>$reservas]);

        $notificaciones=cuenta_notificaciones();
            
        return response()->json($notificaciones);
    }

    public function search(Request $r){

        //Primero vamos a ver si existe una ruta con ese nombre
        //solo el nombre
        $routes = Route::getRoutes();
        foreach ($routes as $route)
        {
           if($r->txt_buscar==$route->uri()){
               return redirect($r->txt_buscar);
           }
        }

        dd($slugs);

        $r->txt_buscar=strtoupper($r->txt_buscar);
        //BUSQUEDA POR PALABRA CLAVE: palabra clave+identificador => EJ usuario 47
        $palabras_clave=[
                ['palabra'=>'PUESTO','tabla'=>'puestos','campo'=>'id_puesto','url'=>'/puestos/edit/','campo_salida'=>'id_puesto'],
                ['palabra'=>'PLANTA','tabla'=>'plantas','campo'=>'id_planta','url'=>'/plantas/edit/','campo_salida'=>'id_planta'],
                ['palabra'=>'EMPRESA','tabla'=>'clientes','campo'=>'id_cliente','url'=>'/clientes/edit/','campo_salida'=>'id_cliente'],
                ['palabra'=>'EDIFICIO','tabla'=>'edificios','campo'=>'id_edificio','url'=>'/edificios/edit/','campo_salida'=>'id_edificio'],
                ['palabra'=>'USUARIO','tabla'=>'users','campo'=>'id','url'=>'/users/edit/','campo_salida'=>'id'],
                ['palabra'=>'INCIDENCIA','tabla'=>'incidencias','campo'=>'id_incidencia','url'=>'/incidencias/edit/','campo_salida'=>'id_incidencia']
        ];
        $texto=explode(' ',$r->txt_buscar);
        if(sizeof($texto)>1){
            $clave=strtoupper($texto[0]);
            $valor=$texto[1];
            foreach($palabras_clave as $palabra){
                if($palabra['palabra']==$clave){
                    $resultado=DB::table($palabra['tabla'])
                    ->when(isset($palabra['join']),function($q) use($palabra){
                        $q->join($palabra['join'][0],$palabra['join'][1],$palabra['join'][2]);
                    })
                    ->where($palabra['campo'],$valor)
                    ->where(function($q) use ($palabra){
                        if (!isAdmin()){
                            $q->Wherein($palabra['tabla'].'.id_cliente',clientes());
                        }
                    })
                    ->value($palabra['campo_salida']);
                    if(isset($resultado)){
                        return redirect($palabra['url'].$resultado);
                    }
                }
            }
        }
    
    
        //BUSQUEDA GENERAL DE TEXTO
        //Busqueda de empresas
        $clientes=DB::table('clientes')
        ->where('clientes.nom_cliente', 'LIKE', "%{$r->txt_buscar}%")
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->where(function($q) use ($r){
            $q->orWhere('clientes.nom_cliente', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('clientes.nom_contacto', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('clientes.id_cliente', 'LIKE', "%{$r->txt_buscar}%");
        })
        ->orderby('clientes.nom_cliente')
        ->get();
    
        //Busqueda de empleados
        $puestos=DB::table('puestos')
        ->join('clientes','puestos.id_cliente','clientes.id_cliente')
        ->where(function($q) use ($r){
            $q->where('puestos.cod_puesto', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('puestos.des_puesto', 'LIKE', "%{$r->txt_buscar}%");
        })
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('puestos.id_cliente',clientes());
            }
        })
        ->orderby('puestos.cod_puesto')
        ->get();
    
        //Busqueda de departamentos
        $plantas=DB::table('plantas')
        ->join('clientes','plantas.id_cliente','clientes.id_cliente')
        ->where('plantas.des_planta', 'LIKE', "%{$r->txt_buscar}%")
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('plantas.id_cliente',clientes());
            }
        })
        ->orderby('plantas.des_planta')
    
        ->get();
    
        //Busqueda de centros
        $edificios=DB::table('edificios')
        ->join('clientes','edificios.id_cliente','clientes.id_cliente')
        ->where(function($q) use ($r){
            $q->where('edificios.des_edificio', 'LIKE', "%{$r->txt_buscar}%");
        })
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('edificios.id_cliente',clientes());
            }
        })
        ->orderby('edificios.des_edificio')
        ->get();
    
        //Busqueda de incidencias
        $incidencias=DB::table('incidencias')
        ->join('clientes','incidencias.id_cliente','clientes.id_cliente')
        ->where(function($q) use ($r){
            $q->where('incidencias.des_incidencia', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('incidencias.txt_incidencia', 'LIKE', "%{$r->txt_buscar}%");
    
        })
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('incidencias.id_cliente',clientes());
            }
        })
        ->get();
    
        //Busqueda de usuarios
        $usuarios=DB::table('users')
        ->join('clientes','users.id_cliente','clientes.id_cliente')
        ->join('niveles_acceso','users.cod_nivel','niveles_acceso.cod_nivel')
        ->where(function($q) use ($r){
            $q->where('users.name', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('users.email', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('users.id', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('users.id_usuario_externo', 'LIKE', "%{$r->txt_buscar}%");
            $q->orwhere('niveles_acceso.des_nivel_acceso', 'LIKE', "%{$r->txt_buscar}%");
        })
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('users.id_cliente',clientes());
            }
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $q->where('users.id_usuario_supervisor',Auth::user()->id);
            }
            if(Auth::user()->val_nivel_acceso==1){
                $q->where('users.id','=',Auth::user()->id);
            }
        })
        ->orderby('users.name')
        ->get();
    
        return view('search.index',compact('clientes','puestos','plantas','edificios','usuarios','incidencias','r'));
    }

    public function target($tipo,$id,$nombre=null){
        validar_acceso_tabla($id,$tipo);

        return view('search.target',compact('tipo','id','nombre'));
    }

    public function set_operario(Request $r){
        $operario=DB::table('contratas_operarios')
            ->where('id_operario',$r->id_operario)
            ->first();
        session(['id_operario'=> $operario->id_operario??null]);
    }
    
}
