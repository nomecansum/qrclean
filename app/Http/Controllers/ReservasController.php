<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\reservas;


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
            ->select('reservas.fec_reserva','reservas.fec_fin_reserva','puestos.cod_puesto','puestos.des_puesto','edificios.des_edificio','plantas.des_planta')
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

    public function comprobar_puestos(Request $r){
        $plantas_usuario=DB::table('plantas_usuario')
            ->where('id_usuario',Auth::user()->id)
            ->pluck('id_planta')
            ->toArray();

        $reservados=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->where('puestos.id_edificio',$r->edificio)
            ->where('fec_reserva',adaptar_fecha($r->fecha))
            ->pluck('reservas.id_puesto')
            ->toarray();
        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where(function($q){
                if(session('CL')->mca_restringir_usuarios_planta=='S'){
                    $q->wherein('puestos.id_planta',$plantas_usuario);
                }
            })
            ->orderby('edificios.des_edificio')
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
            ->where('id_edificio',$r->edificio)
            ->get();

        return view('reservas.comprobar',compact('reservados','puestos','edificios'));
    }

    public function save(Request $r){
        //dd($r->all());

        //Primero hay que comprobar que no han pillado el pñuesto mientras el usuario se lo pensabe

        $ya_esta=reservas::where('id_puesto',$r->id_puesto)->first();
        if($ya_esta){
            //Ya esta pillado
            return [
                'title' => "Reservas",
                'error' => 'El puesto ya ha sido reservado mientras estaba eligiendo, elija otro',
                //'url' => url('sections')
            ];

        } else{
            $res=new reservas;
            $res->id_puesto=$r->id_puesto;
            $res->id_usuario=Auth::user()->id;
            $res->fec_reserva=adaptar_fecha($r->fechas);
            $res->save();
            return [
                'title' => "Reservas",
                'mensaje' => 'Puesto '.$r->des_puesto.' reservado. Identificador de reserva: '.$res->id_reserva,
                //'url' => url('puestos')
            ];
        }

        
    }
}
