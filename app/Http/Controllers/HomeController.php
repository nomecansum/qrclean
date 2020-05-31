<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\puestos;
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
        $cuenta=puestos::count();
        return view('home',compact('cuenta'));
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

        $disponibles=DB::table('puestos')
            ->select('cod_puesto','des_puesto','val_color','val_icono')
            ->where('id_cliente',$p->id_cliente)
            ->where('id_edificio',$p->id_edificio)
            ->where('id_planta',$p->id_planta)
            ->where('id_estado',1)
            ->get();
        if(!isset($p)){
            //Error puesto no encontrado
            $respuesta=[
                'mensaje'=>"Error, puesto no encontrado",
                'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                'color'=>'danger'
            ];
        } else {    
            switch ($p->id_estado) {
                case 1:
                    $respuesta=[
                        'mensaje'=>"Puesto disponible",
                        'icono' => '<i class="fad fa-thumbs-up"></i>',
                        'color'=>'success',
                        'puesto'=>$p
                    ];
                    break;
                case 2:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible",
                        'icono' => '<i class="fad fa-lock-alt"></i>',
                        'color'=>'danger',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles
                    ];
                    break;
                case 3:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible,debe ser limpiado",
                        'icono' => '<i class="fad fa-exclamation-triangle"></i>',
                        'color'=>'warning',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO BLOQUEADO",
                        'icono' => '<i class="fad fa-bring-forward"></i>',
                        'color'=>'gray',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles
                    ];
                    break;
                case 4:
                    $respuesta=[
                        'mensaje'=>"Puesto no disponible, PUESTO BLOQUEADO",
                        'icono' => '<i class="fad fa-bring-forward"></i>',
                        'color'=>'danger',
                        'puesto'=>$p,
                        'disponibles'=>$disponibles
                    ];
                    break;
                default:
                    # code...
                    break;
            }
        }
        return view('scan.result',compact('respuesta'));
    }



    public function estado_puesto($puesto,$estado){ 
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
            logpuestos::create(['id_puesto'=>$p->id_puesto,'id_estado'=>$estado,'id_user'=>Auth::user()->id,'fecha'=>Carbon::now()]);
            DB::table('puestos')->where('token',$puesto)->update([
                'id_estado'=>$estado,
                'fec_ult_estado'=>Carbon::now()
            ]);
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
        }
        return $respuesta;
    }


    public function setqr($sitio){

        $sitio=encrypt($sitio);

        dd($sitio);
        return $sitio;
    }


    public function getqr($sitio){

       foreach(DB::table('puestos')->get() as $puesto)
       {
           DB::table('puestos')->where('id_puesto',$puesto->id_puesto)->update([
               'token'=>Str::random(50)
           ]);
       }
    }


}
