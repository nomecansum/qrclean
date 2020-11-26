<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Auth;
use App\Models\reservas;
use App\Models\puestos;
use App\Models\users;

class ReservasController extends Controller
{
    public function index(){
        return view('reservas.index');
    }

    public function loadMonthSchedule(Request $r)
    {
        if ($r->month)
            $month = Carbon::parse($r->month);
        else $month = Carbon::now()->startOfMonth();

        if ($r->type == 'add')
            $backMonth = $month->addMonth()->format('Y-n');
        elseif($r->type == 'sub')
            $backMonth = $month->subMonth()->format('Y-n');
        else $backMonth = $month->format('Y-n');
        $end=$month->copy()->endOfMonth();
        $reservas=DB::table('reservas')
            ->selectraw('date(reservas.fec_reserva) as fec_reserva,reservas.fec_fin_reserva,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->where('puestos.id_cliente',Auth::user()->id_cliente)
            ->where('reservas.id_usuario',Auth::user()->id)
            ->wherebetween('fec_reserva',[$month,$end])
            ->get();      

        return view('reservas.calendario',compact('backMonth','reservas'))->render();
    }

    public function create($fecha){
        
        $reserva=new reservas;
        $f1=Carbon::parse($fecha);
        return view('reservas.edit',compact('reserva','f1'));
    }

    public function edit($fecha){
        
        $f1=Carbon::parse($fecha);
        $reserva=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->wheredate('fec_reserva',$f1)
            ->where('id_usuario',Auth::user()->id)
            ->first();
        return view('reservas.edit_del',compact('reserva','f1'));
    }


    public function comprobar_puestos(Request $r){
        //Primero comprobamos si tiene una reserva para ese dia
        $reserva=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->where('fec_reserva',adaptar_fecha($r->fecha))
            ->where(function($q) use($r){
                $q->where('fec_reserva',adaptar_fecha($r->fecha));
                $q->orwhereraw("'".adaptar_fecha($r->fecha)."' between fec_reserva AND fec_fin_reserva");
            })
            ->where('id_usuario',Auth::user()->id)
            ->first();
        if(isset($reserva)){
            $f1=adaptar_fecha($r->fecha);
            return view('reservas.edit_del',compact('reserva','f1'));
        }
        
        $fec_desde=Carbon::createFromFormat('d/m/Y H:i',$r->fecha.' '.$r->hora_inicio);
        $fec_hasta=Carbon::createFromFormat('d/m/Y H:i',$r->fecha.' '.$r->hora_fin);

        $plantas_usuario=DB::table('plantas_usuario')
            ->where('id_usuario',Auth::user()->id)
            ->pluck('id_planta')
            ->toArray();

        $edificios_usuario=DB::table('plantas')
            ->join('plantas_usuario','plantas_usuario.id_planta','plantas.id_planta')
            ->where('plantas_usuario.id_usuario',Auth::user()->id)
            ->pluck('id_edificio')
            ->unique()
            ->toArray();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where('puestos.id_edificio',$r->edificio)
            ->where(function($q) use($r,$fec_desde,$fec_hasta){
                $q->where(function($q) use($fec_desde,$fec_hasta,$r){
                    $q->wherenull('fec_fin_reserva');
                    $q->where('fec_reserva',adaptar_fecha($r->fecha));
                });
                $q->orwhere(function($q) use($fec_desde,$fec_hasta,$r){
                    $q->whereraw("'".$fec_desde->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                    $q->orwhereraw("'".$fec_hasta->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                    $q->orwherebetween('fec_reserva',[$fec_desde,$fec_hasta]);
                    $q->orwherebetween('fec_fin_reserva',[$fec_desde,$fec_hasta]);
                });
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        if(isset($reservas)){
            $puestos_reservados=$reservas->pluck('id_puesto')->toArray();
        } else{
            $puestos_reservados=[];
        }

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',Auth::user()->id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where(function($q) use($r){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::parse(adaptar_fecha($r->fecha))."' between fec_desde AND fec_hasta");
            })
            ->get();
        if(isset($asignados_usuarios)){
            $puestos_usuarios=$asignados_usuarios->pluck('id_puesto')->toArray();
        } else{
            $puestos_usuarios=[];
        }
        
        
            
        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        if(isset($asignados_nomiperfil)){
            $puestos_nomiperfil=$asignados_nomiperfil->pluck('id_puesto')->toArray();
        } else{
            $puestos_nomiperfil=[];
        }

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where(function($q) use($plantas_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('puestos.id_planta',$plantas_usuario??[]);
                }
            })
            ->where(function($q){
                if(!checkPermissions(['Mostrar puestos no reservables'],['R'])){
                    $q->where('puestos.mca_reservar','S');
                }
            })
            ->wherenotin('puestos.id_estado',[4,5,6])
            ->wherenotin('puestos.id_puesto',$puestos_usuarios)
            ->wherenotin('puestos.id_puesto',$puestos_nomiperfil)
            ->wherenotin('puestos.id_puesto',$puestos_reservados)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
            ->select('id_edificio','des_edificio')
            ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
            ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where(function($q) use($edificios_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('edificios.id_edificio',$edificios_usuario??[]);
                }
            })
            ->where('id_edificio',$r->edificio)
            ->get();

        $tipo_vista=$r->tipo;

        return view('reservas.'.$r->tipo,compact('reservas','puestos','edificios','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','plantas_usuario','tipo_vista'));
    }

    public function save(Request $r){
        //dd($r->all());
        $fec_desde=Carbon::createFromFormat('d/m/Y H:i',$r->fechas.' '.$r->hora_inicio);
        $fec_hasta=Carbon::createFromFormat('d/m/Y H:i',$r->fechas.' '.$r->hora_fin);
        //Primero hay que comprobar que no han pillado el pñuesto mientras el usuario se lo pensabe

        $ya_esta=reservas::where('id_puesto',$r->id_puesto)->where('fec_reserva',adaptar_fecha($r->fechas))->first();
        if($ya_esta){
            //Ya esta pillado
            return [
                'title' => "Reservas",
                'error' => 'El puesto ya ha sido reservado mientras estaba eligiendo, elija otro',
                //'url' => url('sections')
            ];

        } else{
            //Borramos cualquier otra reserva que tuviese el usuarios par ese dia
  
            $antoerior=reservas::where('id_usuario',Auth::user()->id)->where('fec_reserva',Carbon::parse(adaptar_fecha($r->fechas)))->delete();
            //Insertamos la nueva
            $puesto=puestos::find($r->id_puesto);
            $res=new reservas;
            $res->id_puesto=$r->id_puesto;
            $res->id_usuario=Auth::user()->id;
            $res->fec_reserva=$fec_desde;
            $res->fec_fin_reserva=$fec_hasta;
            // if($puesto->max_horas_reservar && $puesto->max_horas_reservar>0 && is_int($puesto->max_horas_reservar)){
            //     $res->fec_fin_reserva=$res->fec_reserva->addHour($puesto->max_horas_reservar);   
            // }
            $res->id_cliente=Auth::user()->id_cliente;
            $res->save();
            savebitacora('Puesto '.$r->des_puesto.' reservado. Identificador de reserva: '.$res->id_reserva,"Reservas","save","OK");
            return [
                'title' => "Reservas",
                'mensaje' => 'Puesto '.$r->des_puesto.' reservado. Identificador de reserva: '.$res->id_reserva,
                'fecha' => Carbon::parse(adaptar_fecha($r->fechas))->format('Ymd'),
                //'url' => url('puestos')
            ];
        }
    }

    public function delete(Request $r){
        $reserva=reservas::where('id_usuario',Auth::user()->id)->where('id_reserva',$r->id)->first();
        
        if(isset($reserva)){
            $reserva->delete();
            savebitacora('Reserva del puesto '.$r->des_puesto.' reservado para el dia '.$r->fecha.' Cancelada',"Reservas","delete","OK");
            return [
                'title' => "Reservas",
                'mensaje' => 'Reserva del puesto '.$r->des_puesto.' reservado para el dia '.$r->fecha.' Cancelada',
                //'url' => url('puestos')
            ];
        } else {
            return [
                'title' => "Reservas",
                'error' => 'La reserva no existe o no es suya',
                //'url' => url('sections')
            ];
        }
    }

    public function puestos_usuario($id,$desde,$hasta){
        $usuario=users::find($id);

        $plantas_usuario=DB::table('plantas_usuario')
            ->where('id_usuario',$id)
            ->pluck('id_planta')
            ->toArray();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$usuario->id_cliente)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q) use($desde,$hasta){
                $q->wherebetween('fec_reserva',[Carbon::parse($desde),Carbon::parse($hasta)]);
                $q->orwherebetween('fec_fin_reserva',[Carbon::parse($desde),Carbon::parse($hasta)]);
                $q->orwhere(function($q) use($desde,$hasta){
                    $q->wherebetween('fec_reserva',[Carbon::parse($desde),Carbon::parse($hasta)]);
                    $q->wherenull('fec_fin_reserva');
                });
            })
            ->where('reservas.id_cliente',$usuario->id_cliente)
            ->get();
        if(isset($reservas)){
            $puestos_reservados=$reservas->pluck('id_puesto')->toArray();
        } else{
            $puestos_reservados=[];
        }

        $puestos_reservados=$reservas->pluck('id_puesto')->unique()->toArray();

        //Estas tres querys hacen falta para la compatibilidad de la vista
        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario',0)
            ->get();
        if(isset($asignados_usuarios)){
            $puestos_usuarios=$asignados_usuarios->pluck('id_puesto')->toArray();
        } else{
            $puestos_usuarios=[];
        }

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',0)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil',0)
            ->get();
        if(isset($asignados_nomiperfil)){
            $puestos_nomiperfil=$asignados_nomiperfil->pluck('id_puesto')->toArray();
        } else{
            $puestos_nomiperfil=[];
        }

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$usuario->id_cliente)
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->when($puestos_reservados, function($q) use($puestos_reservados){
                $q->wherenotin('id_puesto',$puestos_reservados);
            })
            ->where(function($q) use($plantas_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('puestos.id_planta',$plantas_usuario??[]);
                }
            })
            ->wherenotin('puestos.id_estado',[4,5,6])
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();


        $puestos_check=[];
        $checks=0; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_usuario/";
        
        return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
    }

    public function asignar_reserva_multiple(Request $r){

        $usuario=users::find($r->id_usuario[0]);
        $puesto=puestos::find($r->puesto);
        $period = CarbonPeriod::create(Carbon::parse($r->desde), Carbon::parse($r->hasta));
        foreach($period as $fecha){
            $res=new reservas;
            $res->id_puesto=$r->puesto;
            $res->id_usuario=$usuario->id;
            $res->fec_reserva=$fecha;
            //Si no vienen horas las ponemos a 0
            $res->fec_reserva->hour=0;
            $res->fec_reserva->minute=0;
            $res->fec_reserva->second=0;
            if($puesto->max_horas_reservar && $puesto->max_horas_reservar>0 && is_int($puesto->max_horas_reservar) && session('CL')['mca_reserva_horas']=='S'){
                $res->fec_fin_reserva=$res->fec_reserva->addHour($puesto->max_horas_reservar);   
            }
            $res->id_cliente=$usuario->id_cliente;
            $res->save();
        }
        savebitacora('Reserva  de puesto '.$r->des_puesto.' creada para el usuario '.$usuario->name.' para el periodo  '.$r->desde.' - '.$r->hasta,"Reservas","asignar_reserva_multiple","OK");
        $str_notificacion=Auth::user()->name.' ha creado una Reserva  del puesto '.$r->des_puesto.' para usted en el periodo  '.$r->desde.' - '.$r->hasta;
        notificar_usuario($usuario,"Se le ha asignado un nuevo puesto",'emails.asignacion_puesto',$str_notificacion,1);
        return [
            'title' => "Reservas",
            'message' => 'Reserva  de puesto '.$r->des_puesto.' creada para el usuario '.$usuario->name.' para el periodo  '.$r->desde.' - '.$r->hasta,
            //'fecha' => Carbon::parse(adaptar_fecha($r->fechas))->format('Ymd'),
            //'url' => url('puestos')
        ];
    }
}
