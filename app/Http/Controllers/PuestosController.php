<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PuestosController extends Controller
{
    //
    public function index(){

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',Auth::user()->id_cliente)
            ->get();
        return view('puestos.index',compact('puestos'));
    }

    public function edit($id){
        if($id==0){
            $puesto=new puestos;
            $puesto->id=0;
        } else {
            $puesto=puestos::find($id);
        }

        return view('puestos.edit',compact('puesto'));

    }

    public function ver_puesto($id){
        $puesto=puestos::find($id);
        $url_puesto=explode("?",$puesto->url);
        $url=$url_puesto[0]."?800X600";
        return view('puestos.view',compact('puesto','url'));

    }

    public function delete($id){
        $puesto=puestos::find($id);
        $puesto->delete();
        flash('puesto '.$puesto->etiqueta.' Borrada')->success();
        return redirect('/puestos');

    }

    public function update(Request $r){
        try{


            if($r->id==0){
                $puesto=puestos::create($r->all());
            } else {
                $puesto=puestos::find($r->id);
                $puesto->update($r->all());
            }
            $puesto->save();
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
            ->where('puestos.id_cliente',Auth::user()->id_cliente)
            ->get();

        return view('puestos.print_qr',compact('puestos'));
    }
}
