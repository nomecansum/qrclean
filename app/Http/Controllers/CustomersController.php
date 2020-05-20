<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Auth;
use App\Helpers;
use App\Services\ClienteService;
use App\Services\APPApiService;
use Illuminate\Support\Str;
use App\Models\clientes;
use \Carbon\Carbon;

class CustomersController extends Controller
{
    //
    public function index()
    {
        $clientes = DB::table('clientes')
        ->select('clientes.cod_cliente','clientes.img_logo','clientes.nom_cliente','sistema.cod_sistema','supra.nom_cliente as emp_matriz','clientes.nom_contacto')
        ->selectRaw('(SELECT count(empleados.cod_empleado) FROM empleados WHERE empleados.cod_cliente = clientes.cod_cliente) as empleados')
        ->selectRaw('(SELECT count(centros.cod_centro) FROM centros WHERE centros.cod_cliente = clientes.cod_cliente) as centros')
        ->selectRaw('(SELECT count(departamentos.cod_departamento) FROM departamentos WHERE departamentos.cod_cliente = clientes.cod_cliente) as departamentos')
        ->selectRaw('(SELECT count(dispositivos.cod_dispositivo) FROM dispositivos WHERE dispositivos.cod_cliente = clientes.cod_cliente) as dispositivos')
        ->leftjoin('clientes as supra','clientes.cod_supracliente','supra.cod_cliente')
        ->join('sistema','sistema.cod_cliente','clientes.cod_cliente')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.cod_cliente',clientes());
            }
        })
        ->whereNull('clientes.fec_borrado')
        ->get();

        //$app_svc = new APPApiService;
        //$resp = $app_svc->update_incidencia([1027]);
        //dd($resp);
        //$resultado_app = $app_svc->update_empleado(5934);
        //print_r($resp);
        //die;
        //if($resultado_app["result"] == "ERROR"){
        //    throw new \Exception("Error en la provision del cliente en la APP: " . $resultado_app["msg"]);
        //}

        return view('customers.index',compact('clientes'));
    }

    public function edit($id)
    {
        validar_acceso_tabla($id,"clientes");
        $c = DB::table('clientes')
        ->whereNull('clientes.fec_borrado')
        ->where('cod_cliente',$id)->first();
    	return view('customers.create',compact('c'));
    }
    public function create()
    {
        $cod_sistema = DB::table('sistema')->where('COD_SISTEMA','>=',10000)->orderby('COD_SISTEMA','desc')->first()->COD_SISTEMA;//+1;
        if(empty($cod_sistema))
            $cod_sistema = 10000;
        else $cod_sistema++;
        return view('customers.create',compact('cod_sistema'));
    }
    public function save(Request $r)
    {
        $clsvc = new ClienteService;
        //Validarlos datos
        $clsvc->validar_request($r,'toast');

        DB::beginTransaction();
        try {
            //Insertar el cliente
            $c = $clsvc->insertar($r);
            //Le añadimos el codigo de sistema
            $sistema = $clsvc->insertar_sistema($r, $c);
            //Entidades por defecto: colectivo, departamento, centro, ciclo y horario
            $clsvc->insetar_datos_defecto($c);
            //damos permisos para este cliente a todos los usuarios del supracliente
            $clsvc->add_a_supracliente($c,$r);
            //Añadimos el cliente al usuario en curso
            if(!fullAccess()){
                $clsvc->add_a_usuario($c,Auth::user()->cod_usuario);
            }

			//Creamos la cuenta en el CRM
            $res = altaClienteCrm($r, $sistema);
			if($res["Error"] === false)
			{
				$fichacrm = "";
				$account = $res["body"]->accountid;
				//altaContactoCrm($r, $account);
			}

            savebitacora("Creado cliente ".$r->nom_cliente,null);

            DB::commit();
            return [
                'title' => trans('strings.business'),
                'message' => 'Creado cliente '.$r->nom_cliente.' Sistema:'.$sistema,
                //'url' => url('business')
                'url' => url('business/edit') . "/" . $c
            ];

        } catch (\Exception $e) {
            DB::rollback();
            error_log(json_encode($e->getMessage()));
            savebitacora("Error al crear cliente ".$r->nom_cliente. $e->getMessage(),null);

            return [
                'error' => trans('strings.business'),
                'message' => "Error al crear cliente ".mensaje_excepcion($e),
                //'url' => url('business')
            ];
        }
    }

    public function update(Request $r)
    {
        try {
            // Estos son los datos del cliente antes de actualizarlo
            $cliente_old=clientes::find($r->id);

            $clsvc = new ClienteService;
            //Validarlos datos
            $clsvc->validar_request($r,'toast');
            //Actualizar el cliente
            $c = $clsvc->actualizar($r);
            //Le añadimos el codigo de sistema
            $clsvc->insertar_sistema($r,$c);
            //damos permisos para este cliente a todos los usuarios del supracliente
            $clsvc->add_a_supracliente($c,$r);
            //Añadimos el cliente al usuario en curso
            if(!fullAccess()){
                $clsvc->add_a_usuario($c,Auth::user()->cod_usuario);
            }
            if($cliente_old->mca_appmovil != $r->mca_appmovil){  //Ha activado o desactivado la appmovil
                $clsvc->provisionar_appmpovil($c, $r, null);
            } else {
                $clsvc->provisionar_appmpovil($c, $r, 'U');
            }

            savebitacora("Actualizados datos de cliente ".$r->nom_cliente,$r->cod_cliente);

            return [
                'title' => trans('strings.business'),
                'message' => $r->nom_cliente.': '.trans('strings._configuration.business.updated'),
                'url' => url('business')
            ];
        } catch (\Exception $e) {
            DB::rollback();
            savebitacora("Error al actualizar cliente ".$r->nom_cliente. $e->getMessage(),null);

            return [
                'error' => trans('strings.business'),
                'message' => "Error al actualizar cliente ".mensaje_excepcion($e),
                //'url' => url('business')
            ];
        }
    }

    public function delete($id)
	{
        validar_acceso_tabla($id,"clientes");

        $clsvc = new ClienteService;
        $cliente = $clsvc->delete($id);

        savebitacora("Borrado de cliente [".$id."] completado con éxito", null);
		flash("Borrado de cliente " . DB::table('clientes')->where('cod_cliente', $id)->value('nom_cliente') . " con id " . $id . " completado con éxito")->success();
        return redirect()->back();
    }

    public function delete_completo($id)
    {
        //Baja en la app
        $clientes = clientes::find($id);
        if($clientes->mca_appmovil == "S")
        {
            $clientes->fec_borrado = Carbon::now();
            $clientes->mca_appmovil = "N";
            $clientes->completado = "N";

            $clientes->save();

            $app_svc = new APPApiService;
            $resultado_app = $app_svc->update_cliente([$id]);
            if($resultado_app["result"] == "ERROR"){
                throw new \Exception("Error en la provision del cliente en la APP: " . $resultado_app["msg"]);
            }
        }

        //Borramos en las tablas principales para que se disparen el resto de cascade
        //DB::table('centros')->where('cod_cliente',$id)->delete();
        //DB::table('empleados')->where('cod_cliente',$id)->delete();
        //DB::table('colectivos')->where('cod_cliente',$id)->delete();
        DB::table('users')->where('cod_cliente',$id)->delete();
        DB::table('clientes')->where('cod_cliente',$id)->delete();

        savebitacora("Borrado de cliente [".$id."] completado con éxito", null);
        flash("Borrado completo de cliente " . DB::table('clientes')->where('cod_cliente', $id)->value('nom_cliente') . " con id " . $id . " completado con éxito")->success();
        return redirect()->back();
    }

    public function gen_key()
	{
		return Str::random(64);
	}
}
