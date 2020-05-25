<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;

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
            ->take(35)
            ->get();
        return view('puestos.index',compact('puestos'));
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

            validar_acceso_tabla($id,"puestos");
            if($r->id==0){
                $puesto=puestos::create($r->all());
            } else {
                $puesto=puestos::find($r->id);
                $puesto->update($r->all());
            }
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

    public function print_qr(){

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->get();

        return view('puestos.print_qr',compact('puestos'));
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
}
