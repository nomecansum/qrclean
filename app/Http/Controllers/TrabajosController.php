<?php

namespace App\Http\Controllers;

use App\Models\trabajos;
use App\Models\trabajos_tipos;
use App\Models\grupos;
use App\Models\trabajos_grupos;
use App\Models\contratas;
use App\Models\operarios;
use App\Models\niveles_acceso;
use App\Models\planes;
use App\Models\planes_detalle;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\plantas_zonas;

use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use File;
use Str;
use Illuminate\Support\Facades\Storage;

class TrabajosController extends Controller
{
    //

    ////////////////TIPOS DE TRABAJOS ///////////////////////
    public function tipos_index() {
        $trabajos = DB::table('trabajos')
            ->join('trabajos_tipos', 'trabajos.id_tipo_trabajo', 'trabajos_tipos.id_tipo_trabajo')
            ->join('clientes', 'trabajos.id_cliente', 'clientes.id_cliente')
            ->where(function ($q){
                $q->where('trabajos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.tipos.index', compact('trabajos'));
    }

    public function edit_tipo($id=0 ) {
        if ($id==0){
            $dato = new trabajos();
        } else {
            $dato = trabajos::find($id);
        }
        $tipos = trabajos_tipos::all();
        return view('trabajos.tipos.edit', compact('dato','tipos','id'));
    }

    public function update_tipo(Request $r ) {
        try {
            //Las fechas
            if(isset($r->fechas)) {
                $f = explode(' - ',$r->fechas);
                $r->request->add(['fec_inicio'=>adaptar_fecha($f[0])]);
                $r->request->add(['fec_fin'=>adaptar_fecha($f[1])]);
            } else {
                $r->request->add(['fec_inicio'=>null]);
                $r->request->add(['fec_fin'=>null]);
            }
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }

            if ($r->id==0){
                $dato = trabajos::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"trabajos");
                $dato = trabajos::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            savebitacora('Tarea de trabajo creada '.$r->des_trabajo,"Trabajos","update_tipo","OK");
            return [
                'title' => "Tareas de trabajos",
                'message' => 'Tarea '.$r->des_trabajo. ' actualizada con exito',
                'url' => url('/trabajos/tipos')
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando la tarea  '.$dato->des_trabajo.' '.$e->getMessage() ,"Trabajos","update_tipo","ERROR");
            return [
                'title' => "Tareas de trabajos",
                'error' => 'ERROR: Ocurrio un error actualizando la tarea '.$r->des_trabajo.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_tipo($id)
    {
        try {
            validar_acceso_tabla($id,"trabajos");
            $dato = trabajos::findOrFail($id);
            $dato->delete();
            savebitacora('Tarea '.$dato->des_trabajo. ' borrado',"Trabajos","delete","OK");
            flash("Tarea ".$dato->des_trabajo." borrado")->success();
		    return redirect('trabajos');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando la tareaa. '.mensaje_excepcion($exception))->error();
            return redirect('trabajos');
        }
    }


    ///////////////////GRUPOS DE TRABAJOS ///////////////////////
    public function grupos_index() {
        $datos = DB::table('grupos_trabajos')
            ->join('clientes', 'grupos_trabajos.id_cliente', 'clientes.id_cliente')
            ->where(function ($q){
                $q->where('grupos_trabajos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.grupos.index', compact('datos'));
    }

    public function edit_grupo($id=0 ) {
        if ($id==0){
            $dato = new grupos();
        } else {
            $dato = grupos::find($id);
        }
        $ya_estan=trabajos_grupos::where('id_grupo',$id)->pluck('id_trabajo')->toArray();
        $tareas = trabajos::where('id_cliente',Auth::user()->id_cliente)->wherenotin('id_trabajo',$ya_estan)->get();
        return view('trabajos.grupos.edit', compact('dato','tareas','id'));
    }

    public function update_grupo(Request $r ) {
        try {
            //Las fechas
            if(isset($r->fechas)) {
                $f = explode(' - ',$r->fechas);
                $r->request->add(['fec_inicio'=>adaptar_fecha($f[0])]);
                $r->request->add(['fec_fin'=>adaptar_fecha($f[1])]);
            } else {
                $r->request->add(['fec_inicio'=>null]);
                $r->request->add(['fec_fin'=>null]);
            }

            
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }

            if ($r->id==0){
                $dato = grupos::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"grupos_trabajos");
                
                $dato = grupos::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            //Ahora los elementos del grupo
            $items = json_decode($r->nestedset);
            $dato->num_trabajos=count($items);
            $dato->save();
            DB::table('trabajos_grupos')->where('id_grupo',$dato->id_grupo)->delete();
            foreach($items as $i=>$item){
                $el=new trabajos_grupos;
                $el->id_grupo=$dato->id_grupo;
                $el->id_trabajo=$item->id;
                $el->num_nivel=$item->depth;
                $el->id_padre=$item->parent_id==""?0:$item->parent_id;
                $el->num_orden=$i;
                $el->save();
            }

            savebitacora('Grupo de trabajos creado '.$r->des_grupo,"Trabajos","update_grupo","OK");
            return [
                'title' => "Grupo de trabajos",
                'message' => 'Grupo '.$r->des_grupo. ' actualizada con exito',
                'url' => url('/trabajos/grupos')
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando el grupo  '.$dato->des_grupo.' '.$e->getMessage() ,"Trabajos","update_grupo","ERROR");
            return [
                'title' => "Grupo de trabajos",
                'error' => 'ERROR: Ocurrio un error actualizando el grupo '.$r->des_grupo.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_grupo($id)
    {
        try {
            validar_acceso_tabla($id,"trabajos");
            $dato = trabajos::findOrFail($id);
            $dato->delete();
            DB::table('trabajos_grupos')->where('id_grupo',$dato->id_grupo)->delete();
            savebitacora('Tarea '.$dato->des_trabajo. ' borrado',"Trabajos","delete","OK");
            flash("Tarea ".$dato->des_trabajo." borrado")->success();
		    return redirect('trabajos_grupos');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando la tareaa. '.mensaje_excepcion($exception))->error();
            return redirect('trabajos_grupos');
        }
    }


    ////////////////CONTRATAS DE TRABAJOS ///////////////////////
    public function contratas_index() {
        $datos = DB::table('contratas')
            ->select('contratas.*','clientes.nom_cliente')
            ->selectraw("(select count(*) from contratas_operarios where id_contrata=contratas.id_contrata) as num_operarios")
            ->join('clientes', 'contratas.id_cliente', 'clientes.id_cliente')
            ->where(function ($q){
                $q->where('contratas.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.contratas.index', compact('datos'));
    }

    public function edit_contrata($id=0 ) {
        if ($id==0){
            $dato = new contratas();
        } else {
            $dato = contratas::find($id);
        }
        $operarios = operarios::where('id_contrata',$id)->get();
        return view('trabajos.contratas.edit', compact('dato','operarios','id'));
    }

    public function update_contrata(Request $r ) {
        try {
            //Las fechas
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }
            if ($r->hasFile('imagen')) {
                $file = $r->file('imagen');
                $path = config('app.ruta_public').'/img/contratas/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
                $r->request->add(['img_logo'=>$img_usuario]);
            }

            if ($r->id==0){
                $dato = contratas::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"contratas");
                $dato = contratas::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            savebitacora('Contrata de trabajo creada '.$r->des_contrata,"Trabajos","update_contrata","OK");
            return [
                'title' => "Contratas de trabajos",
                'message' => 'Contrata '.$r->des_contrata. ' actualizada con exito',
                'url' => url('/trabajos/contratas')
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando la contrata  '.$r->des_contrata.' '.$e->getMessage() ,"Trabajos","update_contrata","ERROR");
            return [
                'title' => "Contratas de trabajos",
                'error' => 'ERROR: Ocurrio un error actualizando la contrata '.$r->des_contrata.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_contrata($id)
    {
        try {
            validar_acceso_tabla($id,"contratas");
            $dato = contratas::findOrFail($id);
            $dato->delete();
            savebitacora('Contrata '.$dato->des_contrata. ' borrada',"Trabajos","delete_contrata","OK");
            flash("Contrata ".$dato->des_contrata." borrada")->success();
		    return redirect('contratas');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando la contrata. '.mensaje_excepcion($exception))->error();
            return redirect('contratas');
        }
    }

    public function usuarios_internos($id,$id_perfil=null) {
       
        $usuarios = DB::table('contratas_operarios')
            ->select('contratas_operarios.*','users.name','users.cod_nivel','users.img_usuario','users.id')
            ->join('contratas', 'contratas_operarios.id_contrata', 'contratas.id_contrata')
            ->join('users', 'contratas_operarios.id_usuario', 'users.id')
            ->where(function ($q){
                $q->where('contratas.id_cliente',Auth::user()->id_cliente);
            })
            ->where('contratas_operarios.id_contrata',$id)
            ->get();
        $perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)
        ->where('cod_nivel','>',1)
        ->where(function ($q){
            $q->where('id_cliente',Auth::user()->id_cliente);
            $q->orwhere('mca_fijo','S');
        })
        ->get();

        $lista_usuarios=DB::table('users')
            ->select('users.name','users.cod_nivel','users.img_usuario','users.id')
            ->where('users.id_cliente',Auth::user()->id_cliente)
            ->wherein('cod_nivel',$perfiles->pluck('cod_nivel')->toarray())
            ->where(function($q) use ($id_perfil,$perfiles){
                if($id_perfil!=null){
                    $q->where('users.cod_nivel', $id_perfil);
                } else {
                    $q->where('users.cod_nivel', $perfiles->first()->cod_nivel);
                }
            })
            ->orderby('name')
            ->get();
        
        return view('trabajos.contratas.fill_usuarios_internos', compact('usuarios','perfiles','id_perfil','id','lista_usuarios'));
    }

    public function usuarios_genericos($id) {
        $usuarios = DB::table('contratas_operarios')
            ->select('contratas_operarios.*')
            ->join('contratas', 'contratas_operarios.id_contrata', 'contratas.id_contrata')
            ->where(function ($q){
                $q->where('contratas.id_cliente',Auth::user()->id_cliente);
            })
            ->where('contratas_operarios.id_contrata',$id)
            ->wherenull('id_usuario')
            ->orderby('nom_operario')
            ->get();

        return view('trabajos.contratas.fill_usuarios_genericos', compact('usuarios','id'));
    }

    public function crear_usuarios_genericos(Request $r){
        if($r->val_color=="#000000"){
            $r->request->add(['val_color'=>null]);
        }
        for ($i=($r->num_inicio-1);$i<($r->num_inicio+$r->num_usuarios-1);$i++){
            $usuario = new operarios();
            $usuario->id_contrata = $r->id_contrata;
            $usuario->val_color = $r->val_color;
            $usuario->id_cliente = Auth::user()->id_cliente;
            $usuario->nom_operario = $r->des_prefijo.($i+1);
            $usuario->save();
        }
        savebitacora( 'Creados '.$r->num_usuarios. ' operarios de '.$r->des_prefijo,"Trabajos","crear_usuarios_genericos","OK");
        return [
            'title' => "Contratas de trabajos",
            'message' => 'Creados '.$r->num_usuarios. ' operarios de '.$r->des_prefijo,
            'url' => url('/trabajos/contratas')
        ];
       
    }

    public function set_usuarios_contrata($accion,$id_contrata,$id_operario){
        $contrata=contratas::findOrFail($id_contrata);
        switch ($accion) {
            case 'D':
                $usuario = operarios::where('id_contrata',$id_contrata)->where('id_operario',$id_operario)->first();
                $usuario->delete();
                return [
                    'title' => "Contratas de trabajos",
                    'message' => 'Operario '.$id_operario.' eliminado',
                    'id' => $id_operario,
                ];
                break;
            case 'AI':
                $usuario = new operarios();
                $usuario->id_contrata = $id_contrata;
                $usuario->id_usuario = $id_operario;
                $usuario->id_cliente = $contrata->id_cliente;
                $usuario->save();
                return [
                    'title' => "Contratas de trabajos",
                    'message' => 'Operario '.$id_operario.' añadido',
                    'id' => $id_operario,
                ];
                break;
            case 'DI':
                $usuario = operarios::where('id_contrata',$id_contrata)->where('id_usuario',$id_operario)->first();
                $usuario->delete();
                return [
                    'title' => "Contratas de trabajos",
                    'message' => 'Operario '.$id_operario.' eliminado',
                    'id' => $id_operario,
                ];
                break;
            default:
                # code...
                break;
        }
    }


    ///////////////////GRUPOS DE TRABAJOS ///////////////////////
    public function planes_index() {
        $datos = DB::table('trabajos_planes')
            ->join('clientes', 'trabajos_planes.id_cliente', 'clientes.id_cliente')
            ->join('edificios', 'trabajos_planes.id_edificio', 'edificios.id_edificio')
            ->where(function ($q){
                $q->where('trabajos_planes.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.planes.index', compact('datos'));
    }

    public function edit_plan($id=0 ) {
        if ($id==0){
            $dato = new planes();
        } else {
            $dato = planes::find($id);
        }

        $edificios = edificios::where('id_cliente',Auth::user()->id_cliente)->get();
        $grupos = grupos::where('id_cliente',Auth::user()->id_cliente)->get();
        $contratas = contratas::where('id_cliente',Auth::user()->id_cliente)->get();

        return view('trabajos.planes.edit', compact('dato','id','edificios','grupos','contratas'));
    }

    public function update_plan(Request $r ) {
        try {
            
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }

            if(isset($r->mca_activo)){
                $r->request->add(['mca_activo'=>'S']);
            } else {
                $r->request->add(['mca_activo'=>'N']);
            }

            if ($r->id==0){
                $dato = planes::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"trabajos_planes");
                
                $dato = planes::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            

            savebitacora('Plan de trabajo creado '.$r->des_plan,"Trabajos","update_plan","OK");
            return [
                'title' => "Plan de trabajo",
                'message' => 'Plan '.$r->des_plan. ' actualizada con exito',
                'url' => url('/trabajos/planes')
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando el plan  '.$dato->des_plan.' '.$e->getMessage() ,"Trabajos","update_plan","ERROR");
            return [
                'title' => "Plan de trabajo",
                'error' => 'ERROR: Ocurrio un error actualizando el plan '.$r->des_plan.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_plan($id)
    {
        try {
            validar_acceso_tabla($id,"trabajos_planes");
            $dato = planes::findOrFail($id);
            $dato->delete();
            DB::table('trabajos_planes')->where('id_plan',$dato->id_plan)->delete();
            savebitacora('Plan '.$dato->des_plan. ' borrado',"Trabajos","delete_plan","OK");
            flash("Plan ".$dato->des_plan." borrado")->success();
            return redirect('trabajos_planes');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando el plan. '.mensaje_excepcion($exception))->error();
            return redirect('trabajos_planes');
        }
    }

    public function get_plan(Request $r) {
        $dato = planes::find($r->id_plan);
        $tareas = trabajos::where('id_cliente',Auth::user()->id_cliente)->get();
        $grupos = grupos::where('id_cliente',Auth::user()->id_cliente)
            ->when($r->grupos, function($q) use($r){
                $q->wherein('id_grupo',$r->grupos);
            })
            ->get();

        $trabajos= DB::table('trabajos')
            ->join('trabajos_tipos', 'trabajos_tipos.id_tipo_trabajo', 'trabajos.id_tipo_trabajo')
            ->join('trabajos_grupos', 'trabajos_grupos.id_trabajo', 'trabajos.id_trabajo')
            ->where('trabajos.id_cliente',Auth::user()->id_cliente)
            ->orderby('num_orden')
            ->get();
        $contratas = contratas::where('id_cliente',Auth::user()->id_cliente)
            ->when($r->contratas, function($q) use($r){
                $q->wherein('id_contrata',$r->contratas);
            })
            ->get();
        $operarios = operarios::where('id_cliente',Auth::user()->id_cliente)
            ->when($r->contratas, function($q) use($r){
                $q->wherein('id_contrata',$r->contratas);
            })
            ->get();
        $plantas = plantas::where('id_cliente',Auth::user()->id_cliente)
            ->where(function($q) use($r){
                if(isset($r->plantas)){
                    $q->wherein('plantas.id_planta',$r->plantas);
                } else {
                    $q->wherein('plantas.id_planta',[]);
                }
            })
            ->get();
    
        $zonas = DB::table('plantas_zonas')
            ->select('plantas_zonas.*','plantas.des_planta')
            ->join('plantas', 'plantas_zonas.id_planta', 'plantas.id_planta')
            ->where(function ($q){
                $q->where('plantas.id_cliente',Auth::user()->id_cliente);
            })
            ->where(function($q) use($r){
                if(isset($r->zonas)){
                    $q->wherein('plantas_zonas.id_planta_zona',$r->zonas);
                } else {
                    $q->wherein('plantas_zonas.id_planta_zona',[]);
                }
            })
            ->get();

        $detalle= planes_detalle::where('id_plan',$r->id_plan)->get();

        $detalle=DB::table('trabajos_planes_detalle')
            ->join('trabajos_grupos', 'trabajos_planes_detalle.id_grupo_trabajo', 'trabajos_grupos.id_grupo')
            ->leftjoin('plantas_zonas', 'trabajos_planes_detalle.id_zona', 'plantas_zonas.key_id')
            ->leftjoin('plantas', 'plantas_zonas.id_planta', 'plantas.id_planta')
            ->leftjoin('contratas', 'trabajos_planes_detalle.id_contrata', 'contratas.id_contrata')
            ->where('id_plan',$r->id_plan)
            ->get();

        return view('trabajos.planes.detalle', compact('dato','tareas','grupos','contratas','operarios','plantas','zonas','trabajos'));
    }
}
