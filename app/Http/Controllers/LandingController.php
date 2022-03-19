<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use carbon;
use App\Models\contactos;
use App\Models\contactos_producto;
use App\Models\marcas;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    //
    public function save(Request $r){

    try{    //Vamos a guardar
        $contacto=new contactos;
        $contacto->nombre=$r->name;
        $contacto->email=$r->email;
        $contacto->empresa=$r->empresa;
        $contacto->mensaje=$r->message;
        $contacto->mca_acepto=$r->chk_acepto??'N';
        $contacto->mca_enviar=$r->chk_mandar??'N';
        $contacto->token=Str::random(50);
        $contacto->save();
        Cookie::queue('landing', $contacto->id_contacto, 999999);
        return [
                'title' => "OK",
                'message' => 'Registro completado!',
                'id' => $contacto->id_contacto
            ];
        } catch (\Exception $exception) {
            return [
                'title' => "Estado de puesto",
                'error' => 'ERROR: Ocurrio un error guardando contacto '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }
    }

    public function products(){

        $cookie=Cookie::get('landing');
        //Vamos a ver si estab registrado
        $esta=contactos::find($cookie);
        if(isset($esta)){
            return view('landing.products',compact('esta'));
        } else {
            return redirect('welcome');
        }
    }

    public function save_product(Request $r){

        $cookie=Cookie::get('landing');
        //Vamos a ver si estab registrado
        $esta=contactos::find($cookie);
        if(isset($esta)){
            $cp=new contactos_producto;
            $cp->id_contacto=$r->id;
            $cp->id_producto=$r->product;
            $cp->save();
            return [
                'title' => "OK",
                'message' => 'Se ha registrado su solicitud. Muchas gracias'
            ];
        } else {
            return redirect('welcome');
        }
    }

    public function save_product2($marca,$persona){

        //https://192.168.1.103/landing/asoc/25/3d92ea00171d73c38b076baa96a1a40d
        $esta=contactos::where('token',$persona)->first();
        $detalles=marcas::where('token',$marca)->first();
        if(isset($esta)){
            $cp=new contactos_producto;
            $cp->id_contacto=$esta->id_contacto;
            $cp->id_producto=$detalles->id_marca;
            $cp->id_usuario_com=Auth::user()->id??null;
            $cp->save();
            return view('landing.gracias',compact('detalles'));
        } else {
            return redirect('welcome');
        }
    }

    public function get_marca($marca,$persona=0){

        $cookie=Cookie::get('landing');
        //Vamos a ver si estab registrado
        $esta=contactos::find($cookie);
        $detalles=marcas::where('token',$marca)->first();

        if(isset($esta)){
            $cp=new contactos_producto;
            $cp->id_contacto=$esta->id_contacto;
            $cp->id_producto=$detalles->id_marca;
            $cp->save();
            return view('landing.gracias',compact('detalles'));
        } else {
            return redirect(url('/landing/scan',$marca));
        }
    }

    public function scan($id=''){
        $estado_destino=1;
        $modo='usuario';
        $titulo='';
        $tipo_scan="main";
        return view('landing.scan',compact('estado_destino','modo','titulo','tipo_scan','id'));
    }
}
