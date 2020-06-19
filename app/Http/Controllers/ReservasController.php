<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;


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
}
