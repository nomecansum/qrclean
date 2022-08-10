<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

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
                ->where(function($q){
                    if (isSupervisor(Auth::user()->id)) {
                        $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                        $q->wherein('puestos.id_puesto',$puestos_usuario);
                    }
                })
                ->get(),

            "tags" => DB::table('tags')
                ->select('clientes.id_cliente','clientes.nom_cliente','tags.id_tag','tags.nom_tag')
                ->join('clientes','clientes.id_cliente','tags.id_cliente')
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
                ->where(function($q){
                    if (isSupervisor(Auth::user()->id)) {
                        $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                        $q->wherein('puestos.id_puesto',$puestos_usuario);
                    }
                })
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
                ->where(function($q){
                    if (isSupervisor(Auth::user()->id)) {
                        $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                        $q->wherein('puestos.id_puesto',$puestos_usuario);
                    }
                })
                ->get()
        ];
    }

    public function combo_limpiadores(Request $r){

        $clientes=DB::table('puestos')
            ->join('clientes','clientes.id_cliente','puestos.id_cliente')
            ->wherein('puestos.id_puesto',$r->lista_id)
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->pluck('nom_cliente','clientes.id_cliente')
            ->unique();

        $usuarios=DB::table('users')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->wherein('users.id_cliente',array_keys($clientes->toarray()))
            ->where(function($q) use($r){
                if($r->tipo=='L')
                    {   
                        $q->where('nivel_acceso',config('app.id_perfil_personal_limpieza')); 
                    } else {
                        $q->where('nivel_acceso',config('app.id_perfil_personal_mantenimiento')); 
                    }
                            
            })            
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        
        return view('resources.combo_limpiadores',compact('usuarios','clientes'));
    }

    public function combo_plantas($id_edificio){

        $plantas=DB::table('plantas')
            ->where('id_edificio',$id_edificio)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        return view('resources.combo_plantas',compact('plantas'));
    }

    public function combo_plantas_salas($id_edificio){

        $plantas=DB::table('plantas')
            ->select('plantas.id_planta','plantas.des_planta')
            ->join('puestos','plantas.id_planta','puestos.id_planta')
            ->where('plantas.id_edificio',$id_edificio)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_sala'))
            ->distinct()
            ->get();
        return view('resources.combo_plantas',compact('plantas'));
    }


    public function combo_edificios($id_cliente){
        $edificios=DB::table('edificios')
            ->where('id_cliente',$id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        return view('resources.combo_edificios',compact('edificios'));
    }

    public function combo_paises($id_cliente){
        $datos = DB::table('provincias')
            ->select('nom_pais as nombre', 'id_pais as id')
			->leftjoin("edificios", "provincias.id_prov", "edificios.id_provincia")
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
			->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_cliente', $id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->orderby('nom_pais')
            ->distinct()
            ->get();
        return view('resources.combo_generico',compact('datos'));
    }
    public function combo_regiones($id_cliente){
        $datos = DB::table('provincias')
            ->select('regiones.cod_region as id', 'nom_region as nombre')
            ->leftjoin("edificios", "provincias.id_prov", "edificios.id_provincia")
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
            ->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_cliente', $id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->orderby('nom_region')
            ->distinct()
            ->get();
        return view('resources.combo_generico',compact('datos'));
    }
    public function combo_provincias($id_cliente){
        $datos = DB::table('provincias')
            ->select('id_prov as id', 'nombre')
            ->leftjoin("edificios", "provincias.id_prov", "edificios.id_provincia")
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
            ->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_cliente', $id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->orderby('nombre')
            ->distinct()
            ->get();
        return view('resources.combo_generico',compact('datos'));
    }

    
    public function search_clientes_json(Request $r){
        $r->searchTerm=strtoupper($r->searchTerm);
        if(strlen($r->searchTerm)<3 || !isset($r->searchTerm)){
            return json_encode(["1"]);
        }
        $clientes=DB::table('clientes')
        ->select('clientes.id_cliente as id','clientes.nombre_cliente as text')
		->where(function($q){
            if (!fullAccess()) {
                $q->WhereIn('id_cliente',clientes());
            }
            if (session('id_cliente')) {
                $q->where('id_cliente',session('id_cliente'));
            }
        })
        ->where('clientes.nombre_cliente', 'LIKE', "%{$r->searchTerm}%")
        ->orderby('nombre_cliente')
        ->get();
        if((fullAccess() || $clientes->count()>100) && (strlen($r->searchTerm)<3 || !isset($r->searchTerm))){
            return json_encode(["2"]);
        } else {
            return json_encode($clientes);
        }
        
        //return view('combos.fill_combo_clientes',compact('clientes'));
    }

}
