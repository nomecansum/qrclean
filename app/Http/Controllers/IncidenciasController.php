<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use App\Models\rondas;
use App\Models\incidencias_tipos;
use App\Models\incidencias;
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
    public function index(){
        $incidencias=DB::table('incidencias')
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
            ->get();
        return view('incidencias.index',compact('incidencias'));
    }

    public function form_cierre($id){
        validar_acceso_tabla($id,'incidencias');
        $causas_cierre=DB::table('causas_cierre')
            ->where('id_cliente',Auth::user()->id_cliente)
            ->get();
        return view('incidencias.fill-form-cerrar',compact('id','causas_cierre'));
    }

    public function detalle_incidencia($id){
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
            ->where('incidencias.id_incidencia',$id)
            ->first();
        return view('incidencias.fill-detalle-incidencia',compact('incidencia'));
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
        return view('incidencias.tipos.edit', compact('tipo','Clientes','id'));
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
                'title' => "Tipos de incidenica",
                'message' => 'Tipo de incidencia '.$r->des_tipo_incidencia. ' actualizado con exito',
                'url' => url('/incidencias/tipos')
            ];
        } catch (Exception $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error creando tipo de incidencia '.$r->des_tipo_incidencia.' '.$exception->getMessage() ,"Incidencias","tipos_save","ERROR");
            return [
                'title' => "Tipos de incidenica",
                'error' => 'ERROR: Ocurrio un error actualizando el tipo de incidencia '.$r->des_tipo_incidencia.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    //USUARIOS ABRIR INCIDENCIAS
    public function nueva_incidencia($puesto){
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
        return view('incidencias.nueva_incidencia',compact('puesto','tipos'));
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
            'img_attach1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg|max:4096',
            'img_attach2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg|max:4096',
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
                    $path = public_path().'/uploads/incidencias/'.$puesto->id_cliente;
                    if(!File::exists($path)) {
                        File::makeDirectory($path);
                    }
                    $$var = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                    $file->move($path,$$var);
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

            $this->post_procesado_incidencia($inc);
            
            savebitacora('Incidencia de tipo '.$tipo->des_tipo_incidencia. ' '.$r->des_incidencia.' creada por '.Auth::user()->name,"Incidencias","save","OK");
            return [
                'title' => "Crear incidencia en puesto ".$puesto->cod_puesto,
                'message' => "Incidencia de tipo ".$tipo->des_tipo_incidencia.' creada. Muchas gracias',
                'url' => url('/')
            ];
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
}
