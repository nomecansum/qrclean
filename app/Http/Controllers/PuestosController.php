<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use App\Models\rondas;
use App\Models\puestos_ronda;
use App\Models\limpiadores;
use App\Models\tags;
use App\Models\tags_puestos;
use App\Models\users;
use App\Models\puestos_tipos;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use PDF;
use Illuminate\Support\Str;
use Redirect;


class PuestosController extends Controller
{
    //
    public function index(){

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','estados_puestos.des_estado','estados_puestos.val_color','estados_puestos.hex_color','clientes.nom_cliente','clientes.id_cliente','puestos_asignados.id_usuario','puestos_asignados.id_perfil', 'puestos.val_color as color_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->get();

        $tipos = DB::table('puestos_tipos')
            ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                    $q->orwhere('puestos_tipos.mca_fijo','S');
                }
            })
        ->get();

        return view('puestos.index',compact('puestos','tipos'));
    }

    public function search(Request $r){
        if($r->estado){
            $estados=$r->estado;
            $estados=array_filter($r->estado, "ctype_digit");
            $atributos=array_filter($r->estado, "ctype_alpha");
        } else {
            $estados=null;
            $atributos=[];
        }
        
        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','estados_puestos.des_estado','estados_puestos.val_color','estados_puestos.hex_color','clientes.nom_cliente','clientes.id_cliente','puestos_asignados.id_usuario','puestos_asignados.id_perfil', 'puestos.val_color as color_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
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
            ->where(function($q) use($r){
                if ($r->puesto) {
                    $q->whereIn('puestos.id_puesto',$r->puesto);
                }
            })
            ->where(function($q) use($r,$estados){
                if ($estados) {
                    $q->whereIn('puestos.id_estado',$estados);
                }
            })
            ->where(function($q) use($r,$atributos){
                if(in_array('A',$atributos)){
                    $q->where('mca_acceso_anonimo','S');
                }
                if(in_array('R',$atributos)){
                    $q->where('mca_reservar','S');
                }
                if(in_array('P',$atributos)){
                    $q->wherenotnull('puestos_asignados.id_perfil');
                }
                if(in_array('U',$atributos)){
                    $q->wherenotnull('puestos_asignados.id_usuario');
                }
            })
            ->where(function($q) use($r){
                if ($r->tags) {
                    $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                    $q->whereIn('puestos.id_puesto',$puestos_tags);
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->get();
        return view('puestos.fill-tabla',compact('puestos','r'));
    }

    public function edit($id){
        
        if($id==0){
            $puesto=new puestos;
            $puesto->id=0;
            $tags="";
        } else {
            validar_acceso_tabla($id,"puestos");
            $puesto=DB::table('puestos')
                ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
                ->where('puestos.id_puesto',$id)
                ->first();
        }
        $usuarios=DB::table('users')
            ->where('id_cliente',Auth::user()->id_cliente)
            ->orderby('name')
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('id')->toArray();
                    $q->wherein('users.id',$usuarios_supervisados);
                }
            })
            ->get();

        $perfiles=DB::table('niveles_acceso')
            ->wherein('id_cliente',[1,Auth::user()->id_cliente])
            ->where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('cod_nivel')->toArray();
                    $q->wherein('niveles_acceso.cod_nivel',$usuarios_supervisados);
                }
            })
            ->orderby('val_nivel_acceso')
            ->orderby('des_nivel_acceso')
            ->get();
        $tags=DB::table('tags')
            ->join('tags_puestos','tags.id_tag','tags_puestos.id_tag')               
            ->where('tags_puestos.id_puesto',$id)
            ->pluck('nom_tag')
            ->toarray();
        $tags=implode(",",$tags);

        $tipos = DB::table('puestos_tipos')
        ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                $q->orwhere('puestos_tipos.mca_fijo','S');
            }
        })
        ->get();
        
        return view('puestos.edit',compact('puesto','tags','usuarios','perfiles','tipos'));

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

            if ($r->hasFile('img_puesto')) {
                $file = $r->file('img_puesto');
                $path = '/img/puestos/';
                $img_puesto = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                $puesto->img_puesto=$img_puesto;
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_puesto);
            }
            $puesto->save();

            //Procesamos los tags
            //Borramos los tags que tenga el puesto
            tags_puestos::where('id_puesto',$puesto->id_puesto)->delete();
            //Y ahora insertamos los que vengan
            $arr_tags=explode(",",$r->tags);
            foreach($arr_tags as $tag){
                //Primero a ver si existe el tag para el cleinte
                $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$r->id_cliente)->first();

                if(!isset($esta_tag)){
                    DB::table('tags')->insert([
                        'nom_tag'=>$tag,
                        'id_cliente'=>$r->id_cliente
                    ]);
                    $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$r->id_cliente)->first();
                }
                DB::table('tags_puestos')->insert([
                    'id_tag'=>$esta_tag->id_tag,
                    'id_puesto'=>$puesto->id_puesto
                ]);
            }

            //Asignacion directa de puesto
            DB::table('puestos_asignados')->where('id_puesto',$puesto->id_puesto)->delete();
            if($r->id_usuario && $r->id_usuario>0){
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_usuario'=>$r->id_usuario
                ]);
                //Notificar al usuario entrante
                $usuario=users::find($r->id_usuario);
                $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') para usted';
                notificar_usuario($usuario,"Se le ha asignado un nuevo puesto de forma indefinida",'emails.asignacion_puesto',$str_notificacion,1);
               
            } else {

            }
            if($r->id_perfil && $r->id_perfil>0){
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_perfil'=>$r->id_perfil
                ]);
            } else {
                
            }

           
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

        if (!isset($r->lista_id)){
            return Redirect::back();
        }
        try{
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
        } catch(\Exception $e){
            return Redirect::back();
        }
       
    }

     // GESTION DE TIPOS DE PUESTO
     public function index_tipos(){
        $tipos = DB::table('puestos_tipos')
        ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                $q->orwhere('puestos_tipos.mca_fijo','S');
            }
        })
        ->get();
        
        return view('puestos.tipos.index', compact('tipos'));
    }

    public function tipos_edit($id=0){
        if($id==0){
            $tipo=new puestos_tipos();
        } else {
            $tipo = puestos_tipos::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
       
        return view('puestos.tipos.edit', compact('tipo','Clientes','id'));
    }

    public function tipos_save(Request $r){
        try {
            if($r->id==0){
                puestos_tipos::create($r->all());
            } else {
                $tipo=puestos_tipos::find($r->id);
                $tipo->update($r->all());
            }
            savebitacora('Tipo de puesto creado '.$r->des_tipo_puesto,"Puestos","tipos_save","OK");
            return [
                'title' => "Tipos de puesto",
                'message' => 'Tipo de puesto '.$r->des_tipo_puesto. ' actualizado con exito',
                'url' => url('/puestos/tipos')
            ];
        } catch (Exception $exception) {
            savebitacora('ERROR: Ocurrio un error creando tipo de puesto '.$r->des_tipo_puesto.' '.$exception->getMessage() ,"Puestos","tipos_save","ERROR");
            return [
                'title' => "Tipos de puesto",
                'error' => 'ERROR: Ocurrio un error actualizando el tipo de puesto '.$r->des_tipo_puesto.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function tipos_delete($id=0){
        try {
            $tipo = puestos_tipos::findorfail($id);

            $tipo->delete();
            savebitacora('Tipo de puesto borrado '.$tipo->des_tipo_puesto,"Puestos","tipos_delete","OK");
            flash('Tipo de puesto '.$tipo->des_tipo_puesto.' borrado')->success();
            return back()->withInput();
        } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error borrando Tipo de puesto '.$tipo->des_tipo_puesto.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
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
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
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
            ->where(function($q) {
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
        
        return view('puestos.mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil'));
    }

    public function plano(){

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
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
            ->orderby('plantas.num_orden')
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
        
        return view('puestos.plano',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil'));
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

    public function generar_token(){

        $puestos=DB::table('puestos')
            ->wherenull('token')
            ->get();

        foreach($puestos as $p){
            DB::table('puestos')->where('id_puesto',$p->id_puesto)->update([
                'token'=>Str::random(50)
            ]);
        }
    }

    public function modificar_puestos(Request $r){
        //dd($r->all());
        try{
            $listaid=explode(",",$r->lista_id);
            $cosas="";
            $valores=[];
            
            if($r->des_puesto){
                $valores["des_puesto"]=$r->des_puesto;
                $cosas.=" | Nombre: ".$r->des_puesto;
            }
            if($r->id_estado){
                $valores["id_estado"]=$r->id_estado;
                $cosas.=" | Estado: ".$r->id_estado;
            }
            if($r->val_color){
                $valores["val_color"]=$r->val_color;
                $cosas.=" | Color: ".$r->val_color;
            }
            if($r->val_icono && $r->val_icono!="empty"){
                $valores["val_icono"]=$r->val_icono;
                $cosas.=" | Icono: ".$r->val_icono;
            }
            if($r->id_edificio){
                $valores["id_edificio"]=$r->id_edificio;
                $cosas.=" | Edificio: ".$r->id_edificio;
            }
            if($r->id_planta){
                $valores["id_planta"]=$r->id_planta;
                $cosas.=" | Planta: ".$r->id_planta;
            }
            if($r->id_tipo_puesto){
                $valores["id_tipo_puesto"]=$r->id_tipo_puesto;
                $cosas.=" | Tipo de puesto: ".$r->id_tipo_puesto;
            }
            if($r->max_horas_reservar){
                $valores["max_horas_reservar"]=$r->max_horas_reservar;
                $cosas.=" | Tiempo maximo de reserva: ".$r->max_horas_reservar;
            }
            if($r->id_perfil){
                //Asignacion directa de puesto
                foreach($listaid as $id){
                    DB::table('puestos_asignados')->where('id_puesto',$id)->delete();
                    DB::table('puestos_asignados')->insert([
                        'id_puesto'=>$id,
                        'id_perfil'=>$r->id_perfil
                    ]);
                }
                $cosas.=" | Perfil: ".$r->id_perfil;
            }
            if($r->tags){
                //Procesamos los tags
                $arr_tags=explode(",",$r->tags);
                foreach($listaid as $id){
                    //Borramos los tags que tenga el puesto
                    tags_puestos::where('id_puesto',$id)->delete();
                    $id_cliente=puestos::find($id)->id_cliente;
                    //Y ahora insertamos los que vengan
                    foreach($arr_tags as $tag){
                        //Primero a ver si existe el tag para el cleinte
                        $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$id_cliente)->first();

                        if(!isset($esta_tag)){
                            DB::table('tags')->insert([
                                'nom_tag'=>$tag,
                                'id_cliente'=>$id_cliente
                            ]);
                            $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$id_cliente)->first();
                        }
                        DB::table('tags_puestos')->insert([
                            'id_tag'=>$esta_tag->id_tag,
                            'id_puesto'=>$id
                        ]);
                    }
                }
                
                $cosas.=" | Tags: ".$r->tags;
            }
            if(sizeof($valores)>0)
                $puestos=puestos::wherein('id_puesto',$listaid)->update($valores);

            savebitacora('Actualizacion masiva de puestos '.implode(";",$listaid)." Atributos modificados :".$cosas,"OK");
            return [
                'title' => "Borrar puestos",
                'message' => 'Actualizacion masiva de puestos '.implode(";",$listaid)." Atributos modificados :".$cosas,
                'url' => url('puestos')
            ];
        } catch(\Excveption $e){
            return [
                'title' => "puestos",
                'error' => 'ERROR: Ocurrio un error actualizando los puestos '.$r->name.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }
        
    }

    public function borrar_puestos(Request $r){
     
        savebitacora('Borrado masivo de puestos '.implode(";",$r->lista_id),"OK");
        puestos::wherein('id_puesto',$r->lista_id)->delete();
        return [
            'title' => "Borrar puestos",
            'message' => count($r->lista_id).' puestos borrados: '.implode(', ',$r->lista_id),
            'url' => url('puestos')
        ];
    }
}
