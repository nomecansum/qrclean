<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use App\Models\rondas;
use App\Models\incidencias_tipos;
use App\Models\incidencias;
use App\Models\causas_cierre;
use App\Models\incidencias_acciones;
use App\Models\estados_incidencias;

use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use PDF;
use File;
use Str;
use Mail;
use Illuminate\Validation\Rule;

class IncidenciasController extends Controller
{
    //LISTADO DE INCIDENCIAS
    public function index($f1=0,$f2=0){
        $f1=$f1==0?Carbon::now()->startOfMonth()->subMonth():Carbon::parse($f1);
        $f2=$f2==0?Carbon::now()->endOfMonth():Carbon::parse($f2);
        $fhasta=clone($f2);
        $fhasta=$fhasta->addDay();
        $incidencias=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->whereBetween('fec_apertura',[$f1,$fhasta])
            ->orderby('fec_apertura','desc')
            ->get();
        
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
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();
        return view('incidencias.index',compact('incidencias','f1','f2','puestos'));
    }


    //FORMULARIO DE CIERRE DE INCIDENCIA
    public function form_cierre($id){
        validar_acceso_tabla($id,'incidencias');
        $causas_cierre=DB::table('causas_cierre')
            ->where('id_cliente',Auth::user()->id_cliente)
            ->get();
        return view('incidencias.fill-form-cerrar',compact('id','causas_cierre'));
    }


    //FORMULARIO DE AÑADIR NUEVA ACCIOM
    public function form_accion($id){
        validar_acceso_tabla($id,'incidencias');
        $estados = DB::table('estados_incidencias')
            ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('estados_incidencias.id_cliente',Auth::user()->id_cliente);
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            })
        ->get();

        return view('incidencias.fill-form-accion',compact('id','estados'));
    }

    public function detalle_incidencia($id){
        validar_acceso_tabla($id,"incidencias");
        $incidencia=DB::table('incidencias')
            ->select('incidencias.*','edificios.des_edificio','plantas.des_planta','users.name','users.img_usuario','puestos.cod_puesto','puestos.des_puesto','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('users','incidencias.id_usuario_apertura','users.id')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where('incidencias.id_incidencia',$id)
            ->first();
        $acciones=DB::table('incidencias_acciones')
            ->join('users','incidencias_acciones.id_usuario','users.id')
            ->where('id_incidencia',$id)
            ->get();
        return view('incidencias.fill-detalle-incidencia',compact('incidencia','acciones'));
    }


    public function get_detalle_scan($id){
        if(strlen($id)>10){  //Es un token
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->where('token',$id)
                ->first();
        } else { //Es un id
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->where('id_puesto',$id)
                ->first();
        }
        $incidencia=DB::table('incidencias')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where('incidencias.id_puesto',$puesto->id_puesto)
            ->wherenull('incidencias.fec_cierre')
            ->first();
        return view('incidencias.get_detalle_scan',compact('incidencia','puesto'));
    }

    public function delete($id){
        try {
            validar_acceso_tabla($id,"incidencias");
            $incidencia = incidencias::findOrFail($id);
            $incidencia->delete();
            savebitacora('Incidencia ['.$incidencia->id_incidencia.'] '.$incidencia->des_incidencia.' borrada',"Incidencias","delete","OK");
            return redirect()->route('incidencias.index')->with('success_message', 'Incidencia ['.$id.'] '.$incidencia->des_incidencia.' borrada.');
        } catch (Exception $exception) {
            savebitacora('ERROR: Ocurrio un error borrando la incidencia ['.$incidencia->id_incidencia.'] '.$exception->getMessage() ,"Incidencias","delete","ERROR");
            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Ocurrio un error al borrar la incidencia ['.$id.'] '.mensaje_excepcion($exception)]);
        }
    }

    public function cerrar(Request $r){
        try {
            validar_acceso_tabla($r->id_incidencia,'incidencias');
            $inc=incidencias::find($r->id_incidencia);
            $inc->id_causa_cierre=$r->id_causa_cierre;
            $inc->comentario_cierre=$r->comentario_cierre;
            $inc->fec_cierre=Carbon::now();
            $inc->id_usuario_cierre=Auth::user()->id;
            $inc->save();
            savebitacora('Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' cerrada',"Incidencias","cerrar","OK");
            //Ponemos el estado del puesto a operativo
            $puesto=puestos::find($inc->id_puesto);
            $puesto->id_estado=1;
            $puesto->save();
            return [
                'title' => "Cerrar incidencia",
                'message' => 'Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' cerrada',
                'id'=> $inc->id_incidencia
                //'url' => url('/incidencias')
            ];
        } catch (Exception $exception) {
            savebitacora('ERROR: Ocurrio un error cerrando la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage() ,"Incidencias","cerrar","ERROR");
            return [
                'title' => "Cerrar incidencia",
                'error' => 'ERROR: Ocurrio un error cerrando la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function reabrir(Request $r){
        try {
            validar_acceso_tabla($r->id_incidencia,'incidencias');
            $inc=incidencias::find($r->id_incidencia);
            $inc->id_causa_cierre=null;
            $inc->comentario_cierre=null;
            $inc->fec_cierre=null;
            $inc->id_usuario_cierre=null;
            $inc->save();
            savebitacora('Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' reabierta',"Incidencias","reabrir","OK");
            //Ponemos el estado del puesto a operativo
            $puesto=puestos::find($inc->id_puesto);
            $puesto->id_estado=1;
            $puesto->save();
            return [
                'title' => "Reabrir incidencia",
                'message' => 'Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' reabierta',
                'id'=> $inc->id_incidencia
                //'url' => url('/incidencias')
            ];
        } catch (Exception $exception) {
            savebitacora('ERROR: Ocurrio un error reabriendo la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage() ,"Incidencias","reabrir","ERROR");
            return [
                'title' => "Reabrir incidencia",
                'error' => 'ERROR: Ocurrio un error reabriendo la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage(),
                'url' => url('incidencias')
            ];

        }
    }


    // GESTION DE TIPOS DE INCIDENCIA
    public function index_tipos(){
        $tipos = DB::table('incidencias_tipos')
        ->join('clientes','clientes.id_cliente','incidencias_tipos.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('incidencias_tipos.id_cliente',Auth::user()->id_cliente);
                $q->orwhere('incidencias_tipos.mca_fijo','S');
            }
        })
        ->get();
        
        return view('incidencias.tipos.index', compact('tipos'));
    }

    public function tipos_edit($id=0){
        if($id==0){
            $tipo=new incidencias_tipos();
        } else {
            $tipo = incidencias_tipos::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        $estados = DB::table('estados_incidencias')
            ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('estados_incidencias.id_cliente',Auth::user()->id_cliente);
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            })
        ->get();
        return view('incidencias.tipos.edit', compact('tipo','Clientes','id','estados'));
    }

    public function tipos_save(Request $r){
        try {
            if($r->id==0){
                incidencias_tipos::create($r->all());
            } else {
                $tipo=incidencias_tipos::find($r->id);
                $tipo->update($r->all());
            }
            savebitacora('Tipo de incidencia creado '.$r->des_tipo_incidencia,"Incidencias","tipos_save","OK");
            return [
                'title' => "Tipos de incidencia",
                'message' => 'Tipo de incidencia '.$r->des_tipo_incidencia. ' actualizado con exito',
                'url' => url('/incidencias/tipos')
            ];
        } catch (Exception $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error creando tipo de incidencia '.$r->des_tipo_incidencia.' '.$exception->getMessage() ,"Incidencias","tipos_save","ERROR");
            return [
                'title' => "Tipos de incidencia",
                'error' => 'ERROR: Ocurrio un error actualizando el tipo de incidencia '.$r->des_tipo_incidencia.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function tipos_delete($id=0){
        try {
            $tipo = incidencias_tipos::findorfail($id);

            $tipo->delete();
            savebitacora('Tipo de incidencia borrado '.$tipo->des_tipo_incidencia,"Incidencias","causas_save","OK");
            flash('Tipo de incidencia '.$tipo->des_tipo_incidencia.' borrado')->success();
            return back()->withInput();
        } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error borrando Tipo de incidencia '.$tipo->des_tipo_incidencia.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }


    // GESTION DE CAUSAS DE CIERRE DE INCIDENCIA
    public function index_causas(){
        $causas = DB::table('causas_cierre')
        ->join('clientes','clientes.id_cliente','causas_cierre.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('causas_cierre.id_cliente',Auth::user()->id_cliente);
                $q->orwhere('causas_cierre.mca_fija','S');
            }
        })
        ->get();
        return view('incidencias.causas.index', compact('causas'));
    }

    public function causas_edit($id=0){
        if($id==0){
            $causa=new causas_cierre();
        } else {
            $causa = causas_cierre::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        return view('incidencias.causas.edit', compact('causa','Clientes','id'));
    }

    public function causas_save(Request $r){
        try {
            if($r->id==0){
                causas_cierre::create($r->all());
            } else {
                $causa=causas_cierre::find($r->id);
                $causa->update($r->all());
            }
            savebitacora('Causa de cierre actualizada '.$r->des_causa,"Incidencias","causas_save","OK");
            return [
                'title' => "Causas de cierre",
                'message' => 'Causa de cierre '.$r->des_causa. ' actualizada',
                'url' => url('/incidencias/causas')
            ];
        } catch (Exception $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error actualizando causa de cierre '.$r->des_causa.' '.$exception->getMessage() ,"Incidencias","causas_save","ERROR");
            return [
                'title' => "Causas de cierre",
                'error' => 'ERROR: Ocurrio un error actualizando causa de cierre '.$r->des_causa.' '.$exception->getMessage(),
                //'url' => url('causas')
            ];

        }
    }

    public function causas_delete($id=0){
        try {
            $causa = causas_cierre::findorfail($id);

            $causa->delete();
            savebitacora('Causa de cierre borrada '.$causa->des_causa,"Incidencias","causas_save","OK");
            flash('Causa de cierre '.$causa->des_causa.' borrada')->success();
            return back()->withInput();
        } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error borrando causa de cierre '.$causa->des_causa.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }

    // GESTION DE ESTADOS DE INCIDENCIA
    public function index_estados(){
        $estados = DB::table('estados_incidencias')
        ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('estados_incidencias.id_cliente',Auth::user()->id_cliente);
                $q->orwhere('estados_incidencias.mca_fijo','S');
            }
        })
        ->get();
        return view('incidencias.estados.index', compact('estados'));
    }

    public function estados_edit($id=0){
        if($id==0){
            $estado=new estados_incidencias();
        } else {
            $estado = estados_incidencias::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        return view('incidencias.estados.edit', compact('estado','Clientes','id'));
    }

    public function estados_save(Request $r){
        try {
            if($r->id==0){
                estados_incidencias::create($r->all());
            } else {
                $estado=estados_incidencias::find($r->id);
                $estado->update($r->all());
            }
            savebitacora('Estado de incidencia actualizada '.$r->des_estado,"Incidencias","estados_save","OK");
            return [
                'title' => "Estados de incidencia",
                'message' => 'Estado de incidencia '.$r->des_estado. ' actualizado',
                'url' => url('/incidencias/estados')
            ];
        } catch (Exception $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error actualizando estado de incidencia '.$r->des_estado.' '.$exception->getMessage() ,"Incidencias","estados_save","ERROR");
            return [
                'title' => "Estados de incidencia",
                'error' => 'ERROR: Ocurrio un error actualizando estado de incidencia '.$r->des_estado.' '.$exception->getMessage(),
                //'url' => url('causas')
            ];

        }
    }

    public function estados_delete($id=0){
        try {
            $estado = estados_incidencias::findorfail($id);

            $estado->delete();
            savebitacora('Estado de incidencia borrado '.$estado->des_estado,"Incidencias","causas_save","OK");
            flash('Estado de incidencia '.$estado->des_estado.' borrada')->success();
            return back()->withInput();
        } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error borrando causa de cierre '.$estado->des_estado.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }

    //USUARIOS ABRIR INCIDENCIAS
    public function nueva_incidencia($puesto){
        $referer = request()->headers->get('referer');
        if(strpos($referer,'/puesto/')){
            $referer='scan';
        } else {
            $referer='incidencias';
        }

        if(strlen($puesto)>10){  //Es un token
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->where('token',$puesto)
                ->first();
        } else { //Es un id
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->where('id_puesto',$puesto)
                ->first();
        }
        if(!isset($puesto)){
            return view('scan.puesto_no_encontrado',compact('puesto'));    
        }
        validar_acceso_tabla($puesto->id_puesto,'puestos');
        $tipos=DB::table('incidencias_tipos')
            ->join('clientes','incidencias_tipos.id_cliente','clientes.id_cliente')
            ->where(function($q) use($puesto){
                if (!isAdmin()) {
                    $q->where('incidencias_tipos.id_cliente',$puesto->id_cliente);
                    $q->orwhere('incidencias_tipos.mca_fijo','S');
                }
                })
            ->orderby('mca_fijo')
            ->orderby('nom_cliente')
            ->get();
        return view('incidencias.nueva_incidencia',compact('puesto','tipos','referer'));
    }

    
    /**
     * Get the request's data from the request.
     *
     * @param Illuminate\Http\Request\Request $request
     * @return array
     */
    protected function getDataincidencia(Request $request)
    {
        $rules = [
            'des_incidencia' => 'required|string|min:1|max:500',
            'txt_incidencia' => 'nullable|string|min:1|max:65000',
            'img_attach1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg|max:14096',
            'img_attach2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg|max:14096',
            'id_puesto'=> 'required',
            'img1'=>'nullable',
            'img2'=>'nullable',
        ];


        $data = $request->validate($rules);
        return $data;
    }
    
    public function save(Request $r){
        $data=$this->getDataincidencia($r);
        $puesto=puestos::find($r->id_puesto);
        $tipo=incidencias_tipos::find($r->id_tipo_incidencia);
        try{    
            for ($i=0; $i <3 ; $i++) { 
                $var="img".$i;
                $$var='';
                if ($r->hasFile('img_attach'.$i)) {
                    
                    $file = $r->file('img_attach'.$i);
                    $path = config('app.ruta_public').'/uploads/incidencias/'.$puesto->id_cliente;
                    $path_local = public_path().'/uploads/incidencias/'.$puesto->id_cliente;

                        if(!File::exists($path_local)) {
                            File::makeDirectory($path_local);
                        }

                    $$var = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                    // $img = Image::make($file);
                    // $img->resize(1000, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // })->save($path.'/'.$$var);
                    
                    Storage::disk(config('app.upload_disk'))->putFileAs($path,$file,$$var);
                    $file->move($path_local,$$var);
                }
                $data[$var]=$$var;
            }
            $inc=new incidencias;
            $inc->des_incidencia=$data['des_incidencia'];
            $inc->txt_incidencia=$data['txt_incidencia'];
            $inc->id_cliente=$puesto->id_cliente;
            $inc->id_usuario_apertura=Auth::user()->id;
            $inc->fec_apertura=Carbon::now();
            $inc->id_tipo_incidencia=$r->id_tipo_incidencia;
            $inc->id_puesto=$puesto->id_puesto;
            $inc->img_attach1=$img1;
            $inc->img_attach2=$img2;
            $inc->save();

            //Marcamos el puesto como chungo
            $puesto->id_estado=6;
            $puesto->save();
            if($r->referer=='incidencias'){
                $url_vuelta='incidencias';
            } else {
                $url_vuelta='/';
            }
            try{
                $this->post_procesado_incidencia($inc);
                savebitacora('Incidencia de tipo '.$tipo->des_tipo_incidencia. ' '.$r->des_incidencia.' creada por '.Auth::user()->name,"Incidencias","save","OK");
                return [
                    'title' => "Crear incidencia en puesto ".$puesto->cod_puesto,
                    'message' => "Incidencia de tipo ".$tipo->des_tipo_incidencia.' creada. Muchas gracias',
                    'url' => url($url_vuelta)
                ];
            } catch(\Exception $exception){
                savebitacora('ERROR: Ocurrio un error en el postprocesado de incidencia del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(). ' La incidencia se ha registrado correctamente pero no se ha podido procesar la accion de notificacion programada' ,"Incidencias","save","ERROR");
                return [
                    'title' => "Crear incidencia en puesto ".$puesto->cod_puesto,
                    'error' => 'ERROR: Ocurrio un error creando incidencia del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(),
                    'url' => url($url_vuelta)
                ];
            }
           
       
            
        } catch (Exception $exception) {

            savebitacora('ERROR: Ocurrio un error creando incidencia del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage() ,"Incidencias","save","ERROR");
            return [
                'title' => "Crear incidencia en puesto ".$puesto->cod_puesto,
                'error' => 'ERROR: Ocurrio un error creando incidencia del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        } 
    }

    //PROCESADO DE INCIDENCIAS->ENVIARLA A TERCEROS SISTEMAS

    public function post_procesado_incidencia($inc){
        $tipo=incidencias_tipos::find($inc->id_tipo_incidencia);
        $puesto=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('id_puesto',$inc->id_puesto)
            ->first();
        switch ($tipo->tip_metodo) {
            case 'S':  //Mandar SMS
               
                break;
            case 'M':  //Mandar e-mail
                $to_email = $tipo->txt_destinos;
                Mail::send('emails.mail_incidencia', ['inc'=>$inc], function($message) use ($tipo, $to_email, $inc, $puesto) {
                    $message->to($to_email, '')->subject('Incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
                    $message->from(config('mail.from.address'),config('mail.from.name'));
                    if($inc->img_attach1)
                        $message->attach(public_path().'/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach1);
                    if($inc->img_attach2)
                        $message->attach(public_path().'/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach2);
                });
                break;
            case 'P': //HTTP Post
                
                break;
            case 'G': //HTTP Get
                
                break;
            case 'L': //Spotlinker
                
                break;
                                                    
            default:
                # code...
                break;
        }

    }


    public function add_accion(Request $r){
        $data=[];
        $incidencia=incidencias::find($r->id_incidencia);
        try{    
            for ($i=1; $i <3 ; $i++) { 
                $var="img".$i;
                $$var='';
                if ($r->hasFile('img_attach'.$i)) {
                    
                    $file = $r->file('img_attach'.$i);
                    $path = config('app.ruta_public').'/uploads/incidencias/'.$incidencia->id_cliente;
                    $path_local = public_path().'/uploads/incidencias/'.$incidencia->id_cliente;

                        if(!File::exists($path_local)) {
                            File::makeDirectory($path_local);
                        }

                    $$var = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                    // $img = Image::make($file);
                    // $img->resize(1000, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // })->save($path.'/'.$$var);
                    
                    Storage::disk(config('app.upload_disk'))->putFileAs($path,$file,$$var);
                    $file->move($path_local,$$var);
                }
                $data[$var]=$$var;
            }
            $acciones=incidencias_acciones::where('id_incidencia',$r->id_incidencia);
            if($acciones){
                $cuenta=$acciones->count()+1;
            } else {
                $cuenta=1;
            }
           
            //Vamos a insertar
            $accion=new incidencias_acciones;
            $accion->id_incidencia=$incidencia->id_incidencia;
            $accion->num_accion=$cuenta;
            $accion->des_accion=$r->des_accion;
            $accion->fec_accion=Carbon::now();
            $accion->id_usuario=Auth::user()->id;
            $accion->img_attach1=isset($data['img1'])?$data['img1']:null;
            $accion->img_attach2=isset($data['img2'])?$data['img2']:null;
            $accion->save();
            savebitacora("Añadida accion para la incidencia ".$r->id_incidencia,"Incidencias","add_accion","OK");
            return [
                'title' => "Añadir accion a la incidencia",
                'message' => "Añadida accion para la incidencia ".$r->id_incidencia,
                //'url' => url($url_vuelta)
            ];

        } catch (\Exception $e) {

            savebitacora('ERROR: Ocurrio un error añadiendo la accion '.$e->getMessage() ,"Incidencias","add_accion","ERROR");
            return [
                'title' => "Añadir accion",
                'error' => 'ERROR: Ocurrio un error añadiendo la accion '.$e->getMessage(),
                //'url' => url('sections')
            ];
        } 
    }
}
