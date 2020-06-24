<?php

namespace App\Http\Controllers;
use App\Models\clientes;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\puestos;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MKDController extends Controller
{
    public function plano($planta,$token){

        if(!authbyToken($token)){
            $mensaje="No tiene permiso para ver esa informacion";
            return view('genericas.error',compact('mensaje'));
        }

        validar_acceso_tabla($planta,'plantas');
        $cliente=clientes::find(Auth::user()->id_cliente)->first();
        $plantas = plantas::findOrFail($planta);
        $edificio=edificios::find($plantas->id_planta)->first();
        $puestos= DB::Table('puestos')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->where('id_planta',$planta)
            ->get();
        $lista_ids=$puestos->pluck('id_puesto')->toArray();
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->wherein('reservas.id_puesto',$lista_ids)
            ->get();

        return view('mkd.plano',compact('plantas','puestos','reservas','cliente','edificio'));
        
    }
}
