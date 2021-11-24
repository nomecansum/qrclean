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
use stdClass;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

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
            ->selectraw('date(reservas.fec_reserva) as fec_reserva,reservas.fec_fin_reserva,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->where('puestos.id_cliente',Auth::user()->id_cliente)
            ->where('reservas.id_usuario',Auth::user()->id)
            ->wherebetween('fec_reserva',[$month,$end])
            ->get();    


        $asignados=DB::table('puestos_asignados')
            ->selectraw("ifnull(date(puestos_asignados.fec_desde),'".$month."') as fec_desde,ifnull(date(puestos_asignados.fec_hasta),'".$end."') as fec_hasta,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color")
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->where(function($q) use($month,$end){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".$month."' between fec_desde AND fec_hasta");
                $q->orwhereraw("'".$end."' between fec_desde AND fec_hasta");
                $q->orwherebetween('fec_desde',[$month,$end]);
                $q->orwherebetween('fec_hasta',[$month,$end]);
            })
            ->get();
        
            foreach($asignados as $as){
                $period = CarbonPeriod::create($as->fec_desde,$as->fec_hasta);
                foreach($period as $p){
                    $item=new stdclass();
                    $item->fec_reserva=$p->format('Y-m-d');
                    $item->fec_fin_reserva=$p->format('Y-m-d');
                    $item->cod_puesto=$as->cod_puesto;
                    $item->des_puesto=$as->des_puesto;
                    $item->des_edificio=$as->des_edificio;
                    $item->des_planta=$as->des_planta;
                    $item->val_icono=$as->val_icono;
                    $item->val_color=$as->val_color;
                    $reservas->push($item);
                }
                
            }
        return view('reservas.calendario',compact('backMonth','reservas'))->render();
    }

    public function create($fecha){
        
        $reserva=new reservas;
        $f1=Carbon::parse($fecha);
        $tipos = DB::table('puestos_tipos')
        ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                $q->orwhere('puestos_tipos.mca_fijo','S');
            }
        })
        ->where(function($q){
            if(config_cliente('mca_salas',Auth::user()->id_cliente)=='S'){
                $q->wherenotin('puestos_tipos.id_tipo_puesto',config('app.tipo_puesto_sala'));
            }
        })
        ->orderby('id_tipo_puesto')
        ->get();
        //Primero comprobamos si tiene una reserva para ese dia
        $misreservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->whereraw("date(fec_reserva)='".$f1->format('Y-m-d')."'")
            ->where('id_usuario',Auth::user()->id)
            ->get();

        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->get();

        $reserva->id_planta=0;

        return view('reservas.edit',compact('reserva','f1','tipos','misreservas','plantas_usuario'));
    }

    public function edit($fecha){
        
        $f1=Carbon::parse($fecha);
        $reserva=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->wheredate('fec_reserva',$f1)
            ->where('id_usuario',Auth::user()->id)
            ->orderby('fec_reserva')
            ->orderby('fec_fin_reserva')
            ->first();

        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->get();
        
        return view('reservas.edit_del',compact('reserva','f1','plantas_usuario'));
    }

    public function comprobar_puestos(Request $r){

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
            
        //dd($r->all());
        $f = explode(' - ',$r->fecha);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);
        //Intervalo en minutos entre las dos hora en cualquier dia
        $h1=Carbon::parse($f1.' '.$r->hora_inicio);
        $h2=Carbon::parse($f1.' '.$r->hora_fin);
        $intervalo=$h2->diffInminutes($h1)/60;
        //Vamos a mirar que puestos estan disponibles todos los dias que ha solicitado el usuario
        $puestos_reservados=[];
        $period = CarbonPeriod::create($f1,$f2);
        foreach($period as $p){
            $fec_desde=Carbon::parse($p->format('Y-m-d').' '.$r->hora_inicio);
            $fec_hasta=Carbon::parse($p->format('Y-m-d').' '.$r->hora_fin);
            // dd($fec_desde.' - '.$fec_hasta);
            $reservas=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->where('puestos.id_edificio',$r->edificio)
                ->where(function($q) use($r,$fec_desde,$fec_hasta){
                    $q->where(function($q) use($fec_desde,$fec_hasta,$r){
                        $q->wherenull('fec_fin_reserva');
                        $q->where('fec_reserva',$fec_desde->format('Y-m-d'));
                    });
                    $q->orwhere(function($q) use($fec_desde,$fec_hasta,$r){
                        $q->whereraw("'".$fec_desde->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                        $q->orwhereraw("'".$fec_hasta->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                        $q->orwherebetween('fec_reserva',[$fec_desde,$fec_hasta]);
                        $q->orwherebetween('fec_fin_reserva',[$fec_desde,$fec_hasta]);
                    });
                })
                ->where('mca_anulada','N')
                ->where(function($q){
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                })
                ->get();
            $puestos_reservados=array_merge($puestos_reservados,$reservas->pluck('id_puesto')->toArray());
        }
        //dd($puestos_reservados);
        $puestos_usuarios=[];
        //Vamos a mirar que puestos estan disponibles todos los dias que ha solicitado el usuario
        $period = CarbonPeriod::create($f1,$f2);
        foreach($period as $p){
            $asignados_usuarios=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                ->join('users','users.id','puestos_asignados.id_usuario')    
                // ->where('id_usuario','<>',Auth::user()->id)
                ->where(function($q){
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                })
                ->where(function($q) use($r,$p){
                    $q->wherenull('fec_desde');
                    $q->orwhereraw("'".$p."' between fec_desde AND fec_hasta");
                })
                ->get();
            if(isset($asignados_usuarios)){
                $puestos_usuarios=array_merge($puestos_usuarios,$asignados_usuarios->pluck('id_puesto')->toArray());
        }}
     
         
        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        if(isset($asignados_nomiperfil)){
            $puestos_nomiperfil=$asignados_nomiperfil->pluck('id_puesto')->toArray();
        } else{
            $puestos_nomiperfil=[];
        }

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color', 'puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
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
            ->where(function($q) use($r){
                if($r->tipo_puesto>0){
                    $q->where('puestos.id_tipo_puesto',$r->tipo_puesto);
                }
            })
            ->where(function($q) use($intervalo){
                if(session('CL')['mca_reserva_horas']=='S'){
                    $q->where('puestos.max_horas_reservar','>=',$intervalo);
                    $q->orwherenull('puestos.max_horas_reservar');
                    $q->orwhere('puestos.max_horas_reservar','0');
                    $q->orwhere('puestos.max_horas_reservar','');
                }
            })
            ->where(function($q) use($r){
                if($r->id_planta && $r->id_planta!=0){
                    $q->where('puestos.id_planta',$r->id_planta);
                }
            })
            ->where(function($q) use($r){
                if ($r->tags) {
                    if($r->andor=="true"){//Busqueda con AND
                        $puestos_tags=DB::table('tags_puestos')
                            ->select('id_puesto')
                            ->wherein('id_tag',$r->tags)
                            ->groupby('id_puesto')
                            ->havingRaw('count(id_tag)='.count($r->tags))
                            ->pluck('id_puesto')
                            ->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    } else { //Busqueda con OR
                        $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags); 
                    }
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
        $id_planta=$r->id_planta;

        //Actualizadmos la config del usaurio para mostrarle siempre ese tipo de vista
        $u=users::find(Auth::user()->id);
        $u->val_vista_puestos=$r->tipo;
        $u->save();

        return view('reservas.'.$r->tipo,compact('reservas','puestos','edificios','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','plantas_usuario','tipo_vista','id_planta'));
    }

    public function save(Request $r){
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        
        $puesto=puestos::find($r->id_puesto);
        $mensajes_error=[];
        $period = CarbonPeriod::create($f1,$f2);
        foreach($period as $p){
            $fec_desde=Carbon::parse($p->format('Y-m-d').' '.$r->hora_inicio);
            $fec_hasta=Carbon::parse($p->format('Y-m-d').' '.$r->hora_fin);
            //Si en el perfil le hemos puesto que pude reservar varios puestos del tipo no comprobamos si ya estaba pillado
            
            if(session('NIV')["mca_reserva_multiple"]=='N')
            {
                 //Primero comprobamos si tiene una reserva para ese dia de ese tipo de puesto
                $reservas=DB::table('reservas')
                    ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                    ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
                    ->join('users','reservas.id_usuario','users.id')
                    ->where('puestos.id_tipo_puesto',$puesto->id_tipo_puesto)
                    ->where('reservas.id_usuario',Auth::user()->id)
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
                    ->where('mca_anulada','N')
                    ->where('puestos.id_cliente',Auth::user()->id_cliente)
                    ->first();

                if(isset($reservas) && !isset($r->salas)){
                    $mensajes_error[]='Ya tiene una reserva para un puesto del tipo '.$reservas->des_tipo_puesto.' para el periodo ['.$fec_desde->format('d/m/Y H:i').' - '.$fec_hasta->format('d/m/Y H:i').'] que coincide en todo o en parte con la reserva que intenta hacer, anule primero la reserva que entra en conflicto con ésta';
                }

                //Despues comprobamos si tiene una asignacion para ese dia de ese tipo de puesto
                $asignado=DB::table('puestos_asignados')
                    ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
                    ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
                    ->join('users','puestos_asignados.id_usuario','users.id')
                    ->where('puestos.id_tipo_puesto',$puesto->id_tipo_puesto)
                    ->where('puestos_asignados.id_usuario',Auth::user()->id)
                    ->where(function($q) use($fec_desde,$fec_hasta){
                        $q->where(function($q){
                            $q->wherenull('fec_desde');
                            $q->orwherenull('fec_hasta');
                        });
                        $q->orwhereraw("'".$fec_desde->format('Y-m-d')."' between fec_desde AND fec_hasta");
                    })
                    ->where('puestos.id_cliente',Auth::user()->id_cliente)
                    ->first();

                if(isset($asignado)){
                    $mensajes_error[]='Ya tiene un un puesto del tipo '.$asignado->des_tipo_puesto.' asignado para el '.$fec_desde->format('d/m/Y');
                }
            }
           

            //Ahora hay que comprobar que no han pillado el puesto mientras el usuario se lo pensabe
            $ya_esta=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->where('puestos.id_puesto',$puesto->id_puesto)
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
                ->where('mca_anulada','N')
                ->where('puestos.id_cliente',Auth::user()->id_cliente)
                ->first();

            if($ya_esta){//Ya esta pillado
                $mensajes_error[]='El puesto no esta disponible en el horario ['.beauty_fecha($fec_desde).' - '.beauty_fecha($fec_hasta).'] elija otro'.chr(13);
            }

        }
        if(count($mensajes_error)>0){
            return [
                'title' => "Reservas",
                'alert' => implode('<br>'.chr(13),$mensajes_error)
                //'url' => url('sections')
            ];
        }

        //Si todo ha ido bien y no hemos devuelto ningun mensaje de error entonces podemos insertar
        $id_reserva=[];
        foreach($period as $p){
            //Insertamos la nueva    
            $fec_desde=Carbon::parse($p->format('Y-m-d').' '.$r->hora_inicio);
            $fec_hasta=Carbon::parse($p->format('Y-m-d').' '.$r->hora_fin);        
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
            $id_reserva[]=$res->id_reserva;
        }
        if(isset($r->mca_ical) && $r->mca_ical=='S'){
            $det_puesto=puestos::find($r->id_puesto);
            if(isset($r->salas)){
                $des_evento="Reserva de sala de reunion [".$det_puesto->cod_puesto."] ".$det_puesto->des_puesto;
                $tipo="la sala de reunion";
            } else {
                $des_evento="Reserva de puesto [".$det_puesto->cod_puesto."] ".$det_puesto->des_puesto;
                $tipo="el puesto";
            }
            $body="Tiene reservado ".$tipo. " [".$det_puesto->cod_puesto."] ".$det_puesto->des_puesto." con los siguientes identificadores de reserva: ".implode(",",$id_reserva);
            $subject="Detalles de su reserva con Spotdesking";
            $user=users::find(Auth::user()->id);
            $cal=Calendar::create('Reserva de puestos Spotdesking');
            foreach($period as $p){
                $evento=Event::create()
                    ->name($des_evento)
                    ->description($body)
                    ->uniqueIdentifier(implode(",",$id_reserva))
                    ->organizer(Auth::user()->email, Auth::user()->name)
                    ->createdAt(Carbon::now())
                    ->startsAt(Carbon::parse($p->format('Y-m-d').' '.$r->hora_inicio))
                    ->endsAt(Carbon::parse($p->format('Y-m-d').' '.$r->hora_fin));
                $cal->event($evento);
            }

            $cal=$cal->get();
            \Mail::send('emails.plantilla_generica', ['user' => $user,'body'=>$body], function ($m) use ($user,$subject,$cal) {
                if(config('app.env')=='local'){//Para que en desarrollo solo me mande los mail a mi
                    $m->to('nomecansum@gmail.com', $user->name)->subject($subject);
                } else {
                    $m->to($user->email, $user->name)->subject($subject);
                }
                $m->from(config('mail.from.address'),config('mail.from.name'));
                $m->attachData($cal,"reserva_".Auth::user()->id."_".Carbon::now()->format('Ymdhi').".ics",[
                    'mime'=>'text/calendar'
                ]);
            });
           
        }
        return [
            'title' => "Reservas",
            'mensaje' => 'Puesto '.$r->des_puesto.' reservado. Identificadores de reserva: '.implode(",",$id_reserva),
            'fecha' => '['.$fec_desde->format('d/m/Y H:i').' - '.$fec_hasta->format('d/m/Y H:i').']',
            'fec_ver' => $fec_desde->format('Y-m-d'),
            //'url' => url('puestos')
        ];
    
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

    public function puestos_usuario(Request $r, $id,$desde,$hasta){
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
                //$q->whereraw("date(fec_reserva) between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q) use($desde,$hasta){
                    $q->whereraw("'".$desde."' between fec_reserva AND fec_fin_reserva");
                    $q->orwhereraw("'".$hasta."' between fec_reserva AND fec_fin_reserva");
                    $q->orwherebetween('fec_reserva',[$desde,$hasta]);
                    $q->orwherebetween('fec_fin_reserva',[$desde,$hasta]);
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
            ->wherenotnull('id_usuario')
            ->where(function($q) use($desde,$hasta){
                $q->whereraw("date(fec_Desde) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->whereraw("date(fec_hasta) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q){
                    $q->wherenull('fec_desde');
                    $q->wherenull('fec_hasta');
                });    
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
            ->where('id_perfil',$usuario->id_perfil)
            ->where(function($q) use($desde,$hasta){
                $q->whereraw("date(fec_Desde) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->whereraw("date(fec_hasta) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q){
                    $q->wherenull('fec_desde');
                    $q->wherenull('fec_hasta');
                });    
            })
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',$usuario->id_perfil)
            ->where(function($q) use($desde,$hasta){
                $q->whereraw("date(fec_Desde) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->whereraw("date(fec_hasta) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q){
                    $q->wherenull('fec_desde');
                    $q->wherenull('fec_hasta');
                });    
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
            ->when($puestos_usuarios, function($q) use($puestos_usuarios){
                $q->wherenotin('id_puesto',$puestos_usuarios);
            })
            ->when($puestos_nomiperfil, function($q) use($puestos_nomiperfil){
                $q->wherenotin('id_puesto',$puestos_nomiperfil);
            })
            ->where(function($q) use($plantas_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('puestos.id_planta',$plantas_usuario??[]);
                }
            })
            ->wherenotin('puestos.id_estado',[4,5,6])
            ->where(function($q) use($r){
                if(isset($r->lista_id) && is_array($r->lista_id)){
                    $q->wherein('puestos.id_puesto',$r->lista_id);
                }
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();


        $puestos_check=[];
        $checks=0; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_usuario/";
        if(isset($r->lista_id) && is_array($r->lista_id)){ //Si viene por POST es que estamos en reserva multiple desde listado de puestos
            return [
                'title' => "Reserva multiple",
                'message' => $puestos->count().' puestos disponibles de '.count($r->lista_id).' puestos solicitados para para el periodo  '.beauty_fecha($r->desde).' - '.beauty_fecha($r->hasta),
                'recomendacion'=>$puestos->count()!=count($r->lista_id)?'Libere primero las reservas existentes en los puestos o reserve solo los puestos disponibles':'',
                'lista'=>$puestos->pluck('id_puesto')->toArray()
            ]; 
        } else {  //Si viene por GET es que estamos en usuario ->crear reserva
            return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
        }
            
         
        
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
        notificar_usuario($usuario,"<span class='super_negrita'>Asignacion de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'Se le ha asignado un nuevo puesto</span>",'emails.asignacion_puesto',$str_notificacion,1,"alerta_05");
        return [
            'title' => "Reservas",
            'message' => 'Reserva  de puesto '.$r->des_puesto.' creada para el usuario '.$usuario->name.' para el periodo  '.$r->desde.' - '.$r->hasta,
            //'fecha' => Carbon::parse(adaptar_fecha($r->fechas))->format('Ymd'),
            //'url' => url('puestos')
        ];
    }

    public function cancelar_reserva_puesto($id){
        $p=DB::table('puestos')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$id)
            ->first();
        
        if(!isset($p)){
            //Error puesto no encontrado
            return [
                'tipo'=>'ERROR',
                'mensaje'=>"Error, puesto no encontrado"
            ];
        } else {    
            //A ver si tiene reserva
            $reserva=DB::table('reservas')
                ->where('id_puesto',$p->id_puesto)
                ->where(function($q){
                    $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                    $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
                })
                ->get();

            if($reserva->count()==1){
                $res=reservas::find($reserva->first()->id_reserva);
                $res->mca_anulada='S';
                $res->fec_utilizada=Carbon::now();
                $res->save();
                savebitacora("Cancelada reserva ".$res->id_reserva. "por el administrador","Reservas","cancelar_reserva_puesto","OK");
                return [
                    'tipo'=>'OK',
                    'mensaje'=>"Reserva ".$res->id_reserva." cancelada",
                    'id'=>$p->id_puesto
                ];
            } else if($reserva->count()>1) {
            return [
                'tipo'=>'Warning',
                'mensaje'=>"El puesto tiene varias reservas para hoy",
                'reservas'=>$reserva
            ];
            } else {
                return [
                    'tipo'=>'OK',
                    'mensaje'=>"El puesto no tenia reservas para hoy"
                ];
            }
         }
    }

    public function reservas_multiples_admin(Request $r){
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $f = explode(' - ',$r->rango);
        $f1 = Carbon::parse(adaptar_fecha($f[0]));
        $f2 = Carbon::parse(adaptar_fecha($f[1]));

        //Primero la accion, a ver si hay que borrar o crear
        if ($r->accion=="D"){//Borrar
            //El borrado es mas simple, nos cepillamos todo 
            $reservas=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->wherein('puestos.id_puesto',$r->lista_id)
                ->wherebetween('fec_reserva',[$f1,$f2])
                ->where('puestos.id_cliente',Auth::user()->id_cliente)
                ->get();
            foreach($reservas as $res){
                DB::table('reservas')->where('id_reserva',$res->id_reserva)->delete();
                $user_puesto=users::find($res->id_usuario);
                $str_notificacion=Auth::user()->name.' ha realizado una cancelacion masiva de reservas ';
                $str_respuesta=' Se ha cancelado su reserva de puesto que tenía para el día '.Carbon::parse($res->fec_reserva)->format('d/m/Y');
                
                notificar_usuario($user_puesto,"<span class='super_negrita'>Cambio en su reserva de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'>Se han producido cambios en su reserva de puesto</span>",'emails.asignacion_puesto',$str_notificacion.$str_respuesta,1,"alerta_03"); 
            }
            savebitacora(" Se han eliminado las reservas para ".count($r->lista_id).'puestos ['.implode(",",$r->lista_id).']en el intervalo'.$r->rango.' por cancelacion masiva creada por '.Auth::user()->name,"Usuarios","reservas_multiples_admin","OK");
            return [
                'tipo'=>'OK',
                'title'=>'Cancelacion masiva de reservas',
                'mensaje'=>"Se han eliminado las reservas para ".count($r->lista_id).' puestos en el intervalo '.$r->rango
            ];

        } else {
            $period = CarbonPeriod::create($f1, $f2);
            $lista_puestos_pillados=[];
            $lista_nombres_puestos=[];
            $hora_inicio=$r->hora_inicio??'00:00:00';
            $hora_fin=$r->hora_fin??'23:59:59';
            foreach($period as $fecha){
                foreach($r->lista_id as $id_puesto){
                    //Ahora hay que comprobar que no han pillado el puesto mientras el usuario se lo pensabe
                    $ya_esta=DB::table('reservas')
                        ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                        ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
                        ->join('users','reservas.id_usuario','users.id')
                        ->where('puestos.id_puesto',$id_puesto)
                        ->where(function($q) use($hora_inicio,$hora_fin,$fecha){
                            $q->where(function($q) use($fecha){
                                $q->wherenull('fec_fin_reserva');
                                $q->wheredate('fec_reserva',$fecha);
                            });
                            $q->orwhere(function($q) use($fecha){
                                $q->whereraw("'".$fecha->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                            });
                            $q->orwhere(function($q) use($hora_inicio,$hora_fin,$fecha){
                                $q->whereraw("'".$fecha->format('Y-m-d')." ".$hora_inicio."' between fec_reserva AND fec_fin_reserva");
                                $q->orwhereraw("'".$fecha->format('Y-m-d')." ".$hora_fin."' between fec_reserva AND fec_fin_reserva");
                                $q->orwherebetween('fec_reserva',[$fecha->format('Y-m-d').' '.$hora_inicio,$fecha->format('Y-m-d').' '.$hora_fin]);
                                $q->orwherebetween('fec_fin_reserva',[$fecha->format('Y-m-d').' '.$hora_inicio,$fecha->format('Y-m-d').' '.$hora_fin]);
                            });
                        })
                        ->where('reservas.id_usuario','<>',$r->id_usuario)
                        ->where('mca_anulada','N')
                        ->where('puestos.id_cliente',Auth::user()->id_cliente)
                        ->first();

                    $asignados_usuarios=DB::table('puestos_asignados')
                        ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                        ->join('users','users.id','puestos_asignados.id_usuario')    
                        ->where('puestos.id_puesto',$id_puesto)
                        ->where(function($q){
                            $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                        })
                        ->where(function($q) use($r,$fecha){
                            $q->wherenull('fec_desde');
                            $q->orwhereraw("'".$fecha."' between fec_desde AND fec_hasta");
                        })
                        ->first();

                    if(!isset($ya_esta) && !isset($asignados_usuarios)){
                        //Vale, podemos jugar
                        $puesto=puestos::find($id_puesto);
                        $lista_nombres_puestos[]=$puesto->cod_puesto;
                        $res=new reservas;
                        $res->id_puesto=$id_puesto;
                        $res->id_usuario=$r->id_usuario;
                        $res->fec_reserva=Carbon::parse($fecha->format('Y-m-d').' '.$hora_inicio);
                       
                        if($puesto->max_horas_reservar && $puesto->max_horas_reservar>0 && is_int($puesto->max_horas_reservar) && session('CL')['mca_reserva_horas']=='S'){
                            $res->fec_fin_reserva=$res->fec_reserva->addHour($puesto->max_horas_reservar);   
                        } else {
                            $res->fec_fin_reserva=carbon::parse($fecha->format('Y-m-d').' '.$hora_fin);   
                        }
                        $res->id_cliente=$puesto->id_cliente;
                        $res->save();
                    } else {
                        $lista_puestos_pillados[]=$id_puesto;
                    }
                    $usuario=users::find($r->id_usuario);
                    $str_notificacion=Auth::user()->name.' ha creado una nueva reserva de los puestos '.implode(",",$lista_nombres_puestos).' para usted en el intervalo '.$r->rango;
                    //notificar_usuario($usuario,"<span class='super_negrita'>Reserva de puesto creada por administrador....<br></span>Estimado usuario:<br><span class='super_negrita'>Se le ha reservado un nuevo puesto</span>",'emails.asignacion_puesto',$str_notificacion,1,"alerta_05");
                    $mensaje="Se han creado ".count($lista_nombres_puestos)." reservas de ".(count($r->lista_id)-count(array_unique($lista_puestos_pillados))).' puestos para el usuario '.$usuario->name.' y el intervalo '.$r->rango;
                    if(count($lista_puestos_pillados)>0){
                        $mensaje.="<br>Se detectaron ".count($lista_puestos_pillados)." conflictos con otras reservas realizadas por otros usuarios o puestos asignados a nominalmente a usuarios durante este proceso en alguna de las fechas, por lo tanto no pueden ser reservados";
                    }
                    savebitacora(' Se han creado reservas de puesto para los puestos'.implode(",",$lista_nombres_puestos).' para el usuario '.$usuario->name.' para el periodo  '.$r->rango.' creada por '.Auth::user()->name,"Usuarios","reservas_multiples_admin","OK");
                }
            }
            return [
                'tipo'=>'OK',
                'title'=>'Creacion masiva de reservas',
                'mensaje'=>$mensaje,
                'pillados'=>array_unique($lista_puestos_pillados)
            ];
        }
    }
}
