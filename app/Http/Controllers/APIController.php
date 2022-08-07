<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Auth;
use Carbon\Carbon;
use App\User;
use OpenApi\Annotations as OA;
use App\Http\Controllers\IncidenciasController;
use App\Models\users;
use App\Models\causas_cierre;
use App\Models\incidencias;
use App\Models\puestos;
use App\Models\salas;
use App\Models\clientes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class APIController extends Controller
{
    //////////////////FUNCIONES AUXILIARES/////////////////////////////
    public static function get_usuario($r){
        try{
            $result=users::where('id_cliente',Auth::user()->id_cliente)
            ->where(function($q) use($r){
                $q->where('id',$r->id_usuario_apertura)
                ->orWhere('id_externo',$r->id_usuario_apertura);
            })->first()->id;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El usuario '.$r->id_usuario_apertura.' no existe');
        }
       

    }

    public static function get_incidencia($r){
        try{
            $result=incidencias::where('id_cliente',Auth::user()->id_cliente)
                ->where(function($q) use($r){
                    $q->where('id_incidencia',$r->id_incidencia)
                    ->orWhere('id_externo',$r->id_incidencia);
                })
                ->first()->id_incidencia;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('La incidencia '.$r->id_incidencia.' no existe');
        }
    }

    public static function get_puesto($r){
        try{
            $result=puestos::where('id_cliente',Auth::user()->id_cliente)
                ->where(function($q) use($r){
                    $q->where('id_puesto',$r->id_puesto)
                    ->orWhere('cod_puesto',$r->id_puesto);
                })
                ->first()->id_puesto;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El puesto '.$r->id_puesto.' no existe');
        }
    }

    public static function get_causa_cierre($r){
        try{
            $result=causas_cierre::where('id_cliente',Auth::user()->id_cliente)
                ->where(function($q) use($r){
                    $q->where('id_causa_cierre',$r->id_causa_cierre)
                    ->orWhere('id_externo',$r->id_causa_cierre);
                })
                ->first()->id_causa_cierre;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('La causa de cierre '.$r->id_causa_cierre.' no existe');
        }
    }

    public static function respuesta_error($texto,$codigo){
        return response()->json([
            'result'=>'error',
            'error' => $texto,
            'timestamp'=>Carbon::now(),
        ])->setStatusCode($codigo);
    }

    public static function enviar_request_salas($metodo,$accion,$param,$body){
        $response=Http::withOptions(['verify' => false])
            ->withHeaders(['Accept' => 'application/json', 'Content-Type' => 'application/json','Authorization'=>config('app.token_api_salas')])
            ->withbody($body,'application/json')
            ->$metodo(config('app.url_base_api_salas').$accion);
        
        if($response->status()!=200){
            Log::error('Error en la respuesta de la API de salas: '.$response->body());
            return APIController::respuesta_error('Error en la respuesta de la API de salas: '.$response->body(),$response->status());
        } 
        return [
            "body"=>$response->body(),
            "status"=>$response->status()
           ];
    }

    ////////////////////////////////////////////////////////////////////


////////////////////FUNCIONES GENERALES//////////////////////////

    public function test(){
         
        return response()->json([
        'result'=>'ok',
        'timestamp'=>Carbon::now(),
        'message' => 'Hello World!']);
    }
    
    public function entidades(){
        
        $edificios=DB::table('edificios')
            ->select('id_edificio','des_edificio','abreviatura')
            ->where('edificios.id_cliente',Auth::user()->id_cliente)
            ->get();

        $plantas=DB::table('plantas')
            ->select('id_planta','des_planta','id_edificio','num_orden','abreviatura')
            ->where('plantas.id_cliente',Auth::user()->id_cliente)
            ->get();
        
        
        $puestos=DB::table('puestos')
            ->select('puestos.id_planta','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','puestos.id_estado','puestos.id_tipo_puesto','puestos.mca_incidencia','estados_puestos.des_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->where('puestos.id_cliente',Auth::user()->id_cliente)
            ->get();

        foreach($edificios as $ed){
            $ed->plantas=$plantas->where('id_edificio',$ed->id_edificio);
            foreach($ed->plantas as $pl){
                $pl->puestos=$puestos->where('id_planta',$pl->id_planta);
            }
        }

        $tipos_puesto = DB::table('puestos_tipos')
            ->select('id_tipo_puesto','des_tipo_puesto','val_icono','val_color','abreviatura')
            ->where(function($q){
                $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('puestos_tipos.mca_fijo','S');
                }
            })
        ->get();

        $tipos_incidencia=DB::table('incidencias_tipos')
            ->select('id_tipo_incidencia','des_tipo_incidencia','val_icono','val_color','list_tipo_puesto','id_tipo_externo','id_tipo_salas','val_responsable')
            ->where('incidencias_tipos.id_cliente',Auth::user()->id_cliente)
            ->get();

        foreach($tipos_incidencia as $ti){
            if($ti->list_tipo_puesto!=null){
                $ti->list_tipo_puesto=explode(',',$ti->list_tipo_puesto);
            }
        }

        $causas_cierre=DB::table('causas_cierre')
            ->select('id_causa_cierre','des_causa','val_icono','val_color','mca_default','id_externo')
            ->where('causas_cierre.id_cliente',Auth::user()->id_cliente)
            ->get();

        $estados_incidencia=DB::table('estados_incidencias')
            ->select('id_estado','des_estado','val_icono','val_color','mca_cierre')
            ->where('estados_incidencias.id_cliente',Auth::user()->id_cliente)
            ->get();
       
       $respuesta=array(
            'result'=>'ok',
            'timestamp'=>Carbon::now(),
            'edificios'=>$edificios,
            'tipos_puesto'=>$tipos_puesto,
            'tipos_incidencia'=>$tipos_incidencia,
            'causas_cierre'=>$causas_cierre,
            'estados_incidencia'=>$estados_incidencia,
        );
       
        savebitacora('Solicitud de listado de entidades ',"API","entidades","OK"); 
        return response()->json($respuesta);
    }

    public function echo_test(Request $r){
        $data=[
            "QUERY_STRING"=>$r->getQueryString(),
            "HEADERS"=>$r->headers->all(),
            "BODY"=>$r->getContent(),
        ];
        return response()->json($data);

    }

    public function process_test(Request $r){
        $data=[
            "id_incidencia"=>"FF2202",
            "url_detalle"=>"http://localhost:8080/incidencias/FF2202"
        ];
        return response()->json($data);
    }

///////////////////////FUNCIONES PARA INCIDENCIAS/////////////////

    public function get_incidents(Request $r){

        try{
            //Fechas
            $f1=(isset($r->fec_desde))?Carbon::parse($r->fec_desde):Carbon::now()->startOfMonth();
            $f2=(isset($r->fec_hasta))?Carbon::parse($r->fec_hasta):Carbon::now()->endOfMonth();
            $fechas=$f1->format('d/m/Y').' - '.$f2->format('d/m/Y');
            $r->request->add(['fechas' => $fechas]);
            $r->request->add(['ac' => 'B']);


            $respuesta=app('App\Http\Controllers\IncidenciasController')->search($r);
            //dd($respuesta);
            $incidencias = $respuesta->map(function ($item, $key) {
                $acciones=DB::table('incidencias_acciones')
                    ->select('id_accion','des_accion','fec_accion','id_usuario','mca_resuelve','users.id_externo as id_usuario_ext')
                    ->join('users','users.id','incidencias_acciones.id_usuario')
                    ->where('id_incidencia',$item->id_incidencia)
                    ->get();
                return [
                    'id_incidencia' => $item->id_incidencia,
                    'id_externo' => $item->id_externo,
                    'id_incidencia_salas' => $item->id_incidencia_salas,
                    'des_incidencia' => $item->des_incidencia,
                    'txt_incidencia' => $item->txt_incidencia,
                    'fec_apertura' => $item->fec_apertura,
                    'fec_cierre' => $item->fec_cierre,
                    'id_tipo_incidencia' => $item->id_tipo_incidencia,
                    'id_puesto' => $item->id_puesto,
                    'id_causa_cierre' => $item->id_causa_cierre,
                    'comentario_cierre' => $item->comentario_cierre,
                    'id_estado' => $item->id_estado,
                    'id_usuario_apertura' => $item->id_usuario_apertura,
                    'id_usuario_ext' => users::find($item->id_usuario_apertura)->id_externo,
                    'acciones' => $acciones,
                ];
            });
            savebitacora('Solicitud de listado de incidencias '.json_encode($r->all()),"API","get_incidents","OK"); 
            return response()->json([
                'result'=>'ok',
                'timestamp'=>Carbon::now(),
                'incidencias' => $incidencias]);
        }catch (\Throwable $e) {
            savebitacora('ERROR Solicitud de listado de incidencias '.json_encode($r->all()),"API","get_incidents","ERROR"); 
            return $this->respuesta_error('ERROR Solicitud de listado de incidencias '.$e->getMessage(),400);
        } 
    }

    public function crear_incidencia(Request $r){

        try{
            $r->request->add(['fec_apertura' => Carbon::now()]);
            $r->request->add(['id_usuario_apertura' => $this->get_usuario($r)]);
            $r->request->add(['id_cliente' => Auth::user()->id_cliente]);
            $r->request->add(['id_puesto' => $this->get_puesto($r)]);
            $r->request->add(['procedencia' => "api"]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
            savebitacora('Crear de incidencia '.json_encode($r->all()),"API","crear_incidencia","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Creacion de incidencia '.json_encode($r->all()),"API","crear_incidencia","ERROR");
            return $this->respuesta_error('ERROR creando incidencia '.$e->getMessage(),400);
        } 
    }

    public function add_accion(Request $r){
        try{
            $r->request->add(['id_usuario' => $this->get_usuario($r)]);
            $r->request->add(['id_incidencia' => $this->get_incidencia($r)]);
            $r->request->add(['procedencia' => "api"]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->add_accion($r);
            savebitacora('Añadir accion en incidencia '.json_encode($r->all()),"API","add_accion","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR añadiendo accion a incidencia '.json_encode($r->all()),"API","add_accion","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),400);
        } 
        
    }

    public function cerrar_ticket(Request $r){
        try{
            $r->request->add(['id_usuario' => $this->get_usuario($r)]);
            $r->request->add(['id_incidencia' => $this->get_incidencia($r)]);
            $r->request->add(['id_causa_cierre' => $this->get_causa_cierre($r)]);
            $r->request->add(['procedencia' => "api"]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->cerrar($r);
            savebitacora('Cerrar incidencia incidencia '.json_encode($r->all()),"API","cerrar_ticket","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR cerrando incidencia '.json_encode($r->all()),"API","cerrar_ticket","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error cerrando incidencia '.$e->getMessage(),400);
        } 
    }

    public function reabrir_ticket(Request $r){
        try{
            $r->request->add(['id_usuario' => $this->get_usuario($r)]);
            $r->request->add(['id_incidencia' => $this->get_incidencia($r)]);
            $r->request->add(['procedencia' => "api"]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->reabrir($r);
            savebitacora('Reabrir incidencia incidencia '.json_encode($r->all()),"API","reabrir_ticket","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR reabriendo incidencia '.json_encode($r->all()),"API","reabrir_ticket","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error reabriendo incidencia '.$e->getMessage(),400);
        } 
    }
    
///////////////////////FUNCIONES PARA SALAS///////////////////////

    public function solicitud_sincro_datos(Request $r,$fecha,$cliente){
        try{
            //Buscamos el cliente
            $cliente=clientes::where('id_externo',$cliente)->first();
            if(!isset($cliente)){
                return $this->respuesta_error('ERROR: El cliente no existe',400);
            }
            $r->request->add(['procedencia' => "salas"]);
            //Codigo para la resincronizacion de incidencias
            $url="get_estructura_incidencias_empresa_desde_fecha/".$fecha;
            $respuesta=$this->enviar_request_salas("GET",$url,"","");
            $respuesta=json_decode($respuesta['body']);

            //Sincronizamos las salas
            $salas_spotlinker=json_decode($respuesta->a_salas);
            foreach($salas_spotlinker->salas as $sala){
                $esta=puestos::where('id_cliente',$cliente)
                        ->join('salas','salas.id_puesto','puestos.id_puesto')
                        ->where('des_puesto',$sala->nombre)
                        ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_sala'))
                        ->first();
                if(!isset($esta)){
                    $sala_qrclean=salas::where('id_puesto',$esta->id_puesto)->first();
                    $sala_qrclean->id_externo=$sala->id;
                    $sala_qrclean->save();
                }
            }
            //fin
            
            savebitacora('Solicitud de resincronizacion de estructuras salas'.json_encode($r->all()),"API","solicitud_sincro","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('Error en sincronizacion  de estructuras salas '.json_encode($r->all()),"API","reabrir_ticket","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error en el proceso '.$e->getMessage(),400);
            
        } 
    }

    public function crear_incidencia_salas(Request $r){
        try{
            $r->request->add(['fec_apertura' => Carbon::parse($r->fecha)]);
            $r->request->add(['id_usuario_apertura'=>DB::table('users')->where('name','Spotlinker Salas')->first()->id_usuario??0]);

            $r->request->add(['id_puesto' => salas::where('id_externo',$r->sala_id)->first()->id_puesto??0]);
            $r->request->add(['procedencia' => "salas"]);
            $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
            savebitacora('Crear de incidencia '.json_encode($r->all()),"API","crear_incidencia","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Creacion de incidencia '.json_encode($r->all()),"API","crear_incidencia","ERROR");
            return response()->json([
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ])->setStatusCode(400);
        } 
    }

    public function add_accion_salas(Request $r){
        try{
            $r->request->add(['fec_apertura' => Carbon::parse($r->fecha)]);
            $r->request->add(['id_usuario_apertura'=>DB::table('users')->where('name','Spotlinker Salas')->first()->id_usuario??0]);

            $r->request->add(['id_puesto' => salas::where('id_externo',$r->sala_id)->first()->id_puesto??0]);
            $r->request->add(['procedencia' => "salas"]);
            $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
            savebitacora('Crear de incidencia '.json_encode($r->all()),"API","crear_incidencia","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Creacion de incidencia '.json_encode($r->all()),"API","crear_incidencia","ERROR");
            return response()->json([
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ])->setStatusCode(400);
        } 
    }

    public function request_sincro($fecha,$cliente){
        try{
            $r->request->add(['fec_apertura' => Carbon::parse($r->fecha)]);
            $r->request->add(['id_usuario_apertura'=>DB::table('users')->where('name','Spotlinker Salas')->first()->id_usuario??0]);

            $r->request->add(['id_puesto' => salas::where('id_externo',$r->sala_id)->first()->id_puesto??0]);
            $r->request->add(['procedencia' => "salas"]);
            $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
            savebitacora('Crear de incidencia '.json_encode($r->all()),"API","crear_incidencia","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Creacion de incidencia '.json_encode($r->all()),"API","crear_incidencia","ERROR");
            return response()->json([
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ])->setStatusCode(400);
        } 
    }

    public function sincro_incidencias_salas($fecha,$cliente){
        try{
            $r->request->add(['fec_apertura' => Carbon::parse($r->fecha)]);
            $r->request->add(['id_usuario_apertura'=>DB::table('users')->where('name','Spotlinker Salas')->first()->id_usuario??0]);

            $r->request->add(['id_puesto' => salas::where('id_externo',$r->sala_id)->first()->id_puesto??0]);
            $r->request->add(['procedencia' => "salas"]);
            $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
            savebitacora('Crear de incidencia '.json_encode($r->all()),"API","crear_incidencia","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Creacion de incidencia '.json_encode($r->all()),"API","crear_incidencia","ERROR");
            return response()->json([
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ])->setStatusCode(400);
        } 
    }
}
