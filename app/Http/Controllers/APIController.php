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
/**
 * @OA\OpenApi(
 *   @OA\Server(
 *      url="/api"
 *   ),
 *   @OA\Info(
 *      version="1.0.0",
 *      title="Spotdesking API",
 *      description="Spotdesking [Aka QRCLEAN] API Description",
 * ),
 *  * @OA\Tag(
 *    name="Autorizacion",
 *    description="Consultas relacionadas con la autorización de usuarios para el API",
 *),
 * @OA\Tag(
 *    name="Generales",
 *    description="Consulta de datos existentes en la plataforma",
 *),
 * @OA\Tag(
 *    name="Gestion de incidencias",
 *    description="Consulta de para la gestion de incidencias generica",
 *),
  * @OA\Tag(
 *    name="Salas",
 *    description="Consulta de para la integracion con gestion de salas",
 *),
 * )
 */



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

    ////////////////////////////////////////////////////////////////////


////////////////////FUNCIONES GENERALES//////////////////////////
       /**
        * @OA\Get(
        *     path="/test",
        *     tags={"Generales"},
        *     summary="Consulta de test de la API",
        *     security={{"passport":{}}},   
        *     @OA\Response(
        *          response=200,
        *          description="Successful operation",
        *          @OA\JsonContent()
        *       ),
        *     @OA\Response(
        *         response="default",
        *         description="Ha ocurrido un error."
        *     )
        * )
        */
    public function test()
    {
         
        return response()->json([
        'result'=>'ok',
        'timestamp'=>Carbon::now(),
        'message' => 'Hello World!']);
    }
    
    /**
     * @OA\Get(
     *     path="/entidades",
     *     tags={"Generales"},
     *     summary="Consulta de entidades del cliente",
     *     security={{"passport":{}}},
     *     
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function entidades()
    {
        
        $edificios=DB::table('edificios')
            ->select('id_edificio','des_edificio')
            ->where('edificios.id_cliente',Auth::user()->id_cliente)
            ->get();

        $plantas=DB::table('plantas')
            ->select('id_planta','des_planta','id_edificio','num_orden')
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
            ->select('id_tipo_puesto','des_tipo_puesto','val_icono','val_color')
            ->where(function($q){
                $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('puestos_tipos.mca_fijo','S');
                }
            })
        ->get();

        $tipos_incidencia=DB::table('incidencias_tipos')
            ->select('id_tipo_incidencia','des_tipo_incidencia','val_icono','val_color','list_tipo_puesto')
            ->where('incidencias_tipos.id_cliente',Auth::user()->id_cliente)
            ->get();

        foreach($tipos_incidencia as $ti){
            if($ti->list_tipo_puesto!=null){
                $ti->list_tipo_puesto=explode(',',$ti->list_tipo_puesto);
            }
        }

        $causas_cierre=DB::table('causas_cierre')
            ->select('id_causa_cierre','des_causa','val_icono','val_color')
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

    public function echo_test(Request $r)
    {
        echo("QUERY STRING:\r\n");
        echo($r->getQueryString());
        echo("\r\n\r\n");
        echo("HEADERS:\r\n");
        echo($r->headers);
        echo("\r\n\r\n");
        echo("BODY:\r\n");
        echo($r->getContent());
        echo("\r\n\r\n");
    }

///////////////////////FUNCIONES PARA INCIDENCIAS/////////////////
       /**
        * @OA\Post(
        *     path="/incidencias/list",
        *     tags={"Gestion de incidencias"},
        *     summary="Devuelve las incidencias de un cliente segun los filtros",
        *     security={{"passport":{}}},
        *     
        * @OA\RequestBody(
        *         @OA\JsonContent(
        *                 @OA\Property(
        *                     description="Fecha de inicio de la consulta",
        *                     property="fec_desde",
        *                     type="date"
        *                 ),
        *                 @OA\Property(
        *                     description="Fecha de fin de la consulta",
        *                     property="fec_hasta",
        *                     type="date"
        *                 ),   
        *                 @OA\Property(
        *                     description="IDs de tipo de incidencia",
        *                     property="tipoinc",
        *                     type="array",
        *                     @OA\Items(type="integer")
        *                 ),
        *                 @OA\Property(
        *                     description="IDS de estado de la incidencia",
        *                     property="id_estado",
        *                     type="array",
        *                     @OA\Items(type="integer")
        *                 ),
        *                 @OA\Property(
        *                     description="Puestos afectado por la incidencia",
        *                     property="puesto",
        *                     type="array",
        *                     @OA\Items(type="integer")
        *                 ),
        *             )
        *     ),
        *     @OA\Response(
        *          response=200,
        *          description="Successful operation",
        *          @OA\JsonContent()
        *       ),
        *     @OA\Response(
        *         response="default",
        *         description="Ha ocurrido un error."
        *     )
        * )
        */
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
            return [
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ];
        } 
    }

       /**
        * @OA\Put(
        *     path="/incidencias",
        *     tags={"Gestion de incidencias"},
        *     summary="Crea una nueva incidencia",
        *     security={{"passport":{}}},
        *     
        * @OA\RequestBody(
        *         @OA\JsonContent(
        *                 required={"des_incidencia","id_tipo_incidencia","id_puesto","id_usuario_apertura"},
        *                 @OA\Property(
        *                     description="Tiutulo de la incidencia",
        *                     property="des_incidencia",
        *                     type="string",
        *                     maxLength=500
        *                 ),
        *                 @OA\Property(
        *                     description="Texto descriptivo  de la incidencia",
        *                     property="txt_incidencia",
        *                     type="string",
        *                     maxLength=65535
        *                 ),
        *                 @OA\Property(
        *                     description="ID externo",
        *                     property="id_incidencia_externo",
        *                     type="number",
        *                     maxLength=100
        *                 ),
        *                 @OA\Property(
        *                     description="Tipo de la incidencia",
        *                     property="id_tipo_incidencia",
        *                     type="number"
        *                 ),   
        *                 @OA\Property(
        *                     description="ID Estado de la incidencia",
        *                     property="id_estado",
        *                     type="number"
        *                 ),
        *                 @OA\Property(
        *                     description="ID Puesto afectado por la incidencia",
        *                     property="id_puesto",
        *                     type="string"
        *                 ),
        *                 @OA\Property(
        *                     description="ID de usuario que abre la incidencia",
        *                     property="id_usuario_apertura",
        *                     type="string"
        *                 ),
        *             )
        *     ),
        *     @OA\Response(
        *          response=200,
        *          description="Successful operation",
        *          @OA\JsonContent()
        *       ),
        *     @OA\Response(
        *         response="default",
        *         description="Ha ocurrido un error."
        *     )
        * )
        */
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
            return [
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ];
        } 
    }

      /**
        * @OA\post(
        *     path="/incidencias/add_accion",
        *     tags={"Gestion de incidencias"},
        *     summary="Añade una nueva accion a la  incidencia",
        *     security={{"passport":{}}},
        *     
        * @OA\RequestBody(
        *         @OA\JsonContent(
        *                 required={"id_incidencia","id_usuario","des_accion"},
        *                 @OA\Property(
        *                     description="ID de incidencia",
        *                     property="id_incidencia",
        *                     type="number"
        *                 ),
        *                 @OA\Property(
        *                     description="Texto  de la accion",
        *                     property="des_accion",
        *                     maxLength=2000,
        *                     type="string"
        *                 ),
        *                 @OA\Property(
        *                     description="ID de usuario que realiza la accion",
        *                     property="id_usuario",
        *                     type="string"
        *                 )
        *             )
        *     ),
        *     @OA\Response(
        *          response=200,
        *          description="Successful operation",
        *          @OA\JsonContent()
        *       ),
        *     @OA\Response(
        *         response="default",
        *         description="Ha ocurrido un error."
        *     )
        * )
        */
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
            return [
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ];
        } 
        
    }

        /**
            * @OA\post(
            *     path="/incidencias/cerrar",
            *     tags={"Gestion de incidencias"},
            *     summary="Cierra una incidencia",
            *     security={{"passport":{}}},
            *     
            * @OA\RequestBody(
            *         @OA\JsonContent(
            *                 required={"id_incidencia","id_usuario"},
            *                 @OA\Property(
            *                     description="ID de incidencia",
            *                     property="id_incidencia",
            *                     type="number"
            *                 ),
            *                 @OA\Property(
            *                     description="Comentario de cierre de la incidencia",
            *                     property="comentario_cierre",
            *                     type="string",
            *                     maxLength=65535
            *                 ),
            *                 @OA\Property(
            *                     description="ID de usuario que realiza la accion",
            *                     property="id_usuario",
            *                     type="string"
            *                 ),
            *                 @OA\Property(
            *                     description="Identificador de causa de cierre",
            *                     property="id_causa_cierre",
            *                     type="number"
            *                 )
            *             )
            *     ),
            *     @OA\Response(
            *          response=200,
            *          description="Successful operation",
            *          @OA\JsonContent()
            *       ),
            *     @OA\Response(
            *         response="default",
            *         description="Ha ocurrido un error."
            *     )
            * )
            */
    public function cerrar_ticket(Request $r){
        try{
            $r->request->add(['id_usuario' => $this->get_usuario($r)]);
            $r->request->add(['id_incidencia' => $this->get_incidencia($r)]);
            $r->request->add(['procedencia' => "api"]);

            if(!isset($r->id_causa_cierre)){
                $causa=causas_cierre::where('id_cliente',Auth::user()->id_cliente)
                    ->orderby('mca_default','desc')
                    ->orderby('id_causa_cierre','asc')
                    ->first()->id_causa_cierre;  
                $r->request->add(['id_causa_cierre' => $causa]);
            }
            $respuesta=app('App\Http\Controllers\IncidenciasController')->cerrar($r);
            savebitacora('Cerrar incidencia incidencia '.json_encode($r->all()),"API","cerrar_ticket","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR cerrando incidencia '.json_encode($r->all()),"API","cerrar_ticket","ERROR");
            return [
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error cerrando incidencia '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ];
        } 
    }

       /**
        * @OA\post(
        *     path="/incidencias/reabrir",
        *     tags={"Gestion de incidencias"},
        *     summary="Reabre una incidencia cerrada",
        *     security={{"passport":{}}},
        *     
        * @OA\RequestBody(
        *         @OA\JsonContent(
        *                 required={"id_incidencia","id_usuario"},
        *                 @OA\Property(
        *                     description="ID de incidencia",
        *                     property="id_incidencia",
        *                     type="number"
        *                 ),
        *                 @OA\Property(
        *                     description="ID de usuario que realiza la accion",
        *                     property="id_usuario",
        *                     type="string"
        *                 )
        *             )
        *     ),
        *     @OA\Response(
        *          response=200,
        *          description="Successful operation",
        *          @OA\JsonContent()
        *       ),
        *     @OA\Response(
        *         response="default",
        *         description="Ha ocurrido un error."
        *     )
        * )
        */
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
            return [
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error reabriendo incidencia '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ];
        } 
    }


///////////////////////FUNCIONES PARA SALAS///////////////////////
}
