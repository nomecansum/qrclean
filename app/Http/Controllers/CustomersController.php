<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Auth;
use App\Helpers;
use App\services\ClienteService;
use App\services\APPApiService;
use Illuminate\Support\Str;
use App\Models\clientes;
use \Carbon\Carbon;
use App\Models\config_clientes;
use Session;

class CustomersController extends Controller
{
    //
    public function index()
    {
        $clientes = DB::table('clientes')
        ->select('clientes.id_cliente','clientes.img_logo','clientes.nom_cliente','clientes.nom_contacto')
        ->selectRaw('(SELECT count(puestos.id_puesto) FROM puestos WHERE puestos.id_cliente = clientes.id_cliente) as puestos')
        ->selectRaw('(SELECT count(edificios.id_edificio) FROM edificios WHERE edificios.id_cliente = clientes.id_cliente) as edificios')
        ->selectRaw('(SELECT count(plantas.id_planta) FROM plantas WHERE plantas.id_cliente = clientes.id_cliente) as plantas')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->whereNull('clientes.fec_borrado')
        ->get();
        return view('customers.index',compact('clientes'));
    }

    public function edit($id)
    {
        if ($id==0){
            $c=new clientes();
            $c->id_cliente=0;
        } else {
            $c = DB::table('clientes')
            ->whereNull('clientes.fec_borrado')
            ->where('id_cliente',$id)
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('clientes.id_cliente',clientes());
                }
            })
            ->first();
        }
        $config=DB::table('config_clientes')->where('id_cliente',$id)->first();
        if(!isset($config) && $id!=0){
            $config= new config_clientes;
            $config->id_cliente=$id;
            $config->save();
        }

    	return view('customers.create',compact('c','config'));
    }
    public function create()
    {
        $c=new clientes();
        $c->id_cliente=0;
        return view('customers.create',compact('c'));
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
            $config= new config_clientes;
            $config->id_cliente=$c;
            
            $r['min_hora_reservas']=time_to_dec($r->min_hora_reservas.':00','m');
            $r['max_hora_reservas']=time_to_dec($r->max_hora_reservas.':00','m');
            $config->update($r->all());
            $config->mca_restringir_usuarios_planta=$r->mca_restringir_usuarios_planta??'N';
            $config->mca_limpieza=$r->mca_limpieza??'N';
            $config->mca_permitir_anonimo=$r->mca_permitir_anonimo??'N';
            $config->mca_mostrar_nombre_usando=$r->mca_mostrar_nombre_usando??'N';
            $config->save();

            Session::put('CL',$config->toArray());

            savebitacora("Creado cliente ".$r->nom_cliente,'CustomerController','Save');

            DB::commit();
            return [
                'title' => 'Clientes',
                'message' => 'Creado cliente '.$r->nom_cliente,
                //'url' => url('business')
                'url' => url('clientes')
            ];

        } catch (\Exception $e) {
            DB::rollback();
            error_log(json_encode($e->getMessage()));
            savebitacora("Error al crear cliente ".$r->nom_cliente. $e->getMessage(),'CustomerController','Save');

            return [
                'error' => 'Clientes',
                'message' => "Error al crear cliente ".mensaje_excepcion($e),
                //'url' => url('clientes')
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
            //Config de cliente
            $config=config_clientes::findorfail($r->id);
            $r['min_hora_reservas']=time_to_dec($r->min_hora_reservas.':00','m');
            $r['max_hora_reservas']=time_to_dec($r->max_hora_reservas.':00','m');
            
            $config->update($r->all());
            $config->mca_restringir_usuarios_planta=$r->mca_restringir_usuarios_planta??'N';
            $config->mca_limpieza=$r->mca_limpieza??'N';
            $config->mca_permitir_anonimo=$r->mca_permitir_anonimo??'N';
            $config->mca_reserva_horas=$r->mca_reserva_horas??'N';
            $config->mca_mostrar_nombre_usando=$r->mca_mostrar_nombre_usando??'N';
            $config->mca_salas=$r->mca_salas??'N';
            $config->hora_liberar_puestos=$r->hora_liberar_puestos;
            $config->save();
            
            Session::put('CL',$config->toArray());

            savebitacora("Actualizados datos de cliente ".$r->nom_cliente,$r->id_cliente);

            

            return [
                'title' => 'Clientes',
                'message' => $r->nom_cliente.': Actualizado',
                'url' => url('clientes')
            ];
        } catch (\Exception $e) {
            DB::rollback();
            savebitacora("Error al actualizar cliente ".$r->nom_cliente. $e->getMessage(),'CustomerController','Update');

            return [
                'error' => 'Clientes',
                'message' => "Error al actualizar cliente ".mensaje_excepcion($e),
                //'url' => url('clientes')
            ];
        }
    }

    public function delete($id)
	{
        validar_acceso_tabla($id,"clientes");

        $clsvc = new ClienteService;
        $cliente = $clsvc->delete($id);

        savebitacora("Borrado de cliente [".$id."] completado con éxito", 'CustomerController','DeleteCompleto');
		flash("Borrado de cliente " . DB::table('clientes')->where('id_cliente', $id)->value('nom_cliente') . " con id " . $id . " completado con éxito")->success();
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
        DB::table('users')->where('id_cliente',$id)->delete();
        DB::table('clientes')->where('id_cliente',$id)->delete();

        savebitacora("Borrado de cliente [".$id."] completado con éxito", 'CustomerController','DeleteCompleto');
        flash("Borrado completo de cliente " . DB::table('clientes')->where('id_cliente', $id)->value('nom_cliente') . " con id " . $id . " completado con éxito")->success();
        return redirect()->back();
    }

    public function gen_key()
	{
		return Str::random(64);
	}
}

