<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\puestos;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\logpuestos;
use App\Models\users;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

}
