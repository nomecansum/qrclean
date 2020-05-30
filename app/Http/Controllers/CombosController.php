<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class CombosController extends Controller
{
    ////////////MANEJO DE LOS COMBOS DE FILTRO/////////////
    private function filtro_clientes($r){
        if (!isset($r->cliente)){
            if (fullaccess()){
                $r->cliente=DB::table('clientes')->wherenull('fec_borrado')->pluck('id_cliente')->toArray();
            } else {
                $r->cliente=DB::table('clientes')
                    ->wherein('id_cliente',clientes())
                    ->pluck('id_cliente');
            }
        } else{
            if(!fullaccess()){
                $r->cliente=DB::table('clientes')
                ->wherein('id_cliente',clientes())
                ->wherein('id_cliente',$r->cliente)
                ->pluck('id_cliente');
            }
        }
        return $r;
    }
    
    public function loadedificios(Request $r){
        $r=$this->filtro_clientes($r);
        return
        [
            "edificios" => DB::table('edificios')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio')
                ->join('clientes','clientes.id_cliente','edificios.id_cliente')
                ->wherein('clientes.id_cliente',$r->cliente)
                ->get(),

            "plantas" => DB::table('plantas')
                    ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta')
                    ->join('clientes','clientes.id_cliente','plantas.id_cliente')
                    ->join('edificios','edificios.id_edificio','plantas.id_edificio')
                    ->whereIn('clientes.id_cliente',$r->cliente)
                    ->get(),

            "puestos" => DB::table('puestos')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta','puestos.id_puesto','puestos.cod_puesto')
                ->join('clientes','clientes.id_cliente','puestos.id_cliente')
                ->join('edificios','edificios.id_edificio','puestos.id_edificio')
                ->join('plantas','plantas.id_planta','puestos.id_planta')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->get()
        ];
    }

    public function loadplantas(Request $r){

        $r=$this->filtro_clientes($r);
        if (!$r->edificio) {
            $r->edificio=DB::table('edificios')->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        } else{
            $r->edificio=DB::table('edificios')->wherein('id_edificio',$r->edificio)->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        }

        return
        [
            "plantas" => DB::table('plantas')
                    ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta')
                    ->join('clientes','clientes.id_cliente','plantas.id_cliente')
                    ->join('edificios','edificios.id_edificio','plantas.id_edificio')
                    ->whereIn('clientes.id_cliente',$r->cliente)
                    ->wherein('edificios.id_edificio',$r->edificio)
                    ->get(),

            "puestos" => DB::table('puestos')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta','puestos.id_puesto','puestos.cod_puesto')
                ->join('clientes','clientes.id_cliente','puestos.id_cliente')
                ->join('edificios','edificios.id_edificio','puestos.id_edificio')
                ->join('plantas','plantas.id_planta','puestos.id_planta')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->wherein('edificios.id_edificio',$r->edificio)
                ->get()
        ];
    }

    public function loadpuestos(Request $r){

        $r=$this->filtro_clientes($r);
        if (!$r->edificio) {
            $r->edificio=DB::table('edificios')->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        } else{
            $r->edificio=DB::table('edificios')->wherein('id_edificio',$r->edificio)->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        }

        if (!$r->planta) {
            $r->planta=DB::table('plantas')->wherein('id_cliente',$r->cliente)->pluck('id_planta');
        } else{
            $r->planta=DB::table('plantas')->wherein('id_planta',$r->planta)->wherein('id_cliente',$r->cliente)->pluck('id_planta');
        }

        return
        [
            "puestos" => DB::table('puestos')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta','puestos.id_puesto','puestos.cod_puesto')
                ->join('clientes','clientes.id_cliente','puestos.id_cliente')
                ->join('edificios','edificios.id_edificio','puestos.id_edificio')
                ->join('plantas','plantas.id_planta','puestos.id_planta')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->wherein('edificios.id_edificio',$r->edificio)
                ->wherein('plantas.id_planta',$r->planta)
                ->get()
        ];
    }

    public function combo_limpiadores(Request $r){
        $clientes=DB::table('puestos')
            ->join('clientes','clientes.id_cliente','puestos.id_cliente')
            ->wherein('puestos.id_puesto',$r->lista_id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->pluck('nom_cliente','clientes.id_cliente')
            ->unique();

        $usuarios=DB::table('users')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->wherein('users.id_cliente',array_keys($clientes->toarray()))
            ->where('nivel_acceso',config('app.id_perfil_personal_limpieza'))
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        
        return view('resources.combo_limpiadores',compact('usuarios','clientes'));
    }

}
