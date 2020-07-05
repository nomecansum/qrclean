<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use App\Models\rondas;
use App\Models\puestos_ronda;
use App\Models\limpiadores;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use PDF;

class PuestosController extends Controller
{
    //
    public function index(){

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
            ->get();
        return view('puestos.index',compact('puestos'));
    }


    public function search(Request $r){

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
            ->where(function($q) use($r){
                if ($r->cliente) {
                    $q->WhereIn('puestos.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if ($r->edificio) {
                    $q->WhereIn('puestos.id_edificio',$r->edificio);
                }
            })
            ->where(function($q) use($r){
                if ($r->planta) {
                    $q->whereIn('puestos.id_planta',$r->planta);
                }
            })
            ->whereExists(function($q) use($r){
                if ($r->puesto) {
                    $q->whereIn('puestos.id_puesto',$r->puesto);
                }
            })
            ->whereExists(function($q) use($r){
                if ($r->estado) {
                    $q->whereIn('puestos.id_estado',$r->estado);
                }
            })
            ->get();
        return view('puestos.fill-tabla',compact('puestos','r'));
    }

    public function edit($id){
        
        if($id==0){
            $puesto=new puestos;
            $puesto->id=0;
        } else {
            validar_acceso_tabla($id,"puestos");
            $puesto=puestos::find($id);
        }
        return view('puestos.edit',compact('puesto'));

    }

    public function ver_puesto($id){
        validar_acceso_tabla($id,"puestos");
        $puesto=puestos::find($id);
        $url_puesto=explode("?",$puesto->url);
        $url=$url_puesto[0]."?800X600";
        return view('puestos.view',compact('puesto','url'));

    }

    public function delete($id){
        validar_acceso_tabla($id,"puestos");
        $puesto=puestos::find($id);
        $puesto->delete();
        flash('puesto '.$puesto->etiqueta.' Borrada')->success();
        savebitacora('puesto '.$puesto->etiqueta. ' borrado',"Puestos","delete","OK");
        return redirect('/puestos');

    }

    public function update(Request $r){
        try{

            
            if($r->id_puesto==0){
                $puesto=puestos::create($r->all());
            } else {
                validar_acceso_tabla($r->id_puesto,"puestos");
                $puesto=puestos::find($r->id_puesto);
                $puesto->update($r->all());
            }
            $puesto->mca_acceso_anonimo=$r->mca_acceso_anonimo??'N';
            $puesto->mca_reservar=$r->mca_reservar??'N';
            $puesto->save();
            savebitacora('puesto '.$r->etiqueta. ' actualizado',"Puestos","Update","OK");
            return [
                'title' => "puestos",
                'message' => 'puesto '.$r->etiqueta. ' actualizado',
                'url' => url('puestos')
            ];
        } catch (Exception $exception) {
            return [
                'title' => "puestos",
                'error' => 'ERROR: Ocurrio un error actualizando el puesto '.$r->name.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];

        }
    }

    public function print_qr(Request $r){

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->wherein('id_puesto',$r->lista_id)
            ->get();
        
        $filename='Codigos_QR Puestos_'.Auth::user()->id_cliente.'_.pdf';
        $pdf = PDF::loadView('puestos.print_qr',compact('puestos'));
        return $pdf->download($filename);
        //return view('puestos.print_qr',compact('puestos'));
    }

    public function accion_estado(Request $r){

        $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            }
        })
        ->wherein('id_puesto',$r->lista_id)
        ->get();

        $e=DB::table('estados_puestos')->where('id_estado',$r->estado)->first();

        foreach($puestos as $puesto){
            $p=puestos::find($puesto->id_puesto);
            $p->id_estado=$r->estado;
            $p->fec_ult_estado=Carbon::now();
            $p->save();
            //Lo añadimos al log
            logpuestos::create(['id_puesto'=>$puesto->id_puesto,'id_estado'=>$r->estado,'id_user'=>Auth::user()->id,'fecha'=>Carbon::now()]);
            
        }

        savebitacora('Cambio de puestos '.implode(",",$r->lista_id). ' a estado '.$r->estado,"Puestos","accion_estado","OK");
        return [
            'title' => "Puestos",
            'mensaje' => count($r->lista_id).' puestos actualizados a '.$e->des_estado,
            'label'=>$e->des_estado,
            'color'=>$e->val_color,
            'url' => url('puestos')
        ];
    }

    public function mapa(){

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
        ->get();
        
        return view('puestos.mapa',compact('puestos','edificios'));
    }

    public function plano(){

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
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('reservas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();
        
        return view('puestos.plano',compact('puestos','edificios','reservas'));
    }

    public function ronda_limpieza(Request $r){
        //Primero asegurarnos de que tiene acceso para los puestos
        if($r->tip_ronda=='L')
        {
            $tipo_ronda="limpieza";
        } else {
            $tipo_ronda="mantenimiento";
        }
        $puestos=DB::table('puestos')
            ->wherein('id_puesto',$r->lista_id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();

        $usuarios=DB::table('users')
            ->wherein('id',$r->lista_limpiadores)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();

        $ronda=rondas::create(['fec_ronda'=>Carbon::now(),'des_ronda'=>$r->des_ronda,'user_creado'=>Auth::user()->id,'id_cliente'=>Auth::user()->id_cliente, 'tip_ronda'=>$r->tip_ronda]);
        
        foreach($usuarios as $u){
            limpiadores::create(['id_ronda'=>$ronda->id_ronda,'id_limpiador'=>$u->id]);
        }
        foreach($puestos as $p){
            puestos_ronda::create(['id_ronda'=>$ronda->id_ronda,'fec_inicio'=>Carbon::now(),'id_puesto'=>$p->id_puesto]);
        }
        

        //dd($ronda);
        savebitacora('Ruta de '.$tipo_ronda.' '.$r->des_ronda.' creada para '.count($r->lista_id).' puestos y '.count($r->lista_limpiadores).' empleados de '.$tipo_ronda,"Puestos","ronda_".$tipo_ronda,"OK");
        return [
            'title' => "Ronda de ".$tipo_ronda,
            'mensaje' => 'Ronda de '.$tipo_ronda.' '.$r->des_ronda.' creada para '.count($r->lista_id).' puestos y '.count($r->lista_limpiadores).' empleados de '.$tipo_ronda,
            //'url' => url('puestos')
        ];
    }

    public function cambiar_anonimo(Request $r){
        $estado=$r->estado??'N';
        $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            }
        })
        ->wherein('id_puesto',$r->lista_id)
        ->update(['mca_acceso_anonimo'=>$estado]);
        savebitacora('Cambiado el acceso anonimo a '.$estado.' para los puestos '.implode(', ',$r->lista_id),"Puestos","cambiar_anonimo","OK");
        return [
            'title' => "Acceso anonimo",
            'mensaje' => 'Cambiado el acceso anonimo a '.$estado.' para los puestos '.implode(', ',$r->lista_id),
            //'url' => url('puestos')
        ];
    }

    public function cambiar_reserva(Request $r){
        $estado=$r->estado??'N';
        $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            }
        })
        ->wherein('id_puesto',$r->lista_id)
        ->update(['mca_reservar'=>$estado]);
        savebitacora('Cambiado el permiso de reserva a '.$estado.' para los puestos '.implode(', ',$r->lista_id),"Puestos","cambiar_reserva","OK");
        return [
            'title' => "Acceso anonimo",
            'mensaje' => 'Cambiado el permiso de reserva a '.$estado.' para los puestos '.implode(', ',$r->lista_id),
            //'url' => url('puestos')
        ];
    }
}
