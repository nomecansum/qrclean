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
        $edificio=edificios::find($plantas->id_edificio)->first();
        $puestos= DB::Table('puestos')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->where('id_planta',$planta)
            ->get();
        $lista_ids=$puestos->pluck('id_puesto')->toArray();
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('reservas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',Auth::user()->id)
            ->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->get();

        return view('mkd.plano',compact('plantas','puestos','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','cliente','edificio'));
        
    }

    public function datos_plano($planta,$token){
        validar_acceso_tabla($planta,'plantas');
        $puestos= DB::Table('puestos')
            ->select('puestos.id_puesto','cod_puesto','puestos.id_estado','estados_puestos.val_color')
            ->selectraw('(select(fec_reserva) from reservas where reservas.id_puesto=puestos.id_puesto and fec_reserva=date(now())) as fec_reserva')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->where('id_planta',$planta)
            ->get();
        $puestos=json_encode($puestos);
        return $puestos;       
    }
}
