<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\niveles_acceso;
use App\Models\users;
use App\Models\plantas_usuario;
use Illuminate\Http\Request;
use Exception;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Auth;
use Session;
use Redirect;
use \Carbon\Carbon;

class UsersController extends Controller
{

    /**
     * Display a listing of the users.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $usersObjects = DB::table('users')
        ->leftjoin('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
        ->where(function($q){
            if (!isAdmin()) {
                $q->wherein('users.id_cliente',clientes());
            }
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('id')->toArray();
                $q->wherein('users.id',$usuarios_supervisados);
            }
        })
        ->get();

        $supervisores_perfil=[0];
        $supervisores_usuario=[0];

    
        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->get()->pluck('id_perfil')->unique();

        $supervisores_usuario=DB::table('permisos_usuarios')->where('id_seccion',$permiso)->get()->pluck('id_usuario')->unique();

        $supervisores=DB::table('users')
            ->where(function ($q) use($supervisores_perfil,$supervisores_usuario){
                $q->wherein('cod_nivel',$supervisores_perfil);
                $q->orwherein('id',$supervisores_usuario);
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('users.id_cliente',clientes());
                }
            })
            ->orderby('name')
            ->get();
            

        //$usersObjects = users::with('grupo','perfile')->paginate(25);

        return view('users.index', compact('usersObjects','supervisores'));
    }

    /**
     * Show the form for creating a new users.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        //$Grupos = Grupo::pluck('grupo','id_grupo')->all();
        $Perfiles = niveles_acceso::all();

        return view('users.create', compact('Perfiles'));
    }

    /**
     * Store a new users in the storage.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        //Vamos a comprbar el email, porque no puedo pasarlo por el validado
        if(DB::table('users')->where('email',$request->email)->exists()){
            flash('ERROR: El e-mail ya existe '.$request->email)->error();
            return back()->withInput();
        }
        $data = $this->getData($request);

        $img_usuario = "";
        try {
             if ($request->hasFile('img_usuario')) {
                $file = $request->file('img_usuario');
                $path = '/img/users/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
            }

            $data['img_usuario']=$img_usuario;
            $data["password"]=Hash::make($request->password);
            $data["cod_nivel"]=$request->cod_nivel;
            $data["nivel_acceso"]=DB::table('niveles_acceso')->where('cod_nivel',$data['cod_nivel'])->first()->val_nivel_acceso;

            users::create($data);
            savebitacora('Usuario '.$request->email. ' creado',"Usuarios","Store","OK");
            return [
                'title' => "Usuarios",
                'message' => 'Usuario '.$request->name. ' creado con exito',
                'url' => url('users')
            ];
        } catch (Exception $exception) {
            savebitacora('ERROR: Ocurrio un error creando el usuario '.$request->name.' '.$exception->getMessage() ,"Usuarios","Store","ERROR");
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error creando el usuario '.$request->name.' '.$exception->getMessage(),
                'url' => url('users')
            ];
            // flash('ERROR: Ocurrio un error creando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
        }
    }


    /**
     * Show the form for editing the specified users.
     *
     * @param int $id
     *
     * @return Illuminate\View\View
     */
    public function edit($id)
    {
        validar_acceso_tabla($id,"users");
        $users = users::findOrFail($id);
        $Perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->get();
        // dd($Perfiles);

        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->get()->pluck('id_perfil')->unique();

        $supervisores_usuario=DB::table('permisos_usuarios')->where('id_seccion',$permiso)->get()->pluck('id_usuario')->unique();

        $supervisores=DB::table('users')
            ->where(function ($q) use($supervisores_perfil,$supervisores_usuario){
                $q->wherein('cod_nivel',$supervisores_perfil);
                $q->orwherein('id',$supervisores_usuario);
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('users.id_cliente',clientes());
                }
            })
            ->orderby('name')
            ->get();

        $usuarios_supervisables = DB::table('users')
            ->leftjoin('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('users.id_cliente',clientes());
                }
            })
            ->where('users.id','<>',Auth::user()->id)
            ->get();


        $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',$id)->pluck('id')->toarray();

        return view('users.edit', compact('users','Perfiles','supervisores','usuarios_supervisados','usuarios_supervisables'));
    }

    /**
     * Update the specified users in the storage.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        validar_acceso_tabla($id,"users");
       
        $img_usuario = "";
        $data = $this->getData($request);
        
        try {
            if ($request->hasFile('img_usuario')) {
                $file = $request->file('img_usuario');
                $path = config('app.ruta_public').'/img/users/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
            }

            $data['img_usuario']=$img_usuario;

            
            if (isset($request->password)){
                $data["password"]=Hash::make($request->password);
            }
            $users = users::findOrFail($id);
            $data["email_verified_at"]=Carbon::now();
            $data["nivel_acceso"]=DB::table('niveles_acceso')->where('cod_nivel',$data['cod_nivel'])->first()->val_nivel_acceso;
            $data["id_usuario_supervisor"]=$r->id_usuario_supervisor??null;

            $users->update($data);

            //Añadimos los usuarios supervisados

            if(isset($request->lista_id)){
               users::where('id_usuario_supervisor',$id)->id_usuario_supervisor=null;
                foreach($request->lista_id as $id_supervisado){
                    DB::table('users')->where('id',$id_supervisado)->update([
                        'id_usuario_supervisor'=>$id
                    ]);
                }
            }
            savebitacora('Usuario '.$request->email. ' actualizado',"Usuarios","Update","OK");
            return [
                'title' => "Usuarios",
                'message' => 'Usuario '.$request->name. ' actualizado con exito',
                'url' => url('users')
            ];
            // flash('Usuario '.$request->name. 'actualizado con exito')->success();
            // return redirect()->route('users.users.index');
        } catch (Exception $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage() ,"Usuarios","Update","ERROR");
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    /**
     * Remove the specified users from the storage.
     *
     * @param int $id
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        validar_acceso_tabla($id,"users");
        try {
            $users = users::findOrFail($id);
            $users->delete();
            savebitacora('Usuario '.$users->email. ' borrado',"Usuarios","Destroy","OK");
            flash('Usuario '.$id. ' eliminado con exito')->success();
            return redirect()->route('users.index');
        } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error al eliminar el usuario '.$id.' '.$exception->getMessage())->error();
            savebitacora('ERROR: Ocurrio un error borrando el usuario '.$users->name.' '.$exception->getMessage() ,"Usuarios","destroy","ERROR");
            return back()->withInput();
                //->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.']);
        }
    }

    public function borrar_usuarios(Request $r)
    {
        try {
            $id_ok=[];
            foreach($r->lista_id as $id){
                validar_acceso_tabla($id,"users");
                $users = users::find($id);
                if($users){
                    $id_ok[]=$users->name;
                    $users->delete();
                    savebitacora('Usuario '.$users->email. ' borrado',"Usuarios","Destroy","OK");
                }
                
                
            }
        } catch (Exception $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error borrando los usuarios el usuario '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }

        return [
            'title' => "Usuarios",
            'message' => 'Usuarios '.implode(",",$id_ok). ' borrados',
            'url' => url('users')
        ];
    }

    /**
     * Get the request's data from the request.
     *
     * @param Illuminate\Http\Request\Request $request
     * @return array
     */
    protected function getData(Request $request)
    {
        $rules = [
            'name'=>'required',
            'collapse' => 'nullable|numeric|min:-2147483648|max:2147483647',
            'email' => 'required|string|min:1|max:255',
            'email_verified_at' => 'nullable|date_format:j/n/Y g:i A',
            'id_cliente' => 'required',
            'cod_nivel' => 'nullable',
            'remember_token' => 'nullable|string|min:0|max:100',
            'theme' => 'nullable|string|min:0|max:150',
            'val_timezone' => 'nullable|string|min:0|max:100',
            'nivel_acceso'=>'nullable',
            'token_acceso'=>'nullable'
        ];


        $data = $request->validate($rules);
        return $data;
    }

    public function plantas_usuario($id,$check){
        validar_acceso_tabla($id,'users');
        $user=users::findorfail($id);

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$user->id_cliente)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$user->id_cliente)
        ->get();

        $plantas_usuario=DB::table('plantas_usuario')->where('id_usuario',$id)->pluck('id_planta')->toarray();
        return view('users.selector_plantas',compact('puestos','edificios','plantas_usuario','id','check'));
    }

    public function addplanta($usuario,$planta){
        validar_acceso_tabla($usuario,'users');
        savebitacora('Permiso de reserva en planta '.$planta. ' para el usuario '.$usuario,"Usuarios","addplanta","OK");
        $pl=plantas_usuario::insert(['id_planta'=>$planta,'id_usuario'=>$usuario]);
        return [
            'title' => "Asociar planta a usuario",
            'message' => 'Planta asociada ',
            'id' =>$planta
        ];
    }

    public function delplanta($usuario,$planta){
        validar_acceso_tabla($usuario,'users');
        $pl=plantas_usuario::where(['id_planta'=>$planta,'id_usuario'=>$usuario])->delete();
        savebitacora('Quitado permiso de reserva en planta '.$planta. ' para el usuario '.$usuario,"Usuarios","addplanta","OK");
        return [
            'title' => "Asociar planta a usuario",
            'message' => 'Planta eliminada ',
            'id' =>$planta
        ];
    }

    public function setdefcamera($id){
        $u=users::find(Auth::user()->id);
        $u->def_camera=$id;
        $u->save();
        return;
    }

    public function pwd_hash($pwd){

        return Hash::make($pwd);
    }


    public function reback(){
        $this->authwith(session('back_id'));
        session(['back_id'=>null]);
        return redirect('/');
    }

    public function authwith($id)
    {
        $back_id=Auth::user()->id;
        //validar_acceso_tabla($id,"cug_usuarios");
        savebitacora("Relogin al usuario [".$id."]");
        Auth::logout();
        Auth::loginUsingId($id);

        //session(['lang' => Auth::user()->lang]);
        session(['back_id'=>$back_id]);
        //Permisos del usuario
        $permisos = DB::select(DB::raw("
        SELECT
                des_seccion,
                max(mca_read)as mca_read,
                max(mca_write) as mca_write,
                max(mca_create) as mca_create,
                max(mca_delete) as mca_delete
        FROM
            (SELECT
                `permisos_usuarios`.`id_seccion`,
                `permisos_usuarios`.`mca_read`,
                `permisos_usuarios`.`mca_write`,
                `permisos_usuarios`.`mca_create`,
                `permisos_usuarios`.`mca_delete`,
                `secciones`.`des_seccion`
            FROM
                `permisos_usuarios`
                INNER JOIN `secciones` ON (`permisos_usuarios`.`id_seccion` = `secciones`.`cod_seccion`)
            WHERE
                `cod_usuario` = ".auth()->user()->id."
            UNION
            SELECT
                `secciones_perfiles`.`id_seccion`,
                `secciones_perfiles`.`mca_read`,
                `secciones_perfiles`.`mca_write`,
                `secciones_perfiles`.`mca_create`,
                `secciones_perfiles`.`mca_delete`,
                `secciones`.`des_seccion`
            FROM
                `secciones_perfiles`
                INNER JOIN `secciones` ON (`secciones_perfiles`.`id_seccion` = `secciones`.`cod_seccion`)
            WHERE
                id_perfil=".auth()->user()->cod_nivel.") sq
        GROUP BY sq.des_seccion"));
        session(['P' => $permisos]);

        //Imagen del usuario
        $imagen=DB::table('users')
        ->select('img_usuario')
        ->where('id',Auth::user()->cod_usuario)
        ->value('img_usuario');
        session(['profile_pic'=>$imagen]);
        Session::forget('id_cliente');

        //Logo de cliente
        $cliente=DB::table('clientes')->where('id_cliente',Auth::user()->cod_cliente)->first();

        if(!empty($cliente->fec_borrado))
           return response()->json(["Tu empresa se encuentra dada de baja en el sistema, para cualquier pregunta contacte al 917 373 295 "],422);

        if($cliente && $cliente->img_logo!=''){
            session(['logo_cliente' => $cliente->img_logo]);
        }
        else Session::forget('logo_cliente');
    

        savebitacora("Cambio de sesion del usuario",null);
        return redirect('/');
    }

    public function asignar_plantas(Request $r){

        try {
            //Primero borramos lo que ya tuvieran si se ha escogido la opcion
            if($r->borrar_ant){
                DB::table('plantas_usuario')->wherein('id_usuario',$r->lista_id)->wherein('id_planta',$r->lista_plantas)->delete();
            }
            foreach($r->lista_id as $id){
                foreach($r->lista_plantas as $p){
                    DB::table('plantas_usuario')->insert([
                        "id_usuario"=>$id,
                        "id_planta"=>$p
                    ]);
                }
                
            }
            savebitacora('Asignado permiso de  de reserva en plantas '.implode(",",$r->lista_plantas). ' para los usuarios '.implode(",",$r->lista_id),"Usuarios","asignar_plantas","OK");

            return [
                'title' => "Usuarios",
                'message' => 'Asignado permiso de  de reserva en plantas '.implode(",",$r->lista_plantas). ' para los usuarios '.implode(",",$r->lista_id),
                //'url' => url('users')
            ];

        } catch (Exception $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error asignando permisos de planta a los usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function asignar_supervisor(Request $r){
        try {
            $supervisor=$r->supervisor==0?null:$r->supervisor;
            foreach($r->lista_id as $id){
                DB::table('users')->where('id',$id)->update([
                    "id_usuario_supervisor"=>$supervisor
                ]);
            }
            savebitacora('Asignado supervisor' .$r->supervisor.' para los usuarios '.implode(",",$r->lista_id),"Usuarios","asignar_supervisor","OK");

            return [
                'title' => "Usuarios",
                'message' =>'Asignado supervisor' .$r->supervisor.' para los usuarios '.implode(",",$r->lista_id),
                //'url' => url('users')
            ];

        } catch (Exception $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error asignando permisos de planta a los usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function puestos_supervisor($id){
        $usuario=users::find($id);

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$usuario->id_cliente)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$usuario->id_cliente)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->where('reservas.id_cliente',$usuario->id_cliente)
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',$id)
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',$usuario->cod_nivel)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',$usuario->cod_nivel)
            ->get();

        $puestos_check=DB::table('puestos_usuario_supervisor')->where('id_usuario',$id)->pluck('id_puesto')->toarray();
        $checks=1; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_supervisor/";

        
        return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
    }

    public function add_puesto_supervisor($id,$puesto,$accion){
        try {
            if($accion=="A"){
                DB::table('puestos_usuario_supervisor')->insert([
                    "id_puesto"=>$puesto,
                    "id_usuario"=>$id
                ]);
            } else {
                DB::table('puestos_usuario_supervisor')->where(["id_puesto"=>$puesto,"id_usuario"=>$id])->delete();
            }
            return [
                'result' => "OK"
            ];
        } catch (Exception $exception) {
            return [
                'result' => "ERROR",
                'error' => 'ERROR: Ocurrio un error asignando puestos al usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }
}

