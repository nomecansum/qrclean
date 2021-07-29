<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use App\Models\rondas;
use App\Models\users;
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
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa')
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->whereBetween('fec_apertura',[$f1,$fhasta])
            ->wherenull('incidencias.fec_cierre')
            ->orderby('fec_apertura','desc')
            ->get();
        
        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();
        return view('incidencias.index',compact('incidencias','f1','f2','puestos'));
    }

    
    //BUSCAR INCIDENCIAS
    public function search(Request $r){
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        if($r->estado){
            $estados=$r->estado;
            $estados=array_filter($r->estado, "ctype_digit");
            $atributos=array_filter($r->estado, "ctype_alpha");
        } else {
            $estados=null;
            $atributos=[];
        }

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
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
            ->where(function($q) use($r){
                if ($r->estado_inc) {
                    $q->whereIn('incidencias.id_estado',$r->estado_inc);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipo) {
                    $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
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
                    
                    if($r->andor){//Busqueda con AND
                        $puestos_tags=DB::table('tags_puestos')
                            ->select('id_puesto')
                            ->wherein('id_tag',$r->tags)
                            ->groupby('id_puesto')
                            ->havingRaw('count(id_tag)='.count($r->tags))
                            ->pluck('id_puesto')
                            ->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    } else { //Busqueda con OR
                        $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags); 
                    }
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

        $lista_puestos=$puestos->pluck('id_puesto')->toArray();

        $incidencias=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->wherein('incidencias.id_puesto',$lista_puestos)
            ->whereBetween('fec_apertura',[$f1,$f2])
            ->where(function($q) use($r){
                if($r->ac=='C'){
                    $q->wherenotnull('fec_cierre');
                }
                if($r->ac=='A'){
                    $q->wherenull('fec_cierre');
                }
            })
            ->where(function($q) use($r){
                if ($r->tipoinc) {
                    $q->whereIn('incidencias.id_tipo_incidencia',$r->tipoinc);
                }
            })
            ->orderby('fec_apertura','desc')
            ->get();
        $f1=Carbon::parse($f1);
        $f2=Carbon::parse($f2);

        return view('incidencias.fill_tabla_incidencias',compact('incidencias','f1','f2','puestos','r'));
    }

    //USUARIOS ABRIR INCIDENCIAS
    public function nueva_incidencia($puesto,$tipo='normal'){
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
        $config=DB::table('config_clientes')->where('id_cliente',$puesto->id_cliente)->first();
        $tipos=DB::table('incidencias_tipos')
            ->join('clientes','incidencias_tipos.id_cliente','clientes.id_cliente')
            ->where(function($q) use($puesto){
                $q->where('incidencias_tipos.id_cliente',$puesto->id_cliente);
                $q->orwhere('incidencias_tipos.mca_fijo','S');
                })
            ->where(function($q) use($puesto){
                $q->wherenull('list_tipo_puesto');
                $q->orwhereraw('FIND_IN_SET('.$puesto->id_tipo_puesto.', list_tipo_puesto) <> 0');
            })
            ->orderby('mca_fijo')
            ->orderby('nom_cliente')
            ->get();
        if($tipo=='embed'){
            return view('incidencias.fill_frm_incidencia',compact('puesto','tipos','referer','config'));
        } else {
            return view('incidencias.nueva_incidencia',compact('puesto','tipos','referer','config'));
        }
        
    }

    //Funcion para abrir incidencia cuando no has seleccionado puesto 
    public function selector_puestos(){
        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();
        return view('incidencias.selector_puestos',compact('puestos'));
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
            'des_incidencia' => 'nullable|string|min:1|max:500',
            'txt_incidencia' => 'nullable|string|min:1|max:65000',
            'img_attach1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg|max:14096',
            'img_attach2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg|max:14096',
            'id_puesto'=> 'required',
            'img1'=>'nullable',
            'img2'=>'nullable',
            'adjuntos'=>'nullable',
        ];
        $data = $request->validate($rules);
        return $data;
    }
    
    public function save(Request $r){
        $data=$this->getDataincidencia($r);
        $puesto=puestos::find($r->id_puesto);
        $tipo=incidencias_tipos::find($r->id_tipo_incidencia);
        try{     
            if(isset($r->adjuntos) and is_array($r->adjuntos)){
                $adjuntos=$r->adjuntos[0];
                $adjuntos=explode(",",$adjuntos);
                $indice=1;
                foreach($adjuntos as $key=>$value){
                    $var="img".$indice;
                    $$var=$value;
                    $indice++;
                }
            }

            $inc=new incidencias;
            $inc->des_incidencia=$data['des_incidencia']??null;
            $inc->txt_incidencia=$data['txt_incidencia']??null;
            $inc->id_cliente=$puesto->id_cliente;
            $inc->id_usuario_apertura=Auth::user()->id;
            $inc->fec_apertura=Carbon::now();
            $inc->id_tipo_incidencia=$r->id_tipo_incidencia;
            $inc->id_puesto=$puesto->id_puesto;
            $inc->img_attach1=$img1??null;
            $inc->img_attach2=$img2??null;
            $inc->id_estado_vuelta_puesto=$puesto->id_estado;
            $inc->save();

            //Marcamos el puesto como chungo
            $puesto->mca_incidencia='S';
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

    public function add_accion(Request $r){
        $data=[];
        $incidencia=incidencias::find($r->id_incidencia);
        $tipo=incidencias_tipos::find($incidencia->id_tipo_incidencia);
        try{    
            
            if(isset($r->adjuntos) and is_array($r->adjuntos)){
                $adjuntos=$r->adjuntos[0];
                $adjuntos=explode(",",$adjuntos);
                $indice=1;
                foreach($adjuntos as $key=>$value){
                    $var="img".$indice;
                    $$var=$value;
                    $indice++;
                }
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
            $accion->img_attach1=$img1??null;
            $accion->img_attach2=$img2??null;
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

    public function subir_adjuntos(Request $r){
		try{
			if(isset($r->id_cliente)){
				$path = config('app.ruta_public').'/uploads/incidencias/'.$r->id_cliente;
				$file = $r->file('file')[0];

                    $original = $file->getClientOriginalName();
                    $extension = File::extension($file->getClientOriginalName());
                    $newfile = $r->id_cliente.'_'.Str::random(24).'.'.$extension;
                    Storage::disk(config('app.upload_disk'))->putFileAs($path,$file,$newfile);
				return \Response::json(array('success' => true, 'filename'=>$original,'newfilename'=>$newfile));
			}
		} catch(\Exception $e){
		response()->json([
			"error" => "Error subiendo adjunto ".mensaje_excepcion($e),
			"TS" => Carbon::now()->format('Y-m-d h:i:s')
			],400)->throwResponse();
		return \Response::json(array('error' => false));
		}

	}


    //PROCESADO DE INCIDENCIAS->ENVIARLA A TERCEROS SISTEMAS

    public function post_procesado_incidencia($inc){
        $tipo=incidencias_tipos::find($inc->id_tipo_incidencia);
        $usuario_abriente=users::find($inc->id_usuario_apertura);
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
                Mail::send('emails.mail_incidencia', ['inc'=>$inc,'tipo'=>$tipo], function($message) use ($tipo, $to_email, $inc, $puesto) {
                    if(config('app.env')=='local'){//Para que en desarrollo solo me mande los mail a mi
                        $message->to(explode(';','nomecansum@gmail.com'), '')->subject('Incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
                    } else {
                        $message->to(explode(';',$to_email), '')->subject('Incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
                    }
                    $message->from(config('mail.from.address'),config('mail.from.name'));
                    if($inc->img_attach1!==null && strlen($inc->img_attach1)>5){
                        $adj1=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach1);
                        $message->attachData($adj1,$inc->img_attach1);
                    }     
                    if($inc->img_attach2!==null && strlen($inc->img_attach2)>5){
                        $adj2=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach2);
                        $message->attachData($adj2,$inc->img_attach2);
                    }
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
        //Enviamos mail al uusario abriente
        Mail::send('emails.mail_incidencia', ['inc'=>$inc,'tipo'=>$tipo], function($message) use ($tipo, $to_email, $inc, $puesto, $usuario_abriente) {
            if(config('app.env')=='local'){//Para que en desarrollo solo me mande los mail a mi
                $message->to(explode(';','nomecansum@gmail.com'), '')->subject('Confirmacion de apertura de incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
            } else {
                $message->to(explode(';',$usuario_abriente->email), '')->subject('Confirmacion de apertura de incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
            }
            $message->from(config('mail.from.address'),config('mail.from.name'));
            if($inc->img_attach1!==null && strlen($inc->img_attach1)>5){
                $adj1=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach1);
                $message->attachData($adj1,$inc->img_attach1);
            }     
            if($inc->img_attach2!==null && strlen($inc->img_attach2)>5){
                $adj2=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach2);
                $message->attachData($adj2,$inc->img_attach2);
            }
        });
        

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
        $incidencia=incidencias::find($id);
        $estados = DB::table('estados_incidencias')
            ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
            ->where(function($q) use($incidencia){
                $q->where('estados_incidencias.id_cliente',$incidencia->id_cliente);
                $q->orwhere('estados_incidencias.mca_fijo','S');
            })
            ->orderby('des_estado')
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
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
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
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
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
            $puesto=puestos::find($incidencia->id_puesto);
           
            $incidencia->delete();
            $puesto->mca_incidencia='N';
            $puesto->id_estado=$incidencia->id_estado_vuelta_puesto;
            $puesto->save();
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
            $puesto=puestos::find($inc->id_puesto);
            $puesto->mca_incidencia='N';
            $puesto->id_estado=$inc->id_estado_vuelta_puesto??1;
            $puesto->save();
            
            savebitacora('Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' cerrada',"Incidencias","cerrar","OK");
            //Enviamos mail al uusario abriente
            $des_causa=causas_cierre::find($inc->id_causa_cierre)->des_causa;
            $usuario_abriente=users::find($inc->id_usuario_apertura);
            $body="Tenemos el placer de comunicarle que la incidencia [".$inc->id_incidencia."] ".$inc->des_incidencia."Que usted abrió el  ".Carbon::parse($inc->fec_apertura)->format('d/m/Y')." ha sido cerrada por ".Auth::user()->name." Con el siguiente comentario:<br>".chr(13)." [".$des_causa."] ".$r->comentario_cierre;
            Mail::send('emails.mail_cerrar_incidencia', ['inc'=>$inc,'body'=>$body], function($message) use ($inc, $puesto,$body,$usuario_abriente) {
                if(config('app.env')=='local'){//Para que en desarrollo solo me mande los mail a mi
                    $message->to(explode(';','nomecansum@gmail.com'), '')->subject('Confirmacion de cierre de incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
                } else {
                    $message->to(explode(';',$usuario_abriente->email), '')->subject('Confirmacion de cierre de incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
                }
                $message->from(config('mail.from.address'),config('mail.from.name'));
            });

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
            $puesto=puestos::find($inc->id_puesto);
            $puesto->mca_incidencia='S';
            $puesto->save();

            $inc=incidencias::find($r->id_incidencia);
            $inc->id_causa_cierre=null;
            $inc->comentario_cierre=null;
            $inc->fec_cierre=null;
            $inc->id_usuario_cierre=null;
            $inc->id_estado_vuelta_puesto=$puesto->id_estado;
            $inc->save();
            savebitacora('Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' reabierta',"Incidencias","reabrir","OK");
            //Ponemos el estado del puesto a operativo
            
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
                $tipo->list_tipo_puesto=implode(",",$r->tipos_puesto);
                $tipo->save();
                
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
            savebitacora('Tipo de incidencia borrado '.$tipo->des_tipo_incidencia,"Incidencias","tipos_delete","OK");
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

    

}