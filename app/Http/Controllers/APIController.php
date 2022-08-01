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
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Spotdesking API",
 *      description="Spotdesking [Aka QRCLEAN] API Description",
 * )
 *
 */

class APIController extends Controller
{
    
       /**
        * @OA\Get(
        *     path="/api/test",
        *     tags={"test"},
        *     summary="Consulta de test de la API",
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
    public function test()
    {
         
        return response()->json([
        'result'=>'ok',
        'timestamp'=>Carbon::now(),
        'message' => 'Hello World!']);
    }

    
    /**
     * @OA\Get(
     *     path="/api/entidades",
     *     tags={"entidades"},
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
        
        return response()->json($respuesta);
    }

       /**
        * @OA\Post(
        *     path="/api/incidencias/list",
        *     tags={"get_incidents"},
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
        return response()->json([
            'result'=>'ok',
            'timestamp'=>Carbon::now(),
            'incidencias' => $incidencias]);
    }

       /**
        * @OA\Put(
        *     path="/api/incidencias",
        *     tags={"crear_incidencia"},
        *     summary="Crea una nueva incidencia",
        *     security={{"passport":{}}},
        *     
        * @OA\RequestBody(
        *         @OA\JsonContent(
        *                 @OA\Property(
        *                     description="Tiutulo de la incidencia",
        *                     property="des_incidencia",
        *                     type="string"
        *                 ),
        *                 @OA\Property(
        *                     description="Texto descriptivo  de la incidencia",
        *                     property="txt_incidencia",
        *                     type="string"
        *                 ),
        *                 @OA\Property(
        *                     description="Tipo de la incidencia",
        *                     property="id_tipo_incidencia",
        *                     type="number"
        *                 ),   
        *                 @OA\Property(
        *                     description="ID de tipo de incidencia",
        *                     property="tipoinc",
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
        *                     type="number"
        *                 ),
        *                 @OA\Property(
        *                     description="ID Puesto afectado por la incidencia",
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

        $r->request->add(['fec_apertura' => Carbon::now()]);
        $r->request->add(['id_usuario_apertura' => users::where('id_cliente',Auth::user()->id_cliente)
                                                        ->where(function($q) use($r){
                                                            $q->where('id',$r->id_usuario_apertura)
                                                              ->orWhere('id_externo',$r->id_usuario_apertura);
                                                        })->first()->id]);
        $r->request->add(['id_cliente' => Auth::user()->id_cliente]);

        $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
        return response()->json($respuesta);
    }

    /**
        * @OA\post(
        *     path="/api/incidencias/add_accion",
        *     tags={"add_accion"},
        *     summary="Añade una nueva accion a la  incidencia",
        *     security={{"passport":{}}},
        *     
        * @OA\RequestBody(
        *         @OA\JsonContent(
        *                 @OA\Property(
        *                     description="ID de incidencia",
        *                     property="id_incidencia",
        *                     type="number"
        *                 ),
        *                 @OA\Property(
        *                     description="Texto  de la accion",
        *                     property="des_accion",
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

            $r->request->add(['id_usuario' => users::where('id_cliente',Auth::user()->id_cliente)
                                                            ->where(function($q) use($r){
                                                                $q->where('id',$r->id_usuario_apertura)
                                                                  ->orWhere('id_externo',$r->id_usuario_apertura);
                                                            })->first()->id]);
    
            $respuesta=app('App\Http\Controllers\IncidenciasController')->add_accion($r);
            return response()->json($respuesta);
        }
}
