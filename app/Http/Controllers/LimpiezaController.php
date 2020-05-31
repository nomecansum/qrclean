<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\users;
use PDF;


class LimpiezaController extends Controller
{
    public function index(){

        $usuarios=DB::table('users')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();

        $rondas=DB::table('rondas_limpieza')
            ->join('limpiadores_ronda','limpiadores_ronda.id_ronda','rondas_limpieza.id_ronda')
            ->join('users as u1','rondas_limpieza.user_creado','u1.id')
            ->join('users as u2','limpiadores_ronda.id_limpiador','u2.id')
            ->select('fec_ronda','des_ronda','rondas_limpieza.id_ronda','u1.name as user_creado')
            ->selectraw("group_concat(u2.name SEPARATOR '#') as user_asignado") 
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->whereBetween('fec_ronda',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])
            ->groupby('rondas_limpieza.id_ronda','fec_ronda','des_ronda','u1.name')
            ->get();

        $detalles=DB::table('rondas_limpieza')
            ->select('puestos_ronda.*','puestos.cod_puesto','puestos.id_edificio','puestos.id_planta','puestos.id_estado')
            ->join('puestos_ronda','puestos_ronda.id_ronda','rondas_limpieza.id_ronda')
            ->join('puestos','puestos_ronda.id_puesto','puestos.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->whereBetween('fec_ronda',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])
            ->get();
        return view('limpieza.index',compact('rondas','detalles','usuarios'));
    }

    public function view($id,$print=0){
        $ronda=DB::table('rondas_limpieza')
            ->join('users as u1','rondas_limpieza.user_creado','u1.id')
            ->join('clientes','rondas_limpieza.id_cliente','clientes.id_cliente')
            ->where('rondas_limpieza.id_ronda',$id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->first();

        $limpiadores=DB::table('limpiadores_ronda')
            ->join('users','limpiadores_ronda.id_limpiador','users.id')
            ->where('id_ronda',$ronda->id_ronda)
            ->get();

        $detalles=DB::table('puestos_ronda')
            ->select('puestos_ronda.*','puestos.cod_puesto','puestos.id_edificio','puestos.id_planta','puestos.id_estado','edificios.des_edificio','plantas.des_planta','estados_puestos.des_estado','estados_puestos.val_color','users.name')
            ->join('puestos','puestos_ronda.id_puesto','puestos.id_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->leftjoin('users','puestos_ronda.user_audit','users.id')
            ->where('id_ronda',$ronda->id_ronda)
            ->get();

        if($print==0){
            return view('limpieza.detalle',compact('ronda','limpiadores','detalles','print'));
        } else{
            $filename='Ronda de limpieza #'.$id.'.pdf';
            $pdf = PDF::loadView('limpieza.detalle',compact('ronda','limpiadores','detalles','print'));
            return $pdf->download($filename);
        }

        
    }
}
